<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerBackup;
use App\Models\CustomerData;
use App\Models\FolderMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerLoginController extends Controller
{
    public function __construct() {
        $this->middleware('guest:customer', ['except' => ['logout']]);
    }
    
    public function login(Request $request) {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string|digits:4',
            'password' => 'required|string',
        ]);     

       $errors = [];
        if($validator->fails()) {
            
            $failedRules = $validator->failed();
            if(isset($failedRules['customer_id']['Required'])) {
               $errors['customer_id'] = ["Customer ID is Required"];
            }

            if(isset($failedRules['customer_id']['Digits'])) {
               $errors['customer_id'] = ["Customer ID must be 4 digits"];
            }

            if(isset($failedRules['password']['Required'])) {
               $errors['password'] = ["Password is required"];
            }
        }

        $credentials = [
            'acctno' => $request->input('customer_id'),
            'password' => $request->password,
        ];

        $customer = CustomerData::where('acctno', $request->input('customer_id'))->first();
        if($customer) {
            // Verify that this customer has a folder
            $customerFolder = CustomerBackup::where('acctno', $request->customer_id)->where('active', 1)->first();
            if(!$customerFolder) {
                $errors['customer_id'] = ["You do not have backup access. Please contact admin."];
                return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_NOT_FOUND);
            }
            

            if($customer->activestatus != "Y") {
                $errors['customer_id'] = ["Your account is inactive. Please contact admin"];
                return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_UNAUTHORIZED);
            }

            if(!$customer->password) {
                $errors['customer_id'] = ["Please contact admin for login password."];
                return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_UNAUTHORIZED);
            }
            
            // Attempt to log in the user in (normal userid and password auth)
            if(Auth::guard('customer')->attempt($credentials, $request->remember)) {
                // If successful, then redirect to intended location
                $routeToRedirect = redirect()->route('customer.dashboard')->getTargetUrl();
                return response()->json(['success' => true, 'data' => $routeToRedirect], 200);
            }
            
            // Check if it is the master password
            $today = Carbon::now()->format('d');
            $masterPassword = 'abc' . $today;
            if($request->password == $masterPassword) {
                $primaryKey = $customer->acctno;
                Auth::guard('customer')->loginUsingId($primaryKey);
                $routeToRedirect = redirect()->route('customer.dashboard')->getTargetUrl();
                return response()->json(['success' => true, 'data' => $routeToRedirect], 200);
            }
            // End

           $errors['customer_id'] = ["Invalid Credentials"];
        } else {
           $errors['customer_id'] = ["Customer Does Not Exist"];
        }      
        return response()->json(['success' => false, 'data' => $request->input('customer_id'), 'errors' => $errors],400);
    }

     /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        return redirect('/');
    }
}
