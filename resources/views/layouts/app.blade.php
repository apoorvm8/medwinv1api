<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    {!! Meta::toHtml() !!}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="robots" content="index, follow">
    <meta name="author" content="Medwin Softwares, India">
    <meta name="abstract" content="www.medwinsoftwares.in ">

    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/jquery.scrollfire.min.js')}}"></script> --}}
    <script type="application/ld+json">
        { 
		  "@context" : "http://schema.org","@type" : "LocalBusiness","name" : "Medwin Softwares","address": {
            "@type": "PostalAddress","addressLocality" : "New Delhi","addressRegion" : "New Delhi","postalCode" : "110037"},"description" : "Retail Softwares, Inventory Softwares, Pharamacy Softwares, Bookstore Softwares, Footwear Softwares, Departmental Softwares, POS, Pharma POS, GST software","url" : "http://www.medwinsoftwares.in","logo" : "http://www.medwinsoftwares.in/assets/img/main_logo.jpg","openingHours": "Mo,Tu,We,Th,Fr,Sa,Su"
        } 
    </script>
    <!-- Fonts -->
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="/assets/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css?family=Lato:400,300italic,700%7cRaleway:400,300,200,500,600,700,800,900%7cRoboto:400,500"
        rel="stylesheet" type="text/css">
    <!-- Styles -->
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{asset('bootstrap-4.4.1/css/bootstrap.min.css')}}" />
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link href="{{asset('css/sideNav.css')}}" rel="stylesheet" media="screen and (max-width: 991px)">
    {{-- <link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet"> --}}
    <link href="{{asset('css/lightbox.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css" />
    <link rel="stylesheet" href="{{asset('DataTables/DataTables-1.10.22/css/jquery.dataTables.min.css')}}">
</head>

<body id="home">


    <div class="wrapper">
        <!-- Navbar -->
        @include('inc.nav')

        <!-- Content-->
        <div id="content">
            @include('inc.sideNav')
            @yield('content')
            @include('inc.footer')
        </div>
        <!-- Dark Overlay element -->
        <div class="overlay"></div>
    </div>

    @include('inc.modals.user-login')

    <script src="{{asset('DataTables/jQuery-3.3.1/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('bootstrap-4.4.1/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('DataTables/DataTables-1.10.22/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('js/additional-methods.min.js')}}"></script>
    <script src="{{asset('js/jquery.numeric-min.js')}}"></script>
    <script src="{{asset('js/helper.js')}}"></script>
    {{-- Lightbox JS --}}
    <script src="{{asset('js/lightbox.js')}}"></script>

    <!-- jQuery Custom Scroller CDN -->
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js">
    </script>
    <script>

        // Set Defaults For the jquery-validator
        $.validator.setDefaults({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            errorClass: 'text-danger font-weight-bold',
            errorElement: "small",
            highlight: function(element) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },
            normalizer: function(value) {
                return $.trim(value);
            },
            errorPlacement: function(error, element) {
                while(element[0].nextElementSibling) {
                    element[0].nextElementSibling.remove();
                }
                element[0].insertAdjacentElement('afterend', error[0]);
            }
        });

        $('.modal').on('shown.bs.modal', function() {
            $(this).find('[autofocus]').focus();
        });

        $('.modal').on('hide.bs.modal', function() {
            // Pass form by their ids
            clearForm(['customerLoginForm'], true, true);
        });

        // Numeric Plugin
        $('.numeric').numeric({
            negative: false,
            decimal: false
        });

        //jquery form validator
        $('#customerLoginForm').validate({
            rules: {
                customer_id: {
                    required: true,
                    rangelength: [4, 4]
                },
                password: {
                    required: true,
                }
            },
            messages: {
                customer_id: {
                    required: "Customer ID is required.",
                    rangelength: "Customer ID must be 4 digits"
                },
                password: {
                    required: "Password Is Required.",
                }  
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                clearForm(['customerLoginForm'], false, true);

                $.ajax({
                    url: '{!! route('customer.login') !!}',
                    method: 'POST',
                    data: {
                        _token:'{{csrf_token()}}',
                        customer_id: form.querySelector('input[name="customer_id"]').value.trim(),
                        password: form.querySelector('input[name="password"]').value.trim()
                    },
                    beforeSend: function() {
                        btnLoaderAction('customerLoginBtn', 'none', 'block', true);
                    },
                    success: function(res) {
                        btnLoaderAction('customerLoginBtn', 'block', 'none', false);
                        if(res.success) {
                            // Redirection to the retailer dashboard.
                            window.location.href= res.data;
                        }
                    },
                    error: function(res) {
                        btnLoaderAction('customerLoginBtn', 'block', 'none', false);
                        res = res.responseJSON;
                        
                        if(!res.success) {
                            let errors = res.errors;
                            Object.keys(errors).forEach(key => {
                                let formel = form.querySelector('input[name="'+key+'"]');
                                if(formel) {
                                    formel.classList.add('is-invalid');
                                    while(formel.nextElementSibling) {
                                        formel.nextElementSibling.remove();
                                    }
                                    let small = document.createElement('small');
                                    small.className = 'text-danger font-weight-bold';
                                    small.innerHTML = errors[key][0];
                                    formel.insertAdjacentElement('afterend', small);
                                }

                            });
                        }                      
                    }
                });
            }
        });

        
        $(document).ready(function() {
            //Get the button:
            mybutton = document.getElementById("myBtn");
            // When the user scrolls down 20px from the top of the document, show the button
            window.onscroll = function() {scrollFunction()};

            function scrollFunction() {
                if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                    mybutton.style.display = "block";
                } else {
                    mybutton.style.display = "none";
                }
            }

           
            if($(window).width() > 1499) {
			    $('#showcaseHighlights').addClass('customShowcase');
		    }
            $('#sidebar').mCustomScrollbar({
                theme: 'minimal',
            });

            $('#dismiss, .overlay').on('click', function () {
            // hide sidebar
            $('#sidebar').removeClass('active');
            // hide overlay
            $('.overlay').removeClass('active');
            });

            $('#sidebarCollapse').on('click', function () {
            // open sidebar
            $('#sidebar').addClass('active');
            // fade in the overlay
            $('.overlay').addClass('active');
            $('.collapse.in').toggleClass('in');
            $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            });
        });
    </script>
    @if( !(request()->is('register') || request()->is('admin/login') || request()->is('login')))
    @include('inc.effects')
    @endif
    @yield('scripts')
</body>

</html>