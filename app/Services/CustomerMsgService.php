<?php

namespace App\Services;

use App\Models\CustomerMsg;
use App\Traits\HashIds;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerMsgService
{
   use HashIds;

   public function getCustomerMsgs($params) {
      
      $query = CustomerMsg::query();

      if(isset($params["quickFilter"]) && $params["quickFilter"]) {
         $keyword = $params["quickFilter"];
         // $query->where('subdesc',$params["quickFilter"]);

         $query->where(function($sql) use($keyword) {
            foreach(CustomerMsg::SEARCHABLE as $field) {
               if($field == "customer_msgs.created_at") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_msgs.created_at,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
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

   public function bulkMarkAsSeen($data) {
        try {
            $encodedIds = json_decode($data['idsStr'], true);
            $ids = [];
            foreach($encodedIds as $encodedId) {
                $ids[] = $this->decode($encodedId, "Customer Messages");
            }
            CustomerMsg::whereIn('id', $ids)->update(['seen' => 1]);
        } catch (Exception $ex) {
            throw new Exception("Error in marking the selected records as seen." . $ex->getMessage());
        }
    }
}

