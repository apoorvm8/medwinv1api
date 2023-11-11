<?php

namespace App\Services;

use App\Models\CustomerData;
use App\Models\Einvoice;
use App\Traits\HashIds;
use Exception;
use Illuminate\Support\Facades\DB;

class EinvoiceService
{
   use HashIds;

   public function getCustomerEinvoices($params) {
      
      $query = Einvoice::
      select('customer_data.subdesc', 'customer_data.gstno', 'einvoice.*')->join('customer_data', 'customer_data.acctno', '=', 'einvoice.acctno');

      if(isset($params["quickFilter"]) && $params["quickFilter"]) {
         $keyword = $params["quickFilter"];
         // $query->where('subdesc',$params["quickFilter"]);

         $query->where(function($sql) use($keyword) {
            foreach(Einvoice::SEARCHABLE as $field) {
   
               if($field == "einvoice.install_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(einvoice.install_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "einvoice.next_amc_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(einvoice.next_amc_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "einvoice.created_at") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(einvoice.created_at,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
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

   public function deleteEinvoice($id) {
      $id = $this->decode($id, "E-Invoice");
      DB::transaction(function() use($id){
         Einvoice::find($id)->delete();
      });
   }
}

