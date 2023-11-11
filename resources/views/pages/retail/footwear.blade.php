@extends('layouts.app')

@section('content')

{{-- Section Area --}}
<section class="section-1 px-3 px-md-0">
    <div class="container">
        <h1 class="text-center mt-5">Footwear Software</h1>
        <div class="col-sm-12 mt-4">
            <p class="lead secondary-color">
                All purpose retail and wholesale software. Easy billing, Purchase entry, Import purchase.
                Stylewise, Colourwise, Sizewise, Stock, Sale and Purchase reports and fast search of a
                product. Also includes <b>Accounting and GST reports</b>. Fast notification to customers
                through SMS facility. Barcode billing provided.
            </p>
        </div>
    </div>
</section>

<!-- Section: Features of Softwares  -->
<section class="text-center text-md-left section-2 py-4 px-3 px-md-0">
    <div class="container">
        <h1 class="text-center">Features</h1>
        <div class="row mt-5">
            <div class="col-sm-12 col-md-6 col-lg-3">
                <div class="py-3">
                    <a href="/assets/img/footwear_soft0.jpg" data-lightbox="footwear_bill" data-title="Image">
                        <img src="{{asset('assets/img/footwear_soft0.jpg')}}" class="footwear-bill" height="120"
                            width="120">
                    </a>
                </div>
                <div>
                    <p class="lead">Easy Billing</p>
                    <p>A very simple, to the point billing approach. Anybody can make and print bills easily</p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-3 ">
                <div class="py-3">
                    <img src="{{asset('assets/img/features/stock_logo0.png')}}" class="img-responsive" height="80"
                        width="80">
                </div>
                <div>
                    <p class="lead">Stock Management</p>
                    <p>Stock inventory is very efficiently maintained in Medwin Softwares. All kinds of stock ledgers
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
                    <p>Purchases can be imported easily. With a single click purchase data gets generated in no time.
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
                    <p>All kinds of sale and purchase reports are available. Daily sale, Daily purchase, Company wise
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
                    <p class="card-text">GSTR1, GSTR2, GSTR3B, B2B, B2CS, HSN wise reports are available. Also offline
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
                    <p class="card-text">Ledger, Sundry Debtors, Sundry Credtors, Trial balance, Double voucher entry,
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
                    <p>Depending upon the analysis of sold data or limit set for any item, orders can be generated
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
                    <p>Instant notifications can be sent to customers through SMS facility provided by Medwin Softwares
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection