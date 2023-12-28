<?php

namespace App\Services;

use App\Models\CustomerStockAccess;
use App\Models\Einvoice;
use App\Traits\HashIds;
use Illuminate\Support\Facades\DB;

class CustomerStockAccessService
{
   use HashIds;

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

