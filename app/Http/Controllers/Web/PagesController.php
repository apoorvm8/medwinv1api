<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index() {
        Meta::setTitle('Medwin Softwares | Pharmacy Billing, Stock, GST and Accounting Software');
        Meta::setKeywords(['Pharmacy Software', 'Inventory Software', 'Accounting Software', 'Billing Software', 'GST Software']);
        Meta::setDescription('Pharmacy-focused billing, batch-wise stock, expiry, purchase, GST reporting and accounting software for retail and wholesale pharmacies, with solutions for other trades.');

        return view('pages.index');
    }    

    public function contact() {
        Meta::setTitle('Contact Medwin Softwares | Request a Demo');
        Meta::setDescription('Tell us about your billing, stock and reporting needs. Contact Medwin Softwares to discuss the right retail or wholesale software for your business.');
        return view('pages.contact');
    }

    public function terms_and_condition() {
        Meta::setTitle('Terms and Conditions | Medwin Softwares');
        return view('pages.terms_and_conditions');
    }

    public function retail_pharma() {
        Meta::setTitle('Pharmacy Software | Pharmacy store software | Retail and Wholesale Pharmacy Software');
        Meta::setKeywords(['pharma software', 'pharmacy software', 'chemist software', 'pharmacy shop software', 'chemist shop software', 'pharmacy billing software', 'chemist billing software',
        'software for retail pharmacy', 'software for wholesale pharmacy', 'pharmacy GST software', 'medical retail software', 'medical wholesale software']);
        Meta::setDescription('Medwin pharmacy software supports billing, purchases, batch-wise stock, expiry tracking, returns, order management, GST reports and accounting.');
        return view('pages.retail.pharma');
    }

    public function retail_bookstore() {
        Meta::setTitle('Bookstore Software | Billing, Inventory and ISBN Search');
        Meta::setKeywords(['bookstore software', 'bookshop software', 'retail bookstore software', 'book software', 'books software', 
        'software for bookstore', 'bookstore billing software', 'ISBN search software', 'GST bookstore software']);
        Meta::setDescription('Medwin bookstore software combines ISBN search, billing, purchase import, stock control, author, publisher and genre-wise reports, GST reports and accounting.');
        return view('pages.retail.bookstore');
    }

    public function retail_footwear() {
        Meta::setTitle('Footwear Software  | Shoe Software | Retail Footwear | Wholesale Footwear');
        Meta::setKeywords(['footwear software', 'shoe shop software', 'footwear shop software', 'retail footwear', 'software for footwear', 'software for shoes']);
        Meta::setDescription('Medwin footwear software manages billing, purchases, barcode use, inventory and reports by style, colour and size, with GST reports and accounting.');
        return view('pages.retail.footwear');
    }

    public function retail_departmental() {
        Meta::setTitle('Departmental Store Software | Grocery Shop Software | Supermarket Software | Retail Departmental Store');
        Meta::setKeywords(['departmental store software', 'grocery shop software', 'grocery store software', 'supermarket software', 'supermarket retail software', 'departmental store retail software', 'software for departmental store', 
        'software for supermarket', 'retail software for supermarket', 'retail software for departmental store', 'software for grocery shop', 'retail software for grocery']);
        Meta::setDescription('Medwin departmental store software brings barcode billing, purchases, returns, stock, sales, profit reports, GST reports and accounting into one system.');
        return view('pages.retail.departmental');
    }
    
    public function file_download(Request $request, $fileId) {
        return;
    }

    public function downloads() {
        return redirect()->to('/');
    }
}
