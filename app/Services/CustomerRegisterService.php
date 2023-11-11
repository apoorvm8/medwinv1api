<?php

namespace App\Services;

use App\Models\CustomerRegister;
use App\Traits\HashIds;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerRegisterService
{
   use HashIds;

    public function getCustomerRegisters($params) {
        $query = CustomerRegister::query();
        if(isset($params["quickFilter"]) && $params["quickFilter"]) {
        $keyword = $params["quickFilter"];
        // $query->where('subdesc',$params["quickFilter"]);
            foreach(CustomerRegister::SEARCHABLE as $field) {

                if($field == "customer_registers.datetimereg") {
                    $query->orWhere(DB::raw("DATE_FORMAT(datetimereg,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                    continue;
                }

                $query->orWhere($field, 'LIKE', '%'.$keyword.'%');
            }
        }
        return $query->paginate($params["pageSize"], ['*'], 'page', $params['page']);
    }

    public function bulkDelete($data) {
        try {
            $encodedIds = json_decode($data['idsStr'], true);
            $ids = [];
            foreach($encodedIds as $encodedId) {
                $ids[] = $this->decode($encodedId, "Customer Registers");
            }
            CustomerRegister::whereIn('id', $ids)->delete();
        } catch (Exception $ex) {
            throw new Exception("Error in deleting the selected records " . $ex->getMessage());
        }
    }
}

