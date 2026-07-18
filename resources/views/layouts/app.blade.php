<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    {!! Meta::toHtml() !!}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Medwin Softwares, India">
    <meta name="theme-color" content="#071a38">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
            "name": "Medwin Softwares",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "New Delhi",
                "addressRegion": "Delhi",
                "postalCode": "110037",
                "addressCountry": "IN"
            },
            "description": "Billing, inventory, purchase, accounting and GST reporting software for Indian retail and wholesale businesses.",
            "url": "https://www.medwinsoftwares.in",
            "logo": "https://www.medwinsoftwares.in/assets/img/main_logo.jpg"
        }
    </script>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">

    <link rel="stylesheet" href="{{ asset('bootstrap-4.4.1/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lightbox.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('DataTables/DataTables-1.10.22/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=20260718">
    @stack('styles')
</head>

<body id="home" class="marketing-site">
    <a class="skip-link" href="#main-content">Skip to main content</a>

    @include('inc.nav')

    <main id="main-content">
        @yield('content')
    </main>

    @include('inc.footer')
    @include('inc.modals.user-login')

    <button id="myBtn" class="back-to-top" type="button" aria-label="Back to top">
        <i class="fas fa-arrow-up" aria-hidden="true"></i>
    </button>

    <script src="{{ asset('DataTables/jQuery-3.3.1/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('bootstrap-4.4.1/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('DataTables/DataTables-1.10.22/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/additional-methods.min.js') }}"></script>
    <script src="{{ asset('js/jquery.numeric-min.js') }}"></script>
    <script src="{{ asset('js/helper.js') }}"></script>
    <script src="{{ asset('js/lightbox.js') }}"></script>

    <script>
        $(function () {
            $.validator.setDefaults({
                onkeyup: false,
                onclick: false,
                errorClass: 'invalid-feedback d-block',
                errorElement: 'small',
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                },
                normalizer: function (value) {
                    return $.trim(value);
                },
                errorPlacement: function (error, element) {
                    element.nextAll('.invalid-feedback').remove();
                    error.insertAfter(element);
                }
            });

            $('.numeric').numeric({
                negative: false,
                decimal: false
            });

            $('.modal').on('shown.bs.modal', function () {
                $(this).find('[autofocus]').trigger('focus');
            });

            $('#customerLoginModal').on('hidden.bs.modal', function () {
                clearForm(['customerLoginForm'], true, true);
            });

            $('#customerLoginForm').validate({
                rules: {
                    customer_id: {
                        required: true,
                        rangelength: [4, 4]
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    customer_id: {
                        required: 'Customer ID is required.',
                        rangelength: 'Customer ID must be 4 digits.'
                    },
                    password: {
                        required: 'Password is required.'
                    }
                },
                submitHandler: function (form, event) {
                    event.preventDefault();
                    clearForm(['customerLoginForm'], false, true);

                    $.ajax({
                        url: '{{ route('customer.login') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            customer_id: form.querySelector('input[name="customer_id"]').value.trim(),
                            password: form.querySelector('input[name="password"]').value.trim()
                        },
                        beforeSend: function () {
                            btnLoaderAction('customerLoginBtn', 'none', 'inline-flex', true);
                        },
                        success: function (response) {
                            btnLoaderAction('customerLoginBtn', 'inline', 'none', false);
                            if (response.success) {
                                window.location.href = response.data;
                            }
                        },
                        error: function (xhr) {
                            btnLoaderAction('customerLoginBtn', 'inline', 'none', false);
                            var response = xhr.responseJSON || {};
                            var errors = response.errors || {
                                customer_id: ['We could not sign you in. Please try again.']
                            };

                            Object.keys(errors).forEach(function (key) {
                                var field = form.querySelector('input[name="' + key + '"]');
                                if (!field) {
                                    return;
                                }

                                field.classList.add('is-invalid');
                                $(field).nextAll('.invalid-feedback').remove();
                                var feedback = document.createElement('small');
                                feedback.className = 'invalid-feedback d-block';
                                feedback.textContent = errors[key][0];
                                field.insertAdjacentElement('afterend', feedback);
                            });
                        }
                    });
                }
            });

            var contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function () {
                    if (!contactForm.checkValidity()) {
                        return;
                    }

                    var submitButton = document.getElementById('submit');
                    submitButton.disabled = true;
                    submitButton.querySelector('.submit-label').textContent = 'Sending request...';
                });
            }

            $('a[href^="#"]').on('click', function (event) {
                if (!this.hash || this.hash === '#') {
                    return;
                }

                var target = document.querySelector(this.hash);
                if (!target) {
                    return;
                }

                event.preventDefault();
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 88
                }, 550);
            });

            $('.navbar-collapse a:not(.dropdown-toggle)').on('click', function () {
                $('.navbar-collapse').collapse('hide');
            });

            var navbar = document.getElementById('navbar');
            var backToTop = document.getElementById('myBtn');

            function updateScrolledState() {
                var isScrolled = window.pageYOffset > 24;
                if (navbar) {
                    navbar.classList.toggle('is-scrolled', isScrolled);
                }
                if (backToTop) {
                    backToTop.classList.toggle('is-visible', window.pageYOffset > 500);
                }
            }

            updateScrolledState();
            window.addEventListener('scroll', updateScrolledState, { passive: true });

            if (backToTop) {
                backToTop.addEventListener('click', function () {
                    $('html, body').animate({ scrollTop: 0 }, 550);
                });
            }
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>
