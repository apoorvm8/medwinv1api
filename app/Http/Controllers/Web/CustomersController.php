<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CustomerBackup;
use App\Models\CustomerData;
use App\Models\FolderMaster;
use App\Services\CustomerBackupService;
use App\Services\FolderService;
use App\Traits\HashIds;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JsonException;

class CustomersController extends Controller
{
    use HashIds;
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:customer');

    }

    /**
     * @desc Method To Get The Main Customer Data View
     * @type get
     */
    public function dashboard() {
        return view('customer.home');
    }

    /**
     * @desc Method to return the view related to changing the password
     */
    public function changePassword() {
        return view('customer.changepassword');
    }

    /**
     * @desc Method To Get the backup view
     */
    public function backup() {
        $customerBackup = CustomerBackup::where('acctno', Auth::guard('customer')->user()->acctno)->where('active', 1)->first();
        if(!$customerBackup) {
            Auth::guard('customer')->logout();
            return redirect('/');
        }

        $customerFolder = FolderMaster::find($customerBackup->folder_id);

        $fileArr = app(CustomerBackupService::class)->getBackupByType("currentyear", $customerFolder);
        return view('customer.backup')->with(['fileArr' => $fileArr]);
    }

    public function fetchBackup(Request $request) {
        $customerBackup = CustomerBackup::where('acctno', Auth::guard('customer')->user()->acctno)->where('active', 1)->first();
        if(!$customerBackup) {
            Auth::guard('customer')->logout();
            return response()->json(['success' => false, 'fileArr' => []], Response::HTTP_UNAUTHORIZED);
        }

        $customerFolder = FolderMaster::find($customerBackup->folder_id);
        $fileArr = app(CustomerBackupService::class)->getBackupByType($request->backupType, $customerFolder);
        return response()->json(['success' => true, 'fileArr' => $fileArr], Response::HTTP_OK);
    }

    public function updatePassword(Request $request) {
        if(!$request->customerObj)   
            return response()->json(['success' => true, 'msg' => 'Invalid Parameters Provided.'],400);

        try {
            $decodedArr = json_decode($request->customerObj, true, 512, JSON_THROW_ON_ERROR);
            $errors = [];
            $validate = Validator::make($decodedArr, [
                'oldPass' => 'required',
                'newPass' => 'required|min:3|regex:/^[a-z0-9@$!%*#?&]{2,}$/i|confirmed',
            ]);
            
            if($validate->fails()) {
                return response()->json(['success' => false, 'errors' => $validate->errors()->getMessages()], 400);
            }
            
            $acctno = Auth::guard('customer')->user()->acctno;
            $customer = CustomerData::where('acctno', $acctno)->where('activestatus', 'Y')->first();
            
            if(!$customer) {
                $errors["oldPass"] = ["User Not Found. Please Contact Admin"];
                return response()->json(['success' => false, 'errors' => $errors], 404);
            }
            
            if(!Hash::check($decodedArr['oldPass'], $customer->password)) {
                $errors["oldPass"] = ["Incorrect Old Password Provided."];
                return response()->json(['success' => false, 'errors' => $errors], 400);    
            }

            $customer->password = Hash::make($decodedArr['newPass']);
            $customer->save();
            return response()->json(['success' => true, 'msg' => "Login Password Updated Successfully."],200);
        } catch(JsonException $e) {
            return response()->json(['success' => false, 'msg' => "Invalid Customer Object Parsing. Please Check Your JSON."],400);
        }
    }

    public function fileDownload($fileId) {
        $id = 0;
        try {
            $id = Crypt::decrypt($fileId);
         } catch (DecryptException $e) {
             return redirect()->route('customer.dashboard');
         }
         app(FolderService::class)->downloadFile($this->encode(['id' => $id]));
    }  
}
