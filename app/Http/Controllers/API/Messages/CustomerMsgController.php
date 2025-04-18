<?php

namespace App\Http\Controllers\API\Messages;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Messages\CustomerMsgResource;
use App\Services\CustomerMsgService;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerMsgController extends Controller
{

    public function __construct(){} 

    /**
     * @desc Function to get the customers messages
     */
    public function get(Request $request) {
        $data = app(CustomerMsgService::class)->getCustomerMsgs($request->query());
        return response(['success' => true, 'msg' => 'Customers messages retrieved', 'data' => [
            'customerMsgs' => new CustomerMsgResource($data),
        ]]);
    }

    public function update(Request $request) {
        app(CustomerMsgService::class)->bulkAction($request->all());
        return response([
            'success' => true, 'msg' => 'Selected records marked as seen successfully.', 'data' => []
        ]);
    }

    public function delete(Request $request) {
        app(CustomerMsgService::class)->bulkAction($request->all());
        return response([
            'success' => true, 'msg' => 'Selected records deleted successfully.', 'data' => []
        ]);
    }

}
