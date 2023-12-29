<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CustomerMsg;
use Illuminate\Http\Request;

class CustomerMsgController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|string',
            'mobile_no' => 'required|digits:10',
            'email' => 'nullable|email|unique:customer_msgs,email',
            'type_of_soft' => 'required|string'
        ]);

        // Format the software name
        $type_of_soft = $request->input("type_of_soft");

        if($type_of_soft != "") {
            if($type_of_soft == "retailPharma") {
                $type_of_soft = "Retail Pharma";
            } else if($type_of_soft == "wholeSalePharma") {
                $type_of_soft = "Wholesale Pharma";
            } else if($type_of_soft == "retailSupermarket") {
                $type_of_soft = "Retail Supermarket";
            } else if($type_of_soft == "retailBookstore") {
                $type_of_soft = "Retail Bookstore";
            }else if($type_of_soft == "retailFootwear") {
                $type_of_soft = "Retail Footwear";
            } else if($type_of_soft == "wholeSaleFootwear") {
                $type_of_soft = "Wholesale Footwear";
            } else if($type_of_soft == "retailGarment") {
                $type_of_soft = "Retail Garment";
            } else if($type_of_soft == "other") {
                $type_of_soft = "Other";
            }
        } else {
            return;
        }

        // // // If mobile no is correct send SMS;
        // $smsController = new SMSController;
        // $smsController->getUserNumber($request->input("mobile_no"));

        // // If email then send email to user 
        // if($request->input("email") != "") {
        //     $mailController = new EmailController;
        //     $mailController->html_email($request->input("name"), $request->input("mobile_no"), $request->input("email"));
        // }

        
        // Create the customer message
        $customerMsg = new CustomerMsg();
        $customerMsg->name = ucwords($request->input("name"));
        $customerMsg->mobile_no = $request->input("mobile_no");
        $customerMsg->email = $request->input("email");
        $customerMsg->type_of_soft = $type_of_soft; // $request->input('type_of_soft');
        $customerMsg->seen = 0;
        $customerMsg->seen_by = "none";
        $customerMsg->save();

        return redirect('/contact')->with('success', 'Your message has been sent successfully, we will get back to you');
    }
}
