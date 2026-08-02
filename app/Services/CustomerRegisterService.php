<?php

namespace App\Services;

use App\Models\CustomerRegister;
use App\Traits\HashIds;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerRegisterService
{
    use HashIds;

    public function getCustomerRegisters($params)
    {
        $query = CustomerRegister::query()
            ->leftJoin('customer_data', 'customer_data.acctno', '=', 'customer_registers.acctno');

        if (isset($params['quickFilter']) && $params['quickFilter']) {
            $keyword = $params['quickFilter'];
            foreach (CustomerRegister::SEARCHABLE as $field) {
                if ($field == 'customer_registers.datetimereg') {
                    $query->orWhere(DB::raw("DATE_FORMAT(customer_registers.datetimereg,'%d/%m/%Y')"), 'LIKE', '%'.$keyword.'%');

                    continue;
                }

                if ($field == 'customer_data.nextamcdate') {
                    $query->orWhere(DB::raw("DATE_FORMAT(customer_data.nextamcdate,'%d/%m/%Y')"), 'LIKE', '%'.$keyword.'%');

                    continue;
                }

                $query->orWhere($field, 'LIKE', '%'.$keyword.'%');
            }
        }

        if (isset($params['sortOrder']) && $params['sortOrder']) {
            $sortOrder = json_decode($params['sortOrder'], true);
            $sortableFields = [
                'acctno' => 'customer_registers.acctno',
                'subdesc' => 'customer_registers.subdesc',
                'datetimereg' => 'customer_registers.datetimereg',
                'next_amc_date' => 'customer_data.nextamcdate',
            ];
            $sortField = $sortableFields[$sortOrder['field'] ?? 'acctno'] ?? 'customer_registers.acctno';
            $sortDirection = strtolower($sortOrder['sort'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortDirection);
        }

        return $query->paginate($params['pageSize'], [
            'customer_registers.*',
            'customer_data.nextamcdate as next_amc_date',
        ], 'page', $params['page']);
    }

    public function bulkDelete($data)
    {
        try {
            $encodedIds = json_decode($data['idsStr'], true);
            $ids = [];
            foreach ($encodedIds as $encodedId) {
                $ids[] = $this->decode($encodedId, 'Customer Registers');
            }
            CustomerRegister::whereIn('id', $ids)->delete();
        } catch (Exception $ex) {
            throw new Exception('Error in deleting the selected records '.$ex->getMessage());
        }
    }
}
