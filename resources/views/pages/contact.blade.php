@extends('layouts.app')

{{-- Section: Contact Form --}}
@section('content')
@if(request()->is('contact'))
<style>
    @media (min-width: 300px) and (max-width: 639px) {
        #contact-page {
            margin-bottom: 20rem;
        }
    }

    @media(min-width: 500px) and (max-width: 767px) {
        #contact-page {
            margin-bottom: 28rem;
        }
    }
</style>
@endif

<section id="contact-page" class="p-4 section-1">
    <div class="container">
        <div class="row d-lg-none">
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
                    (Mr. Shrawan Jaiswal)
                </p>
                <p class="secondary-color"> <i class="fa fa-envelope primary-color"></i> Email:
                    Medwinsoftware@gmail.com,
                    logicsoft@yahoo.com</p>
            </div>
        </div>
        <div class="row mt-0 mt-md-4">
            <div class="col-md-12 col-lg-6 contact-form-page px-sm-0 px-md-5 px-lg-0">
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
            <div class="col-md-12 col-lg-6 contact-image-page">
                <img src="{{asset('assets/img/smartmockups2.png')}}" class="img-fluid" alt="" />
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
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