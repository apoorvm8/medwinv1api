<?php

namespace App\Services;

use App\Models\CustomerData;
use App\Models\CustomerWhatsapp;
use App\Models\Einvoice;
use App\Traits\HashIds;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerWhatsappService
{
   use HashIds;

   public function getCustomerWhatsapps($params) {
      
      $query = CustomerWhatsapp::
      select('customer_data.subdesc', 'customer_data.gstno', 'customer_whatsapps.*')->join('customer_data', 'customer_data.acctno', '=', 'customer_whatsapps.acctno');

      if(isset($params["quickFilter"]) && $params["quickFilter"]) {
         $keyword = $params["quickFilter"];
         // $query->where('subdesc',$params["quickFilter"]);

         $query->where(function($sql) use($keyword) {
            foreach(CustomerWhatsapp::SEARCHABLE as $field) {
   
               if($field == "customer_whatsapps.install_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_whatsapps.install_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "customer_whatsapps.next_amc_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_whatsapps.next_amc_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "customer_whatsapps.created_at") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_whatsapps.created_at,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
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

   public function deleteCustomerWhatsappService($id) {
      $id = $this->decode($id, "Customer Whatsapp");
      DB::transaction(function() use($id){
         CustomerWhatsapp::find($id)->delete();
      });
   }
}

