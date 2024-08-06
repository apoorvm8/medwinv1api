<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index() {
        Meta::setTitle('Inventory Software | Retail Softwares | GST Softwares | Accounting Software | Wholesale Software');
        Meta::setKeywords(['Inventory Software', 'Accounting Software', 'Billing Software', 'Gst Software', 'Erp Software']);
        Meta::setDescription('Inventory and Accounting Software for Retailers, Wholesalers, Gst Billing & Filing, Stock management, Report Generation');

        return view('pages.index');
    }    

    public function contact() {
        Meta::setTitle('Contact');
        return view('pages.contact');
    }

    public function terms_and_condition() {
        return view('pages.terms_and_conditions');
    }

    public function retail_pharma() {
        Meta::setTitle('Pharmacy Software | Pharmacy store software | Retail and Wholesale Pharmacy Software');
        Meta::setKeywords(['pharma software', 'pharmacy software', 'chemist software', 'pharmacy shop software', 'chemist shop software', 'pharmacy billing software', 'chemist billing software',
        'software for retail pharmacy', 'software for wholesale pharmacy', 'pharmacy gst complient', 'medical retail software', 'medical wholesale software']);
        Meta::setDescription('Medwin Pharmacy Software- Our pharmacy/chemist software is specially designed for Retail Pharmacy/Retail Chemist and provides easy invoicing, purchase entry, purchase import, Batchwise stock report, Expiry list, Sales return, Purchase return, Order management.');
        return view('pages.retail.pharma');
    }

    public function retail_bookstore() {
        Meta::setTitle('Bookstore Software | Library software | Retail Bookstore software');
        Meta::setKeywords(['bookstore software', 'bookshop software', 'retail bookstore software', 'book software', 'books software', 
        'software for bookstore', 'bookstore billing software', 'library management software', 'gst bookstore software']);
        Meta::setDescription('Medwin Bookstore Software- Our bookstore/bookshop software provides Easy billing, Import purchase, Publisher wise, Author wise, Genre wise reports and Fast search. All kinds of sale purchase, Profit reports. Accounting and GST reports available. ISBN search provided.');
        return view('pages.retail.bookstore');
    }

    public function retail_footwear() {
        Meta::setTitle('Footwear Software  | Shoe Software | Retail Footwear | Wholesale Footwear');
        Meta::setKeywords(['footwear software', 'shoe shop software', 'footwear shop software', 'retail footwear', 'software for footwear', 'softwear for shoes']);
        Meta::setDescription('Medwin Footwear software- All purpose retail and wholesale software. Easy billing, Purchase entry, Import purchase. Stylewise, Colourwise, Sizewise, Stock, Sale and Purchase reports and fast search of a product. Also includes Accounting and GST reports.');
        return view('pages.retail.footwear');
    }

    public function retail_departmental() {
        Meta::setTitle('Departmental Store Software | Grocery Shop Software | Supermarket Software | Retail Departmental Store');
        Meta::setKeywords(['departmental store software', 'grocery shop software', 'grocery store software', 'supermarket software', 'supermarket retail software', 'departmental store retail software', 'software for departmental store', 
        'software for supermarket', 'retail software for supermarket', 'retail software for departmental store', 'software for grocery shop', 'retail software for grocery']);
        Meta::setDescription('Medwin Departmental Store Software- All purpose retail and wholesale software. Easy invoicing, Purchase entry, Purchase return, Sale return. All Sale, Stock and Purchase reports available. Profit report, Stock and Sales statement. Barcode search, Accounting, GST reports are all available.');
        return view('pages.retail.departmental');
    }
    
    public function file_download(Request $request, $fileId) {
        return;
    }

    public function downloads() {
        return redirect()->to('/');
    }
}
