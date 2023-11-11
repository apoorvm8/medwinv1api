<?php

namespace App\Services;

use App\Models\CustomerBackup;
use App\Models\CustomerStockAccess;
use App\Models\Einvoice;
use App\Traits\HashIds;
use Illuminate\Support\Facades\DB;

class CustomerBackupService
{
   use HashIds;

   public function getCustomerBackupAccess($params) {
      
      $query = CustomerBackup::
      select('customer_data.subdesc', 'customer_data.gstno', 'customer_backups.*')->join('customer_data', 'customer_data.acctno', '=', 'customer_backups.acctno');

      if(isset($params["quickFilter"]) && $params["quickFilter"]) {
         $keyword = $params["quickFilter"];
         // $query->where('subdesc',$params["quickFilter"]);

         $query->where(function($sql) use($keyword) {
            foreach(CustomerStockAccess::SEARCHABLE as $field) {
   
               if($field == "customer_backups.install_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_backups.install_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "customer_backups.next_amc_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_backups.next_amc_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "customer_backups.created_at") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_backups.created_at,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
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
      return $query->paginate($params["pageSize"], ['*'], 'page', $params['page']);
   }

   public function deleteBackupAccess($id) {
      $id = $this->decode($id, "Customer Backup");
      DB::transaction(function() use($id){
         // Delete the folder first
         $customerBackup = CustomerBackup::find($id);
         $folderId = $customerBackup->folder_id;
         app(FolderService::class)->deleteFolder($this->encode(['id' => $folderId]), true);
         $customerBackup->delete();
      });
   }
}

