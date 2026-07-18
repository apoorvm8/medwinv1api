@extends('layouts.app')

@section('content')
    <section class="page-hero contact-page-hero">
        <div class="page-hero__glow" aria-hidden="true"></div>
        <div class="container site-container position-relative">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <span class="section-eyebrow section-eyebrow--light">Let's talk</span>
                    <h1 class="display-title text-white">Tell us what you need from your business software.</h1>
                    <p class="page-hero__lead mx-auto">
                        Share a few details about your business. Our team will contact you to understand your billing, stock and reporting needs.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space contact-page-section">
        <div class="container site-container">
            <div class="row">
                <div class="col-lg-5 mb-5 mb-lg-0 pr-lg-5">
                    <span class="section-eyebrow">Contact Medwin</span>
                    <h2 class="section-title">Speak with a real person about your workflow.</h2>
                    <p class="section-intro">
                        Whether you run a pharmacy, departmental store, bookstore or footwear business, tell us where you need better control.
                    </p>

                    <div class="contact-info-list mt-5">
                        <a href="tel:+919212705931" class="contact-info-card">
                            <span class="icon-box icon-box--blue"><i class="fas fa-phone-alt" aria-hidden="true"></i></span>
                            <span><small>Call us</small><strong>+91 92127 05931</strong><em>Mr. Shrawan Jaiswal</em></span>
                        </a>

                        <div class="contact-info-card">
                            <span class="icon-box icon-box--teal"><i class="far fa-envelope" aria-hidden="true"></i></span>
                            <span>
                                <small>Email us</small>
                                <strong><a href="mailto:medwinsoftware@gmail.com">medwinsoftware@gmail.com</a></strong>
                                <em><a href="mailto:logicsoft@yahoo.com">logicsoft@yahoo.com</a></em>
                            </span>
                        </div>

                        <div class="contact-info-card">
                            <span class="icon-box icon-box--amber"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>
                            <span><small>Location</small><strong>New Delhi, India</strong><em>Serving retail and wholesale businesses</em></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="contact-page-form-card">
                        <div class="mb-4">
                            <span class="form-step">Enquiry form</span>
                            <h2>Request a demo</h2>
                            <p>Fields marked with * are required.</p>
                        </div>
                        @include('inc.contact-form')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-next-step pb-section">
        <div class="container site-container">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <span class="section-eyebrow">What happens next</span>
                    <h2 class="section-title mb-0">A simple conversation, focused on your business.</h2>
                </div>
                <div class="col-lg-7 ml-lg-auto">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <article class="next-step-card h-100">
                                <span>01</span>
                                <h3>Share your needs</h3>
                                <p>Choose your trade and send your contact details.</p>
                            </article>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <article class="next-step-card h-100">
                                <span>02</span>
                                <h3>Talk to our team</h3>
                                <p>Discuss your billing, stock and reporting workflow.</p>
                            </article>
                        </div>
                        <div class="col-md-4">
                            <article class="next-step-card h-100">
                                <span>03</span>
                                <h3>Review the fit</h3>
                                <p>See which Medwin solution matches your business.</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @if ($errors->any() || session('success') || session('error'))
        <script>
            $(function () {
                var formCard = document.querySelector('.contact-page-form-card');
                if (formCard) {
                    $('html, body').animate({
                        scrollTop: $(formCard).offset().top - 105
                    }, 350, function () {
                        $('.contact-page-form-card .alert').first().attr('tabindex', '-1').trigger('focus');
                    });
                }
            });
        </script>
    @endif
@endsection
