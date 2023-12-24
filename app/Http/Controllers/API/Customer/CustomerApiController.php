<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Customer\CustomerBackupAccessResource;
use App\Http\Resources\API\Customer\CustomerRegisterResource;
use App\Http\Resources\API\Customer\CustomersResource;
use App\Http\Resources\API\Customer\CustomerStockAccessResource;
use App\Http\Resources\API\Customer\CustomerWhatsappResource;
use App\Models\CustomerRegister;
use App\Services\CustomerBackupService;
use App\Services\CustomerRegisterService;
use App\Services\CustomerService;
use App\Services\CustomerStockAccessService;
use App\Services\CustomerWhatsappService;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{

    public function __construct(){} 

    /**
     * @desc Function to get the customers
     */
    public function get(Request $request) {
        $data = app(CustomerService::class)->getCustomers($request->query());
        return response(['success' => true, 'msg' => 'Customers retrieved', 'data' => [
            'customers' => new CustomersResource($data),
        ]]);
    }

    public function updateAction(Request $request, $acctno) {
        $msg = app(CustomerService::class)->updateAction($request->actionType, $acctno, $request->sourceId, auth('sanctum')->user());
        return response(['success' => true, 'msg' => $msg, 'data' => []]);
    }

    public function getStockAccess(Request $request) {
        $data = app(CustomerStockAccessService::class)->getCustomerStockAccess($request->query());
        return response(['success' => true, 'msg' => 'Customers stock access retrieved', 'data' => [
            'stockaccess' => new CustomerStockAccessResource($data),
        ]]);
    }

    public function getBackupAccess(Request $request) {
        $data = app(CustomerBackupService::class)->getCustomerBackupAccess($request);
        return response(['success' => true, 'msg' => 'Customers backup access retrieved', 'data' => [
            'backupaccess' => new CustomerBackupAccessResource($data),
        ]]);
    }

    public function getCustomerRegisters(Request $request) {
        $data = app(CustomerRegisterService::class)->getCustomerRegisters($request);
        return response([
            'success' => true, 'msg' => 'Customer Registers retrieved', 'data' => [
                'customerregisters' => new CustomerRegisterResource($data)
            ]
        ]);
    }
    
    public function getCustomerWhatsapps(Request $request) {
        $data = app(CustomerWhatsappService::class)->getCustomerWhatsapps($request);
        return response([
            'success' => true, 'msg' => 'Customer Whatsapp retrieved', 'data' => [
                'customerwhatsapps' => new CustomerWhatsappResource($data)
            ]
        ]);
    }

    public function bulkDeleteCustomerRegisters(Request $request) {
        app(CustomerRegisterService::class)->bulkDelete($request->all());
        return response([
            'success' => true, 'msg' => 'Selected records removed successfully.', 'data' => []
        ]);
    }

    public function getCustomerDashboardDetails(Request $request) {
        $data = app(CustomerService::class)->getCustomerDashboardDetails($request, auth('sanctum')->user());
        return response([
            'success' => true, 'msg' => 'Customer Dashboard details', 'data' => [
                'dashboarddetails' => $data
            ]
        ]);
    }

    public function getCustomerAmcDue(Request $request) {
        $data = app(CustomerService::class)->getCustomerAmcDue($request, auth('sanctum')->user());
        return response([
            'success' => true, 'msg' => 'Customer Amc Due list', 'data' => [
                'customeramcdue' => $data
            ]
        ]);
    }
}
