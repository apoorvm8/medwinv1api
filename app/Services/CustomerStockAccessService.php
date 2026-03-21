<?php

namespace App\Services;

use App\Models\CustomerStockAccess;
use App\Models\FolderMaster;
use App\Traits\HashIds;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CustomerStockAccessService
{
   use HashIds;

   /**
    * Parse uploaded stock CSV and load into stock_view_data.
    * Deletes existing rows for the same Outlet_Id, then bulk inserts.
    * Streams the CSV (chunked read/insert) to keep memory low on 1GB server.
    *
    * @param \Illuminate\Http\UploadedFile $uploadedFile
    * @return void
    */
   public function importStockFromCsv($uploadedFile)
   {
      $path = $uploadedFile->getRealPath();
      $handle = fopen($path, 'r');
      if (!$handle) {
         return;
      }

      // Skip header
      $header = fgetcsv($handle);
      if ($header === false) {
         fclose($handle);
         return;
      }

      $outletIdToReplace = null;
      $chunk = [];
      $chunkSize = 500;

      while (($cells = fgetcsv($handle)) !== false) {
         if (count($cells) < 15) {
            continue;
         }

         $division = trim($cells[0] ?? '');
         [$outletId, $outletName] = $this->splitDivision($division);
         if ($outletIdToReplace === null && $outletId !== null) {
            $outletIdToReplace = $outletId;
            // Delete existing data for this outlet before first insert
            DB::table('stock_view_data')->where('Outlet_Id', $outletIdToReplace)->delete();
         }

         $dateOfSending = $this->parseDate(trim($cells[13] ?? ''));

         $chunk[] = [
            'Division'       => $division ?: null,
            'Outlet_Id'      => $outletId,
            'Outlet_Name'    => $outletName,
            'Address1'      => trim($cells[1] ?? '') ?: null,
            'Address2'       => trim($cells[2] ?? '') ?: null,
            'Company_name'   => trim($cells[3] ?? '') ?: null,
            'Item_code'      => $this->intOrNull($cells[4] ?? null),
            'Item_Name'      => trim($cells[5] ?? '') ?: null,
            'PackDesc'       => trim($cells[6] ?? '') ?: null,
            'PackSize'       => $this->decimalOrNull($cells[7] ?? null),
            'MRP'            => $this->decimalOrNull($cells[8] ?? null),
            'SaleScm1'       => $this->decimalOrNull($cells[9] ?? null),
            'Salescm2'       => $this->decimalOrNull($cells[10] ?? null),
            'BatchQty'       => $this->intOrNull($cells[11] ?? null),
            'GSTPER'         => $this->decimalOrNull($cells[12] ?? null),
            'Dateofsending'  => $dateOfSending,
            'Timeofsending'  => trim($cells[14] ?? '') ?: null,
         ];

         if (count($chunk) >= $chunkSize) {
            DB::table('stock_view_data')->insert($chunk);
            $chunk = [];
         }
      }

      fclose($handle);

      if (!empty($chunk)) {
         DB::table('stock_view_data')->insert($chunk);
      }
   }

   /**
    * Per outlet: one CSV per acctno, filename must start with c{acctno}_ or {acctno}_.
    * Compare S3 Last-Modified with customer_stock_access.last_modified_at; skip import if not newer.
    *
    * @return array{success: bool, message: string, skipped?: bool}
    */
   public function refreshStockFromS3(int $acctno): array
   {
      $stockAccess = CustomerStockAccess::where('acctno', $acctno)->where('active', 1)->first();
      if (!$stockAccess) {
         return ['success' => false, 'message' => 'Stock access is not enabled for this outlet.'];
      }

      $folder = FolderMaster::find($stockAccess->folder_id);
      if (!$folder) {
         return ['success' => false, 'message' => 'Stock folder not found.'];
      }

      $csvFiles = FolderMaster::where('parent_id', $folder->id)
         ->where('resource_type', FolderMaster::RESOURCE_TYPE_FILE)
         ->orderBy('name')
         ->get()
         ->filter(function ($f) use ($acctno) {
            return $f->name
               && str_ends_with(strtolower($f->name), '.csv')
               && $this->isStockCsvFilenameForOutlet($f->name, $acctno);
         })
         ->values();

      if ($csvFiles->isEmpty()) {
         return ['success' => false, 'message' => 'No stock CSV file found for outlet '.$acctno.'.'];
      }

      if ($csvFiles->count() > 1) {
         return ['success' => false, 'message' => 'Multiple CSV files match outlet '.$acctno.'; expected exactly one.'];
      }

      /** @var FolderMaster $file */
      $file = $csvFiles->first();
      if (!$file->path) {
         return ['success' => false, 'message' => 'Stock CSV path is missing for outlet '.$acctno.'.'];
      }

      $disk = Storage::disk('s3');
      if (!$disk->exists($file->path)) {
         return ['success' => false, 'message' => 'Stock CSV is missing on storage for outlet '.$acctno.'.'];
      }

      $s3Lm = $disk->lastModified($file->path);

      $dbLm = $stockAccess->last_modified_at ? $stockAccess->last_modified_at->getTimestamp() : null;
      if ($dbLm !== null && $s3Lm <= $dbLm) {
         return [
            'success' => true,
            'skipped' => true,
            'message' => 'Already latest stock updated.',
         ];
      }

      try {
         $localPath = tempnam(sys_get_temp_dir(), 'stock_csv_');
         if ($localPath === false) {
            throw new \RuntimeException('Could not create a temporary file for import.');
         }

         try {
            $stream = $disk->readStream($file->path);
            if ($stream === false) {
               throw new \RuntimeException('Could not read file from storage: '.$file->name);
            }
            $target = fopen($localPath, 'wb');
            if ($target === false) {
               if (is_resource($stream)) {
                  fclose($stream);
               }
               throw new \RuntimeException('Could not open temporary file for writing.');
            }
            stream_copy_to_stream($stream, $target);
            fclose($target);
            if (is_resource($stream)) {
               fclose($stream);
            }

            $uploaded = new UploadedFile($localPath, $file->name, 'text/csv', null, true);
            $this->importStockFromCsv($uploaded);
         } finally {
            if (is_file($localPath)) {
               @unlink($localPath);
            }
         }

         $stockAccess->last_modified_at = Carbon::createFromTimestamp($s3Lm);
         $stockAccess->save();
      } catch (Throwable $e) {
         report($e);

         return [
            'success' => false,
            'message' => 'Import failed: '.$e->getMessage(),
         ];
      }

      return [
         'success' => true,
         'skipped' => false,
         'message' => 'Stock data refreshed.',
      ];
   }

   /**
    * Run refreshStockFromS3 for every outlet account (main + linked).
    *
    * @param  array<int|string>  $acctnos
    * @return array{
    *   success: bool,
    *   message: string,
    *   imported_count: int,
    *   skipped_count: int,
    *   failed_count: int,
    *   outlets: list<array{acctno: int, status: 'imported'|'skipped'|'failed', message: string}>
    * }
    */
   public function refreshStockForAllOutlets(array $acctnos): array
   {
      $acctnos = array_values(array_unique(array_map('intval', $acctnos)));

      $outlets = [];
      $importedCount = 0;
      $skippedCount = 0;
      $failedCount = 0;

      foreach ($acctnos as $acctno) {
         $r = $this->refreshStockFromS3($acctno);

         if (! $r['success']) {
            $failedCount++;
            $outlets[] = [
               'acctno' => $acctno,
               'status' => 'failed',
               'message' => $r['message'],
            ];

            continue;
         }

         if (! empty($r['skipped'])) {
            $skippedCount++;
            $outlets[] = [
               'acctno' => $acctno,
               'status' => 'skipped',
               'message' => 'Already latest stock updated.',
            ];
         } else {
            $importedCount++;
            $outlets[] = [
               'acctno' => $acctno,
               'status' => 'imported',
               'message' => 'Stock CSV was downloaded from cloud and imported successfully.',
            ];
         }
      }

      $success = $failedCount === 0;

      $message = $this->buildStockRefreshSummaryMessage($importedCount, $skippedCount, $failedCount);

      return [
         'success' => $success,
         'message' => $message,
         'imported_count' => $importedCount,
         'skipped_count' => $skippedCount,
         'failed_count' => $failedCount,
         'outlets' => $outlets,
      ];
   }

   private function buildStockRefreshSummaryMessage(int $imported, int $skipped, int $failed): string
   {
      $parts = [];
      if ($imported > 0) {
         $phrase = $imported.' outlet'.($imported === 1 ? '' : 's').' updated from cloud';
         if ($skipped === 0 && $failed === 0) {
            $phrase = 'Only '.$phrase;
         }
         $parts[] = $phrase;
      }
      if ($skipped > 0) {
         $parts[] = $skipped.' outlet'.($skipped === 1 ? '' : 's').' already had the latest data (skipped)';
      }
      if ($failed > 0) {
         $parts[] = $failed.' outlet'.($failed === 1 ? '' : 's').' could not be updated';
      }

      if ($parts === []) {
         return 'No outlets were processed.';
      }

      return implode('; ', $parts).'.';
   }

   /**
    * Stock CSV names from the desktop use the outlet id at the start of the basename, e.g. c2642_NAME.csv or 2642_NAME.csv.
    */
   private function isStockCsvFilenameForOutlet(string $filename, int $acctno): bool
   {
      $base = pathinfo($filename, PATHINFO_FILENAME);

      return (bool) preg_match('/^(c)?' . preg_quote((string) $acctno, '/') . '_/i', $base);
   }

   /**
    * Split "2642_ALL INDIA CHEMIST" into [2642, "ALL INDIA CHEMIST"].
    */
   private function splitDivision(string $division): array
   {
      if ($division === '') {
         return [null, null];
      }
      $pos = strpos($division, '_');
      if ($pos === false) {
         return [null, $division];
      }
      $id = $this->intOrNull(substr($division, 0, $pos));
      $name = trim(substr($division, $pos + 1));
      return [$id, $name ?: null];
   }

   private function intOrNull($value)
   {
      if ($value === null || $value === '') {
         return null;
      }
      $v = trim((string) $value);
      return $v === '' ? null : (int) $v;
   }

   private function decimalOrNull($value)
   {
      if ($value === null || $value === '') {
         return null;
      }
      $v = trim((string) $value);
      return $v === '' ? null : (float) $v;
   }

   private function parseDate(?string $value): ?string
   {
      if ($value === null || trim($value) === '') {
         return null;
      }
      try {
         $dt = Carbon::createFromFormat('d/m/y', trim($value));
         return $dt->format('Y-m-d');
      } catch (\Exception $e) {
         return null;
      }
   }

   /**
    * Return Yajra DataTables response for stock_view_data filtered by outlet(s).
    * $acctnoOrAcctnos: single acctno (int|string) or array of acctnos for "All".
    */
   public function getStockDataDataTable($acctnoOrAcctnos)
   {
      $query = DB::table('stock_view_data')
         ->select('Outlet_Id', 'Outlet_Name', 'Item_Name', 'Company_name', 'PackDesc', 'PackSize', 'MRP', 'BatchQty', 'Dateofsending', 'Timeofsending');
      if (is_array($acctnoOrAcctnos)) {
         $query->whereIn('Outlet_Id', $acctnoOrAcctnos);
      } else {
         $query->where('Outlet_Id', $acctnoOrAcctnos);
      }

      return DataTables::of($query)
         ->addIndexColumn()
         ->orderColumn('Item_Name', 'Item_Name $1')
         ->orderColumn('Company_name', 'Company_name $1')
         ->orderColumn('MRP', 'MRP $1')
         ->orderColumn('BatchQty', 'BatchQty $1')
         ->editColumn('Dateofsending', function ($row) {
            return $row->Dateofsending
               ? Carbon::parse($row->Dateofsending)->format('d/m/y')
               : '';
         })
         ->make(true);
   }

   public function getCustomerStockAccess($params) {
      
      $query = CustomerStockAccess::
      select('customer_data.subdesc', 'customer_data.gstno', 'customer_stock_access.*')->join('customer_data', 'customer_data.acctno', '=', 'customer_stock_access.acctno');

      if(isset($params["quickFilter"]) && $params["quickFilter"]) {
         $keyword = $params["quickFilter"];
         // $query->where('subdesc',$params["quickFilter"]);

         $query->where(function($sql) use($keyword) {
            foreach(CustomerStockAccess::SEARCHABLE as $field) {
   
               if($field == "customer_stock_access.install_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_stock_access.install_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "customer_stock_access.next_amc_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_stock_access.next_amc_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "customer_stock_access.created_at") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_stock_access.created_at,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               $sql->orWhere($field, 'LIKE', '%'.$keyword.'%');
            }
         });
      }

      if(isset($params["status"]) && $params["status"]) {
         $status = json_decode($params["status"], true);
         if($status["value"] != -1) {
            $query->where('active', $status["value"]);
         }
      }

      if(isset($params['sortOrder']) && $params['sortOrder']) {
         $sortOrder = json_decode($params['sortOrder'], true);
         $query->orderBy($sortOrder['field'], $sortOrder['sort']);
      }
      
      return $query->paginate($params["pageSize"], ['*'], 'page', $params['page']);
   }

   public function deleteStockAccess($id) {
      $id = $this->decode($id, "Customer Stock");
      DB::transaction(function() use($id){
         // Delete the folder first
         $customerStock = CustomerStockAccess::find($id);
         $folderId = $customerStock->folder_id;
         app(FolderService::class)->deleteFolder($this->encode(['id' => $folderId]), true);
         $customerStock->delete();
      });
   }
}

