@extends('layouts.app')

@section('content')

{{-- Section Area --}}
<section class="text-left section-1 mt-5 px-2 px-md-0">
    <div class="container">
        <h1 class="text-center">Pharmacy Software</h1>
        <div class="col-sm-12 mt-4">
            <p class="lead secondary-color">
                This software provides easy invoicing, purchase entry, purchase import, Batchwise stock report,
                Expiry list, Sales return, Purchase return, Order management. Also includes accounting with all
                features and GST reports. SMS notifications and barcode billing.
            </p>
        </div>
    </div>
</section>

<!-- Section: Features of Softwares  -->
<section class="text-center text-md-left section-2 py-3 px-3 px-md-0">
    <div class="container">
        <h1 class="text-center mt-2">Features</h1>
        <div class="row mt-5">
            <div class="col-sm-12 col-md-6 col-lg-3">
                <div class="py-3">
                    <a href="/assets/img/pharmacy_soft0.jpg" data-lightbox="bill" data-title="Image">
                        <img src="{{asset('assets/img/pharmacy_soft0.jpg')}}" class="pharma-bill" height="120"
                            width="120">
                    </a>
                </div>
                <div>
                    <p class="lead">Easy Billing</p>
                    <p class="secondary-color">A very simple, to the point billing approach. Anybody can make and print
                        bills easily</p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 ">
                <div class="py-3">
                    <img src="{{asset('assets/img/features/stock_logo0.png')}}" class="img-responsive" height="80"
                        width="80">
                </div>
                <div>
                    <p class="lead">Stock Management</p>
                    <p class="secondary-color">Stock inventory is very efficiently maintained in Medwin Softwares. All
                        kinds of stock ledgers
                        are readily availble.</p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 ">
                <div class="py-3">
                    <img src="{{asset('assets/img/features/import_logo0.jpg')}}" class="img-responsive" height="80"
                        width="80">
                </div>
                <div>
                    <p class="lead">Import Purchase</p>
                    <p class="secondary-color">Purchases can be imported easily. With a single click purchase data gets
                        generated in no time.
                    </p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 ">
                <div class="py-3">
                    <img src="{{asset('assets/img/features/sale_report_logo0.png')}}" class="img-responsive" height="80"
                        width="80">
                </div>
                <div>
                    <p class="lead">Sale, Purchase Report</p>
                    <p class="secondary-color">All kinds of sale and purchase reports are available. Daily sale, Daily
                        purchase, Company wise
                        sale, Company wise purchase, Profit wise report etc are available.</p>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-sm-12 col-md-6 col-lg-3 ">
                <div class="py-3">
                    <img src="{{asset('assets/img/features/gst_logo0.png')}}" class="img-responsive" height="80"
                        width="80">
                </div>
                <div>
                    <p class="lead">GST Reports</p>
                    <p class="secondary-color">GSTR1, GSTR2, GSTR3B, B2B, B2CS, HSN wise reports are available. Also
                        offline
                        CSV and excel formats for GSTR1 are available.</p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 ">
                <div class="py-3">
                    <img src="{{asset('assets/img/features/accounting_logo0.png')}}" class="img-responsive" height="80"
                        width="80">
                </div>
                <div>
                    <p class="lead">Accounting</p>
                    <p class="secondary-color">Ledger, Sundry Debtors, Sundry Credtors, Trial balance, Double voucher
                        entry,
                        Cash deposit, Sale receipt, Purchase receipt etc are available.</p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 ">
                <div class="py-3">
                    <img src="{{asset('assets/img/features/stock_report_logo0.jpg')}}" class="img-responsive"
                        height="80" width="80">
                </div>
                <div>
                    <p class="lead">Stock Reports</p>
                    <p class="secondary-color">Depending upon the analysis of sold data or limit set for any item,
                        orders
                        can be generated
                        efficiently.</p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 ">
                <div class="py-3">
                    <img src="{{asset('assets/img/features/notification_logo0.png')}}" class="img-responsive"
                        height="80" width="80">
                </div>
                <div>
                    <p class="lead">Easy Billing</p>
                    <p class="secondary-color">Instant notifications can be sent to customers through SMS facility
                        provided by Medwin Softwares
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection