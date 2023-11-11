@extends('layouts.app')

@section('content')
{{-- Pointer to scroll to the top of position --}}
<a href="#home" id="myBtn" title="Go to top"><i class="fa fa-arrow-up"></i></a>
{{-- End of pointer --}}

{{-- Showcase --}}
<header id="showcase">
    <div class="showcase-content">
        <div class="container">
            <div class="row">
                <div class="col-md-6 showcase-text order-2 order-md-1">
                    <h1 class="lg-h">
                        Welcome to
                        <span class="primary-color">Medwin</span> Softwares
                    </h1>
                    <p class="lead secondary-color mt-4">
                        Providing best softwares for Pharmacy, Departmental Store, Footwear, Bookstore
                        and many more since two decade.
                    </p>
                    <a href="#about" class="about-btn btn btn-primary btn-lg px-md-5 mt-md-4">About</a>
                    <a href="#features" class="features-btn btn btn-secondary btn-lg px-md-4 mt-md-4 ml-4">Features</a>
                </div>
                <div class="col-md-5 showcase-img order-1 order-md-2">
                    <img src="{{asset('assets/img/collage6.png')}}" alt="" class="img-fluid" />
                </div>
            </div>
            <div id="showcaseHighlights" class="row">
                <div class="col-md-4 d-flex flex-column">
                    <h1 class="h4 pl-1">
                        <i class="fa fa-plus px-2 py-1"></i>
                    </h1>
                    <h1 class="h4 my-3">Best Software</h1>
                    <p class="secondary-color">
                        We provide best softwares in all trades with inventory and accounting services for Wholesale and
                        Retail users
                    </p>
                </div>
                <div class="col-md-4">
                    <h1 class="h4 pl-1">
                        <i class="fa fa-chart-line px-2 py-1"></i>
                    </h1>
                    <h1 class="h4 my-3">GST Compliant</h1>
                    <p class="secondary-color">
                        Generate GST compliant invoices, keep track of all compliance requirements and generate all GST
                        returns and reports.
                    </p>
                </div>
                <div class="col-md-4">
                    <h1 class="h4 pl-1">
                        <i class="fa fa-rupee-sign px-2 py-1"></i>
                    </h1>
                    <h1 class="h4 my-3">Simple And Affordable Price</h1>
                    <p class="secondary-color">
                        Our softwares are simple yet provides all features at a reasonably affordable price for all
                        trade users.
                    </p>
                </div>
            </div>
        </div>
    </div>
