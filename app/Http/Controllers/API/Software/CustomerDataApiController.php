<?php

namespace App\Http\Controllers\API\Software;

use App\Http\Controllers\Controller;
use App\Models\CustomerBackup;
use App\Models\CustomerData;
use App\Models\FolderMaster;
use App\Services\CustomerBackupService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

class CustomerDataApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkCustomerReq');
    }

        /**
     * @desc To get backup of customers 
     * @param Request $request -> acctno, type
     * @type GET
     * @auth Private
     */
    public function getBackup(Request $request) {
        $acctno = $request->acctno;
        $backupType = $request->type;
        if($acctno && $backupType) {
            $customer = CustomerData::find($acctno);
            if(!$customer)
                return response()->json(['success' => false, 'msg' => 'Customer not found'], 404);
                
            if($customer->activestatus == 'N')
                return response()->json(['success' => false, 'msg' => 'Customer not active'], 401);

            $customerBackup = CustomerBackup::where('acctno', $acctno)->where('active', 1)->first();
            if(!$customerBackup) {
                return response()->json(['success' => false, 'msg' => 'Backup service not enabled or inactive for customer', 'data' => []], Response::HTTP_UNAUTHORIZED);
            }
            
            $customerFolder = FolderMaster::find($customerBackup->folder_id);
            
            if(!$customerFolder)
                return response()->json(['success' => false, 'msg' => 'Backup folder not found for customer', 'data' => []], Response::HTTP_UNAUTHORIZED);

            $fileArr = app(CustomerBackupService::class)->getBackupByType($backupType, $customerFolder);
            foreach($fileArr as $key => $el) {
                $el["id"] = Crypt::decrypt($el["id"]);
                $el["fileSize"] = $el["fileSize"] . " MB";
                $fileArr[$key] = $el;
            }
            if(count($fileArr) == 0) {
                $msg = "No backup found for this customer $acctno for type $backupType";
            } else {
                $msg = "Customer $acctno, $backupType backup found";
            }
            return response(['success' => true, 'msg' => $msg, 'data' => $fileArr]);
        } else {
            return response()->json(['success' => false, 'msg' => 'Please provide valid parameters'], 400);
        }
    }
}
