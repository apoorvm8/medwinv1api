<?php

namespace App\Http\Controllers\API\Software;

use App\Http\Controllers\Controller;
use App\Models\CustomerBackup;
use App\Models\CustomerData;
use App\Models\CustomerRegister;
use App\Models\CustomerStockAccess;
use App\Models\CustomerWhatsapp;
use App\Models\Einvoice;
use App\Models\FolderMaster;
use App\Services\CustomerBackupService;
use App\Services\CustomerStockAccessService;
use App\Services\CustomerWhatsappService;
use App\Services\EinvoiceService;
use App\Traits\HashIds;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CustomerDataApiController extends Controller
{
    use HashIds;
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

    /**
     * @desc To Save Customer Date
     * @parameters json object
     * @type POST
     * @auth Private
     */
    public function submit(Request $request) {
        $acctno = $request->acctno;
        if($acctno) {
            // Validate Date.
            $convertedDateArr = explode('/', $request->installdate);
            if(count($convertedDateArr) == 3) {
                $installdate = $convertedDateArr[2] . '/' . $convertedDateArr[1]  . '/' . $convertedDateArr[0];
                $installdate = date('Y-m-d', strtotime(str_replace('/', '-', $installdate)));
            } else {
                return response()->json(['success' => false, 'msg' => 'Invalid Install Date Provided.'], 400);
            }
            
            $convertedDateArr = explode('/', $request->nextamcdate);
            if(count($convertedDateArr) == 3) {
                $nextamcdate = $convertedDateArr[2] . '/' . $convertedDateArr[1]  . '/' . $convertedDateArr[0];
                $nextamcdate = date('Y-m-d', strtotime(str_replace('/', '-', $nextamcdate)));
            } else {
                return response()->json(['success' => false, 'msg' => 'Invalid Next Amc Date Provided.'], 400);
            }

            $customerdata = CustomerData::find($acctno);
            if($customerdata) {
                // Update
                CustomerData::where('acctno', $request->acctno)
                ->update(['acctno' => $request->acctno, 'subdesc' => $request->subdesc, 'subadd1' => $request->subadd1,
                    'subadd2' => $request->subadd2, 'subadd3' => $request->subadd3, 'subpercn' => $request->subpercn, 'subphone' => $request->subphone,
                    'gstno' => $request->gstno, 'area' => $request->area, 'state' => $request->state, 'interstate' => $request->interstate, 
                    'importsw' => $request->importsw, 'softwaretype' => $request->softwaretype, 'installdate' => $request->softwaretype, 
                    'installdate' => $installdate, 'nextamcdate' => $nextamcdate, 'amcamount' => $request->amcamount, 'recvamount' => $request->recvamount,
                    'acctcode' => $request->acctcode, 'sub3code' => $request->sub3code, 'email' => $request->email, 
                    'created_at' => $customerdata->created_at, 'updated_at' => now(), 'narration' => $request->narration, 'softref' => $request->softref
                ]);
                
                $msg = 'Customer Data Updated Successfully';
            } else {
                // Create
                CustomerData::create(['acctno' => $request->acctno, 'subdesc' => $request->subdesc, 'subadd1' => $request->subadd1,
                    'subadd2' => $request->subadd2, 'subadd3' => $request->subadd3, 'subpercn' => $request->subpercn, 'subphone' => $request->subphone,
                    'gstno' => $request->gstno, 'area' => $request->area, 'state' => $request->state, 'interstate' => $request->interstate, 
                    'importsw' => $request->importsw, 'softwaretype' => $request->softwaretype, 'installdate' => $request->softwaretype, 
                    'installdate' => $installdate, 'nextamcdate' => $nextamcdate, 'amcamount' => $request->amcamount, 'recvamount' => $request->recvamount,
                    'activestatus' => 'Y', 'acctcode' => $request->acctcode, 'sub3code' => $request->sub3code, 'email' => $request->email, 'created_at' => now(),
                    'narration' => $request->narration, 'softref' => $request->softref
                ]);
                $msg = 'Customer Data Saved Successfully';
            }
            return response()->json(['success' => true, 'msg' => $msg], 200);
        } else {
            return response()->json(['success' => false, 'msg' => 'Please provide valid acctno'], 400);
        }
    }

        /**
     * @desc To Get Next amc date
     * @parameters acctno
     * @type GET
     * @auth Private
     */
    public function getDate(Request $request) {
        $acctno = $request->acctno;
        if($acctno) {
            $customerdata = CustomerData::where('acctno', $acctno)->where('activestatus', 'Y')->first();
            if($customerdata) {
                $nextamcdate = date('d/m/Y', strtotime($customerdata->nextamcdate));
                return response()->json(['success' => true, 'msg' => 'Customer Data Found.', 'data' => $nextamcdate], 200);
            } else {
                return response()->json(['success' => false, 'msg' => 'Customer Data Not Found.'], 404);
            }
        } else {
            return response()->json(['success' => false, 'msg' => 'Please provide valid acctno'], 400);
        }
    }

    /**
     * @desc To Insert Into Customer Register 
     * @parameters json object
     * @type POST
     * @auth Private
     */
    public function register(Request $request) {
        $acctno = $request->acctno;
        if($acctno) {
            CustomerRegister::create(['acctno' => $request->acctno, 'subdesc' => $request->subdesc, 'datetimereg' => now(), 'remarks' => $request->remarks]);
            return response()->json(['success' => true, 'msg' => 'Customer Registered SUccessfully.'], 200);
        } else {
            return response()->json(['success' => false, 'msg' => 'Please provide valid acctno'], 400);
        }
    }

    /**
     * @desc Function to get einvoice status of customer
     * @parameters json object
     * @type GET
     * @auth Private
     */
    public function getEinvoiceStatus(Request $request) {
        $acctno = $request->acctno;
        if($acctno) {
            $customer = CustomerData::find($acctno);
            if(!$customer)
                return response()->json(['success' => false, 'msg' => 'Customer not found', 'data' => []], 404);
                
            if($customer->activestatus == 'N')
                return response()->json(['success' => false, 'msg' => 'Customer not active', 'data' => []], 401);
                
            $eInvoice = Einvoice::where('acctno', $acctno)->first();

            // Not found or not active
            if(!$eInvoice || (!$eInvoice->active))
                return response()->json(['success' => true, 'msg' => 'Customer E-Invoice Not Active', 'active' => false, 'data' => []], 200);
                
            return response()->json(['success' => true, 'msg' => 'Customer E-Invoice Active', 'active' => true, 'data' => [
                'username' => $eInvoice->username, 'password'=> $eInvoice->password ? Crypt::decrypt($eInvoice->password) : null, 'ipaddress' => $eInvoice->ipaddress
            ]], 200);
        } else {
            return response()->json(['success' => false, 'msg' => 'Please provide valid acctno', 'data' => []], 400);
        }
    }

    /**
     * @desc To Save Customer Date
     * @parameters json object
     * @type POST
     * @auth Private
     */
    public function updateEinvoice(Request $request) {
        $validate = Validator::make($request->all(), [
            'acctno' => 'required',
            'installdate' => 'required',
            'nextamcdate' => 'required',
            'remarks' => 'nullable',
            'username' => 'string|nullable',
            'password' => 'string|nullable',
            'ipaddress' => 'string|nullable'          
        ]);
        
        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

         // Validate Date.
         $convertedDateArr = explode('/', $request->installdate);
         if(count($convertedDateArr) == 3) {
             $installdate = $convertedDateArr[2] . '/' . $convertedDateArr[1]  . '/' . $convertedDateArr[0];
             $installdate = date('Y-m-d', strtotime(str_replace('/', '-', $installdate)));
         } else {
             return response()->json(['success' => false, 'msg' => 'Invalid Install Date Provided.'], 400);
         }
         
         $convertedDateArr = explode('/', $request->nextamcdate);
         if(count($convertedDateArr) == 3) {
             $nextamcdate = $convertedDateArr[2] . '/' . $convertedDateArr[1]  . '/' . $convertedDateArr[0];
             $nextamcdate = date('Y-m-d', strtotime(str_replace('/', '-', $nextamcdate)));
         } else {
             return response()->json(['success' => false, 'msg' => 'Invalid Next Amc Date Provided.'], 400);
         }


        $acctno = $request->acctno;
        if($acctno) {
            $customerdata = CustomerData::find($acctno);
            if(!$customerdata)
                return response()->json(['success' => false, 'msg' => 'Customer not found'], 404);
                
            if($customerdata->activestatus == 'N')
                return response()->json(['success' => false, 'msg' => 'Customer not active'], 401);
                
            
            $eInvoice = Einvoice::where('acctno', $acctno)->first();
            if(!$eInvoice)
                return response()->json(['success' => false, 'msg' => 'Customer E-Invoice Not Found.'], 404);

            Einvoice::where('acctno', $request->acctno)->update([
                'next_amc_date' => $nextamcdate,
                'install_date' => $installdate,
                'remarks' => $request->remarks,
                'updated_at' => now(),
                'username' => $request->username,
                'password' => $request->password ? Crypt::encrypt($request->password) : null,
                'ipaddress' => $request->ipaddress
            ]);
                
            return response()->json(['success' => true, 'msg' => 'Customer E-Invoice updated successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'msg' => 'Please provide valid acctno'], 400);
        }
    }

    public function serviceUpdate(Request $request) {
        $validate = Validator::make($request->all(), [
            'acctno' => 'required',
            'installdate' => 'required',
            'nextamcdate' => 'required',
            'remarks' => 'nullable',
            'usertype' => 'required'   
        ]);

        if($validate->fails()) {
            return response()->json(['success' => false, 'errors' => $validate->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

         // Validate Date.
         $convertedDateArr = explode('/', $request->installdate);
         if(count($convertedDateArr) == 3) {
             $installdate = $convertedDateArr[2] . '/' . $convertedDateArr[1]  . '/' . $convertedDateArr[0];
             $installdate = date('Y-m-d', strtotime(str_replace('/', '-', $installdate)));
         } else {
             return response()->json(['success' => false, 'msg' => 'Invalid Install Date Provided.'], 400);
         }
         
         $convertedDateArr = explode('/', $request->nextamcdate);
         if(count($convertedDateArr) == 3) {
             $nextamcdate = $convertedDateArr[2] . '/' . $convertedDateArr[1]  . '/' . $convertedDateArr[0];
             $nextamcdate = date('Y-m-d', strtotime(str_replace('/', '-', $nextamcdate)));
         } else {
             return response()->json(['success' => false, 'msg' => 'Invalid Next Amc Date Provided.'], 400);
         }

         $acctno = $request->acctno;
         if($acctno) {

            $customerdata = CustomerData::find($acctno);
            if(!$customerdata)
                return response()->json(['success' => false, 'msg' => 'Customer not found'], 404);
                 
            if($customerdata->activestatus == 'N')
                return response()->json(['success' => false, 'msg' => 'Customer not active'], 401);
                 

            // Can update even if inactive account
            $type = $request->usertype;
            if($type == "cloudbackup") {
                $cloudBackup = CustomerBackup::where('acctno', $acctno)->first();
                if(!$cloudBackup)
                    return response()->json(['success' => false, 'msg' => 'Customer Backup Not Found.'], 404); 

                CustomerBackup::where('acctno', $request->acctno)->update([
                    'next_amc_date' => $nextamcdate,
                    'install_date' => $installdate,
                    'remarks' => $request->remarks,
                    'updated_at' => now(),
                ]);
            } else if($type == 'stockview') {
                $stockView = CustomerStockAccess::where('acctno', $acctno)->first();
                if(!$stockView)
                    return response()->json(['success' => false, 'msg' => 'Customer Stock Access Not Found.'], 404); 

                CustomerStockAccess::where('acctno', $request->acctno)->update([
                    'next_amc_date' => $nextamcdate,
                    'install_date' => $installdate,
                    'remarks' => $request->remarks,
                    'updated_at' => now(),
                ]);
            } else if($type == 'whatsapp') {
                $customerWhatsapp = CustomerWhatsapp::where('acctno', $acctno)->first();

                if(!$customerWhatsapp)
                    return response()->json(['success' => false, 'msg' => 'Customer Whatsapp Record Not Found.'], 404); 

                CustomerWhatsapp::where('acctno', $request->acctno)->update([
                    'next_amc_date' => $nextamcdate,
                    'install_date' => $installdate,
                    'remarks' => $request->remarks,
                    'updated_at' => now(),
                ]);
            } else {
                return response()->json(['success' => false, 'msg' => 'Please provide a valid type'], 400);
            }
                              
            return response()->json(['success' => true, 'msg' => "Customer $type updated successfully."], 200);
         } else {
             return response()->json(['success' => false, 'msg' => 'Please provide valid acctno'], 400);
         }
    }

    /**
     * @desc Function to completely remove a customer from website
     * @method delete
     * @param Request $request,
     * @param string $id
     */
    public function deleteCustomer(Request $request) {
        // Check if customer exists
        $acctno = $request->acctno;
        if(!$acctno) {
            return response()->json(['success' => false, 'msg' => 'Please provide valid acctno'], Response::HTTP_BAD_REQUEST);
        }
        $customerData = CustomerData::find($request->acctno);

        if(!$customerData) {
            return response()->json(['success' => false, 'msg' => "Customer with id $acctno not found"], Response::HTTP_NOT_FOUND);
        }
        
        $servicesRemoved = [];
        // Check if customer has services present 
        // E-invoice
        $eInvoice = Einvoice::where('acctno', $acctno)->first();
        if($eInvoice) {
            // Remove e-invoice
            app(EinvoiceService::class)->deleteEinvoice($this->encode(['id' => $eInvoice->id]));
            array_push($servicesRemoved, 'E-Invoice');
        }

        // Whatsapp 
        $customerWhatsapp = CustomerWhatsapp::where('acctno', $acctno)->first();

        if($customerWhatsapp) {
            // Remove whatsapp service
            app(CustomerWhatsappService::class)->deleteCustomerWhatsappService($this->encode(['id' => $customerWhatsapp->id]));
            array_push($servicesRemoved, 'Whatsapp');
        }

        // Customer stock access
        $stockView = CustomerStockAccess::where('acctno', $acctno)->first();
        if($stockView) {
            // Remove stock view
            app(CustomerStockAccessService::class)->deleteStockAccess($this->encode(['id' => $stockView->id]));
            array_push($servicesRemoved, 'Stock Access');
        }

        // Customer backup access
        $cloudBackup = CustomerBackup::where('acctno', $acctno)->first();
        if($cloudBackup) {
            // Remove cloud backup
            app(CustomerBackupService::class)->deleteBackupAccess($this->encode(['id' => $cloudBackup->id]));
            array_push($servicesRemoved, 'Backup');
        }

        // Delete the customer
        $customerData->delete();

        $servicesRemovedStr = 'No services were present for this customer.';
        if(count($servicesRemoved) > 0) {
            $servicesRemovedStr = implode(',', $servicesRemoved);
        }
        return response(['success' => true, 'msg' => "Customer $acctno removed successfully, services removed: $servicesRemovedStr"], Response::HTTP_OK);
    }
}