</header>
{{-- End of showcase --}}
<!-- Section About -->
<section id="about" class="p-5 w-100">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 m-auto">
                <h1 class="md-h text-center">
                    <i class="fa fa-question px-2 py-1"></i> About
                </h1>
                <p class="lead secondary-color mt-4 d-none d-md-block">
                    Medwin Softwares is a software provider company providing
                    simple and easy to use yet refined and reliable softwares. We
                    have been providing software solutions to small and medium
                    sized businesses for over twenty years. Medwin Softwares
                    provides retail software in Pharmacuticals, Bookstores,
                    Garments and more, that will help you drive your business
                    smoothly. We believe in full customer satisfaction and strive
                    to work 24/7 for it.
                </p>
                <p class="secondary-color mt-4 d-md-none d-block">
                    Medwin Softwares provides retail software in Pharma ,
                    Bookstores, Garments and more, that will help you drive your
                    business smoothly. We believe in full customer satisfaction
                    and strive to work 24/7 for it.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h2 class="font-weight-bold mt-5 text-center d-none d-md-block">
                    Retail And Wholesale Inventory Softwares
                </h2>
                <h4 class="font-weight-bold mt-5 text-center d-md-none d-block">
                    Retail And Wholesale Inventory Softwares
                </h4>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-6 col-lg-3 cardLeft">
                <div class="card card-light">
                    <div class="card-header bg-white">
                        <img src="{{asset('assets/img/pharmacyimg1.png')}}" class="card-img-top" />
                    </div>
                    <div class="card-body">
                        <h1 class="h4 card-title">Pharma</h1>
                        <p class="card-text secondary-color">
                            Our software provides easy invoicing, Purchase entry,
                            Purchase import, Batchwise stock report, Expiry list...
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{route('pharmacy')}}" class="btn btn-primary btn-block">View More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 cardLeft">
                <div class="card card-light">
                    <div class="card-header bg-white">
                        <img src="{{asset('assets/img/grocery1.png')}}" class="card-img-top" />
                    </div>
                    <div class="card-body">
                        <h1 class="h4 card-title">Departmental</h1>
                        <p class="card-text secondary-color">
                            All purpose retail and wholesale software. Easy invoicing,
                            Purchase entry, Purchase return and more.
                            <span class="d-md-block d-lg-none">Purchase return, Sale return.</span>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{route('departmental')}}" class="btn btn-primary btn-block">View More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 cardRight">
                <div class="card card-light mt-md-3 mt-lg-0">
                    <div class="card-header bg-white">
                        <img src="{{asset('assets/img/bookstore.png')}}" class="card-img-top" />
                    </div>
                    <div class="card-body">
                        <h1 class="h4 card-title">Bookstore</h1>
                        <p class="card-text secondary-color">
                            Easy billing, Import purchase, Publisher wise, Author
                            wise, Genre wise reports and Fast search...
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{route('bookstore')}}" class="btn btn-primary btn-block">View More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 cardRight">
                <div class="card mt-md-3 mt-lg-0">
                    <div class="card-header bg-white">
                        <img src="{{asset('assets/img/shoes1.png')}}" class="card-img-top" />
                    </div>
                    <div class="card-body">
                        <h1 class="h4 card-title">Footwear</h1>
                        <p class="card-text secondary-color">
                            All purpose retail and wholesale software. Easy billing,
                            Purchase entry, Import purchase and more.
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{route('footwear')}}" class="btn btn-primary btn-block">View More</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- </div> -->
</section>
<!-- Features -->
<section id="features" class="p-4 pt-5 text-center text-md-left">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="md-h text-center">
                    <i class="fa fa-check px-2 py-1"></i> Main Features
                </h1>
            </div>
        </div>
        <div class="row mt-5 featureLeft">
            <div class="col-md-4 col-lg-3">
                <div class="py-3">
                    <img src="{{asset('assets/img/billing_logo0.jpg')}}" class="img-responsive" height="80"
                        width="80" />
                </div>
                <div>
                    <p class="lead">Easy Billing</p>
                    <p class="secondary-color">
                        A very simple, to the point billing approach. Anybody can
                        make and print bills easily
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="py-3">
                    <img src="{{asset('assets/img/stock_logo0.png')}}" class="img-responsive" height="80" width="80" />
                </div>
                <div>
                    <p class="lead">Stock Management</p>
                    <p class="secondary-color">
                        Stock inventory is very efficiently maintained in Medwin
                        Softwares. All kinds of stock ledgers are readily availble.
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="py-3">
                    <img src="{{asset('assets/img/import_logo0.jpg')}}" class="img-responsive" height="80" width="80" />
                </div>
                <div>
                    <p class="lead">Import Purchase</p>
                    <p class="secondary-color">
                        Purchases can be imported easily. With a single click
                        purchase data gets generated in no time.
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 d-md-none d-lg-block">
                <div class="py-3">
                    <img src="{{asset('assets/img/sale_report_logo0.png')}}" class="img-responsive" height="80"
                        width="80" />
                </div>
                <div>
                    <p class="lead">Sale, Purchase Report</p>
                    <p class="secondary-color">
                        All kinds of sale and purchase reports are available. Daily
                        sale, Daily purchase, Company wise sale, Company wise
                        purchase, Profit wise report etc are available.
                    </p>
                </div>
            </div>
        </div>
        <div class="row mt-3 featureRight">
            <div class="col-md-4 col-lg-3">
                <div class="py-3">
                    <img src="{{asset('assets/img/gst_logo0.png')}}" class="img-responsive" height="80" width="80" />
                </div>
                <div>
                    <p class="lead">GST Reports</p>
                    <p class="secondary-color">
                        GSTR1, GSTR2, GSTR3B, B2B, B2CS, HSN wise reports are
                        available. Also offline CSV and excel formats for GSTR1 are
                        available.
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="py-3">
                    <img src="{{asset('assets/img/accounting_logo0.png')}}" class="img-responsive" height="80"
                        width="80" />
                </div>
                <div>
                    <p class="lead">Accounting</p>
                    <p class="secondary-color">
                        Ledger, Sundry Debtors, Sundry Credtors, Trial balance,
                        Double voucher entry, Cash deposit, Sale receipt, Purchase
                        receipt etc are available.
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="py-3">
                    <img src="{{asset('assets/img/stock_report_logo0.jpg')}}" class="img-responsive" height="80"
                        width="80" />
                </div>
                <div>
                    <p class="lead">Stock Reports</p>
                    <p class="secondary-color">
                        Depending upon the analysis of sold data or limit set for
                        any item, orders can be generated efficiently.
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 d-md-none d-lg-block">
                <div class="py-3">
                    <img src="{{asset('assets/img/notification_logo0.png')}}" class="img-responsive" height="80"
                        width="80" />
                </div>
                <div>
                    <p class="lead">Notification</p>
                    <p class="secondary-color">
                        Instant notifications can be sent to customers through SMS
                        facility provided by Medwin Softwares
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact -->
<section id="contact" class="p-4">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="md-h text-center">
                    <i style="color: var(--primary-color);" class="fa fa-address-book px-2 py-1"></i>
                    Contact Us
                </h1>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col">
                <p class="secondary-color"><i class="fa fa-phone primary-color"></i> Call Us: +91-9212705931
                    (Mr. Shravan Jaiswal)
                </p>
                <p class="secondary-color"> <i class="fa fa-envelope primary-color"></i> Email:
                    Medwinsoftware@gmail.com,
                    logicsoft@yahoo.com</p>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 col-lg-6 contact-form">
                <p class="lead secondary-color">
                    Fill out the form below and we will get back to you.
                </p>
                <p class="lead secondary-color">(*) Indicates Required</p>
                @include('inc.messages')
                @if(count($errors) > 0)
                @foreach($errors->all() as $error)
                <p class="alert alert-danger">{{ucwords($error)}}</p>
                @endforeach
                @endif
                <form action="{{route('customer.msg.submit')}}" method="POST" id="contactForm">
                    @csrf
                    <div class="form-group py-4 required-field">
                        <input required value="{{old('name')}}" name="name" id="name" type="text"
                            class="form-control my-2 p-2 input" placeholder="Name*" />
                        <label for="name" class="label">Name</label>
                    </div>
                    <div class="form-group py-4">
                        <input required value="{{old('mobile_no')}}" name="mobile_no" id="mobile_no" type="number"
                            class="form-control my-2 p-2 input" placeholder="Mobile Number*" />
                        <label for="Mobile No" class="label">Mobile No</label>
                    </div>
                    <div class="form-group py-4 required-field">
                        <input value="{{old('email')}}" id="email" name="email" type="email"
                            class="form-control my-2 p-2 input" placeholder="Email Address" />
                        <label for="email" class="label">Email Address</label>
                    </div>
                    <div class="form-group py-4">
                        <select required class="form-control select" name="type_of_soft" id="type_of_soft">
                            <option value="" class="option">Type of Software*</option>
                            <option class="option" value="retailPharma">Retail Pharma</option>
                            <option class="option" value="wholeSalePharma">Wholesale Pharma</option>
                            <option class="option" value="retailSupermarket">Retail Supermarket</option>
                            <option class="option" value="retailBookstore">Retail Bookstore</option>
                            <option class="option" value="retailFootwear">Retail Footwear</option>
                            <option class="option" value="wholeSaleFootwear">Wholesale Footwear</option>
                            <option class="option" value="retailGarment">Retail Garment</option>
                            <option class="option" value="other">Other</option>
                        </select>
                    </div>

                    <input id="submit" value="Submit" type="submit"
                        class="btn btn-success mt-3 btn-block p-2 font-weight-bold text-uppercase">
                </form>
            </div>
            <div class="col-md-12 col-lg-6 contact-image">
                <img src="{{asset('assets/img/smartmockups2.png')}}" class="img-fluid" alt="" />
            </div>
        </div>
    </div>
</section>

@endsection
@section('scripts')
@if(count($errors) > 0)
<script>
    $(document).ready(function() {
        let toggleErrors = document.querySelectorAll('.toggle-to-error');
        toggleErrors.forEach(err => {
            err.click();
        });
    });
</script>
@endif
<script>
    $(document).ready(function() {
        let submitBtn = document.getElementById('submit');
        let contactForm = document.getElementById('contactForm');
        contactForm.addEventListener('submit', e => {
            submitBtn.disabled ='true';
        });
    });
</script>
@endsection