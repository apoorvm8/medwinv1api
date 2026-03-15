<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CustomerBackup;
use App\Models\CustomerData;
use App\Models\CustomerStockAccess;
use App\Models\FolderMaster;
use App\Services\CustomerBackupService;
use App\Services\CustomerStockAccessService;
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

    /**
     * @desc Method To Get the stock data view (same logic as backup: require active stock access).
     */
    public function stockData() {
        $user = Auth::guard('customer')->user();
        $acctno = $user->acctno;
        $linked = $user->linked_outlet_ids ? array_map('trim', explode(',', $user->linked_outlet_ids)) : [];
        $linked = array_filter($linked);
        $acctnos = array_values(array_unique(array_merge([$acctno], $linked)));
        $names = CustomerData::whereIn('acctno', $acctnos)->pluck('subdesc', 'acctno')->toArray();
        $outletOptions = [];
        foreach ($acctnos as $id) {
            $name = $names[$id] ?? $id;
            $outletOptions[] = ['value' => (string) $id, 'label' => $name . ' (' . $id . ')'];
        }
        $outletOptions[] = ['value' => 'all', 'label' => 'All'];
        return view('customer.stockdata', [
            'outletOptions' => $outletOptions,
            'defaultOutlet' => (string) $acctno,
        ]);
    }

    /**
     * @desc Yajra DataTables AJAX: returns stock_view_data for selected outlet(s).
     */
    public function stockDataTable(Request $request) {
        $customerStockAccess = CustomerStockAccess::where('acctno', Auth::guard('customer')->user()->acctno)->where('active', 1)->first();
        if (!$customerStockAccess) {
            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }
        $user = Auth::guard('customer')->user();
        $acctno = $user->acctno;
        $linked = $user->linked_outlet_ids ? array_map('trim', explode(',', $user->linked_outlet_ids)) : [];
        $linked = array_filter($linked);
        $allowed = array_merge([$acctno], $linked);
        $outlet = $request->get('outlet', $acctno);
        if ($outlet === 'all') {
            $acctnos = array_values(array_unique($allowed));
            return app(CustomerStockAccessService::class)->getStockDataDataTable($acctnos);
        }
        if (!in_array((string) $outlet, array_map('strval', $allowed), true)) {
            return response()->json([], Response::HTTP_FORBIDDEN);
        }
        return app(CustomerStockAccessService::class)->getStockDataDataTable($outlet);
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
