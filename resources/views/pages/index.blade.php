@extends('layouts.app')

@section('content')
    <section class="hero-section position-relative overflow-hidden">
        <div class="hero-glow hero-glow--one" aria-hidden="true"></div>
        <div class="hero-glow hero-glow--two" aria-hidden="true"></div>

        <div class="container site-container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-copy">
                    <span class="section-eyebrow section-eyebrow--light">
                        <span class="eyebrow-dot" aria-hidden="true"></span>Built for Indian pharmacies
                    </span>

                    <h1 class="hero-title">
                        <span class="hero-title__primary">Pharmacy Billing,</span>
                        <span class="hero-title__wide">Inventory and Accounts</span>
                        <span>Made Simple.</span>
                    </h1>

                    <p class="hero-lead">
                        Manage billing, batch-wise stock, expiry, purchases, GST reports and accounts for retail and wholesale pharmacies.
                    </p>

                    <div class="hero-actions d-sm-flex align-items-center">
                        <a href="#contact" class="btn btn-light btn-lg mr-sm-3 mb-3 mb-sm-0">
                            Request a demo<i class="fas fa-arrow-right ml-2" aria-hidden="true"></i>
                        </a>
                        <a href="#solutions" class="btn btn-outline-light btn-lg">Explore solutions</a>
                    </div>

                    <div class="hero-checks d-flex flex-wrap">
                        <span><i class="fas fa-check" aria-hidden="true"></i>Retail and wholesale pharmacy</span>
                        <span><i class="fas fa-check" aria-hidden="true"></i>Batch and expiry control</span>
                        <span><i class="fas fa-check" aria-hidden="true"></i>GST billing and reports</span>
                    </div>
                </div>

                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="hero-visual">
                        <div class="hero-visual__halo" aria-hidden="true"></div>
                        <div class="windows-laptop hero-windows-laptop">
                            <div class="windows-laptop__lid">
                                <span class="windows-laptop__camera" aria-hidden="true"></span>
                                <div class="windows-laptop__display">
                                    <img src="{{ asset('assets/img/pharmacy_soft0.jpg') }}"
                                        class="windows-laptop__image hero-windows-laptop__image"
                                        alt="Medwin pharmacy billing software displayed on a Windows laptop">
                                </div>
                                <span class="windows-laptop__brand" aria-hidden="true">
                                    <i class="fab fa-windows"></i> Windows
                                </span>
                            </div>
                            <div class="windows-laptop__base" aria-hidden="true"></div>
                            <div class="floating-proof floating-proof--top">
                                <span class="floating-proof__icon"><i class="fas fa-file-invoice" aria-hidden="true"></i></span>
                                <span><strong>GST billing</strong><small>Clear invoice workflow</small></span>
                            </div>

                            <div class="floating-proof floating-proof--bottom">
                                <span class="floating-proof__icon"><i class="fas fa-boxes" aria-hidden="true"></i></span>
                                <span><strong>Stock control</strong><small>Batch, expiry and quantity</small></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="proof-strip" aria-label="Medwin software highlights">
        <div class="container site-container">
            <div class="row no-gutters proof-strip__grid">
                <div class="col-6 col-lg-3 proof-item">
                    <div class="proof-item__inner">
                        <span class="proof-item__icon proof-item__icon--blue" aria-hidden="true"><i class="fas fa-award"></i></span>
                        <div class="proof-item__copy">
                            <strong>20+ years</strong>
                            <span>of pharmacy software experience</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 proof-item proof-item--customers">
                    <div class="proof-item__inner">
                        <span class="proof-item__icon proof-item__icon--teal" aria-hidden="true"><i class="fas fa-users"></i></span>
                        <div class="proof-item__copy">
                            <strong>1,500+ satisfied customers</strong>
                            <span>across India</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 proof-item">
                    <div class="proof-item__inner">
                        <span class="proof-item__icon proof-item__icon--violet" aria-hidden="true"><i class="fas fa-file-invoice"></i></span>
                        <div class="proof-item__copy">
                            <strong>GST billing</strong>
                            <span>invoices and business reports</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 proof-item">
                    <div class="proof-item__inner">
                        <span class="proof-item__icon proof-item__icon--amber" aria-hidden="true"><i class="fas fa-prescription-bottle-alt"></i></span>
                        <div class="proof-item__copy">
                            <strong>Made practical</strong>
                            <span>for familiar Indian pharmacy workflows</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="solutions" class="section-space solutions-section">
        <div class="container site-container">
            <div class="row align-items-end mb-5">
                <div class="col-lg-7">
                    <span class="section-eyebrow">Solutions by trade</span>
                    <h2 class="section-title">Pharmacy software at the core, with solutions for other trades.</h2>
                </div>
                <div class="col-lg-5">
                    <p class="section-intro mb-0">
                        Medwin is built around detailed pharmacy workflows, while the same billing and accounts foundation supports other retail businesses.
                    </p>
                </div>
            </div>

            <div class="row solution-grid">
                <div class="col-md-6 col-xl-3 mb-4">
                    <a href="{{ route('pharmacy') }}" class="solution-card solution-card--pharma h-100">
                        <span class="solution-number">01</span>
                        <span class="icon-box"><i class="fas fa-prescription-bottle-alt" aria-hidden="true"></i></span>
                        <h3>Pharmacy</h3>
                        <p>Billing, batch-wise stock, expiry tracking, purchases, returns and GST reports.</p>
                        <span class="card-link">Explore pharmacy software<i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
                </div>

                <div class="col-md-6 col-xl-3 mb-4">
                    <a href="{{ route('departmental') }}" class="solution-card solution-card--store h-100">
                        <span class="solution-number">02</span>
                        <span class="icon-box"><i class="fas fa-shopping-basket" aria-hidden="true"></i></span>
                        <h3>Departmental store</h3>
                        <p>Fast barcode billing, purchase control, stock, returns and profit reports.</p>
                        <span class="card-link">Explore store software<i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
                </div>

                <div class="col-md-6 col-xl-3 mb-4">
                    <a href="{{ route('bookstore') }}" class="solution-card solution-card--books h-100">
                        <span class="solution-number">03</span>
                        <span class="icon-box"><i class="fas fa-book-open" aria-hidden="true"></i></span>
                        <h3>Bookstore</h3>
                        <p>ISBN search with author, publisher and genre-wise stock and sales reports.</p>
                        <span class="card-link">Explore bookstore software<i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
                </div>

                <div class="col-md-6 col-xl-3 mb-4">
                    <a href="{{ route('footwear') }}" class="solution-card solution-card--footwear h-100">
                        <span class="solution-number">04</span>
                        <span class="icon-box"><i class="fas fa-shoe-prints" aria-hidden="true"></i></span>
                        <h3>Footwear</h3>
                        <p>Control stock and reports across every style, colour and size combination.</p>
                        <span class="card-link">Explore footwear software<i class="fas fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="product-showcase" class="section-space software-showcase-section">
        <div class="container site-container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-5 mb-lg-0">
                    <div class="windows-laptop">
                        <div class="windows-laptop__lid">
                            <span class="windows-laptop__camera" aria-hidden="true"></span>
                            <div class="windows-laptop__display">
                                <img src="{{ asset('assets/img/purchase-screen.png') }}" class="windows-laptop__image"
                                    alt="Medwin purchase entry screen showing invoice rates, stock quantities and GST details" loading="lazy">
                            </div>
                            <span class="windows-laptop__brand" aria-hidden="true">
                                <i class="fab fa-windows"></i> Windows
                            </span>
                        </div>
                        <div class="windows-laptop__base" aria-hidden="true"></div>
                    </div>
                </div>

                <div class="col-lg-5 pl-lg-5">
                    <span class="section-eyebrow">Built for the working day</span>
                    <h2 class="section-title">Every important detail stays close to the transaction.</h2>
                    <p class="section-intro">
                        From the first purchase entry to the final sale, Medwin keeps stock movement, rates, tax and reports connected.
                    </p>

                    <ul class="feature-check-list list-unstyled mt-4">
                        <li><span><i class="fas fa-check" aria-hidden="true"></i></span>Quick access to billing and item information</li>
                        <li><span><i class="fas fa-check" aria-hidden="true"></i></span>Purchase, return and order workflows</li>
                        <li><span><i class="fas fa-check" aria-hidden="true"></i></span>Stock, sales and profit visibility</li>
                        <li><span><i class="fas fa-check" aria-hidden="true"></i></span>GST reports and accounting records</li>
                    </ul>

                    <a href="#features" class="text-link mt-3">See core features<i class="fas fa-arrow-down ml-2" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="section-space features-section">
        <div class="container site-container">
            <div class="text-center section-heading-block mx-auto mb-5">
                <span class="section-eyebrow">Core capabilities</span>
                <h2 class="section-title">The daily tools your business depends on.</h2>
                <p class="section-intro">Simple to understand, detailed where it matters, and ready for busy business workflows.</p>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <article class="feature-card feature-card--blue h-100">
                        <span class="icon-box icon-box--blue"><i class="fas fa-cash-register" aria-hidden="true"></i></span>
                        <h3>Fast billing</h3>
                        <p>Create clear bills with item rates, quantity, discounts and tax details in one view.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <article class="feature-card feature-card--teal h-100">
                        <span class="icon-box icon-box--teal"><i class="fas fa-boxes" aria-hidden="true"></i></span>
                        <h3>Stock control</h3>
                        <p>Track stock movement and review the reports needed for purchase and sales decisions.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <article class="feature-card feature-card--violet h-100">
                        <span class="icon-box icon-box--violet"><i class="fas fa-truck-loading" aria-hidden="true"></i></span>
                        <h3>Purchase management</h3>
                        <p>Record purchases, import purchase data and manage returns with complete item details.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <article class="feature-card feature-card--amber h-100">
                        <span class="icon-box icon-box--amber"><i class="fas fa-file-invoice" aria-hidden="true"></i></span>
                        <h3>GST reports</h3>
                        <p>Keep GST billing and business reports organised for easier review and preparation.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <article class="feature-card feature-card--rose h-100">
                        <span class="icon-box icon-box--rose"><i class="fas fa-calculator" aria-hidden="true"></i></span>
                        <h3>Accounts</h3>
                        <p>Maintain ledgers, receipts, vouchers, balances and connected transaction records.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <article class="feature-card feature-card--navy h-100">
                        <span class="icon-box icon-box--navy"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
                        <h3>Business reports</h3>
                        <p>Review sales, purchases, stock and profit information with practical trade-wise reports.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space workflow-section">
        <div class="container site-container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <span class="section-eyebrow section-eyebrow--light">Connected workflow</span>
                    <h2 class="section-title text-white">From purchase to sale, keep every movement in view.</h2>
                </div>
                <div class="col-lg-5 ml-lg-auto">
                    <p class="section-intro text-white-50 mb-0">A clear flow helps your team work quickly while keeping the records needed at the back office.</p>
                </div>
            </div>

            <div class="row workflow-grid">
                <div class="col-md-4 mb-4 mb-md-0">
                    <article class="workflow-card h-100">
                        <span class="workflow-step">01</span>
                        <span class="icon-box"><i class="fas fa-file-import" aria-hidden="true"></i></span>
                        <h3>Record purchases</h3>
                        <p>Bring item, supplier, rate and tax details into your stock records.</p>
                    </article>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <article class="workflow-card h-100">
                        <span class="workflow-step">02</span>
                        <span class="icon-box"><i class="fas fa-warehouse" aria-hidden="true"></i></span>
                        <h3>Control inventory</h3>
                        <p>Track product movement with the industry details your business needs.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="workflow-card h-100">
                        <span class="workflow-step">03</span>
                        <span class="icon-box"><i class="fas fa-receipt" aria-hidden="true"></i></span>
                        <h3>Bill and review</h3>
                        <p>Complete the sale and use connected reports to understand performance.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section id="why-medwin" class="section-space why-section">
        <div class="container site-container">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <span class="section-eyebrow">Why Medwin</span>
                    <h2 class="section-title">Practical software built for pharmacy counters.</h2>
                    <p class="section-intro">
                        Pharmacy is at the heart of Medwin, from batch and expiry tracking to purchases, billing, GST reports and accounts. Other trades use the same dependable foundation.
                    </p>
                    <div class="experience-note d-flex align-items-center mt-4">
                        <strong>20+</strong>
                        <span>years focused on business software</span>
                    </div>
                </div>

                <div class="col-lg-6 ml-lg-auto">
                    <div class="why-list">
                        <article class="why-item">
                            <span class="why-item__number">01</span>
                            <div>
                                <h3>Trade-specific details</h3>
                                <p>Batch and expiry for pharmacy, ISBN for books, and variants for footwear.</p>
                            </div>
                        </article>
                        <article class="why-item">
                            <span class="why-item__number">02</span>
                            <div>
                                <h3>Clear daily workflows</h3>
                                <p>Purchase, stock, billing and reporting stay connected in a familiar desktop system.</p>
                            </div>
                        </article>
                        <article class="why-item">
                            <span class="why-item__number">03</span>
                            <div>
                                <h3>Useful business visibility</h3>
                                <p>Review sales, purchase, stock and profit reports alongside accounting records.</p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section-space contact-section">
        <div class="container site-container">
            <div class="contact-shell">
                <div class="row no-gutters">
                    <div class="col-lg-5 contact-copy-panel">
                        <div class="contact-copy-panel__inner">
                            <span class="section-eyebrow section-eyebrow--light">Let's talk</span>
                            <h2>Tell us what you need from your business software.</h2>
                            <p>Share your details and the type of software you are looking for. Our team will contact you.</p>

                            <div class="contact-direct">
                                <a href="tel:+919212705931">
                                    <span><i class="fas fa-phone-alt" aria-hidden="true"></i></span>
                                    <div><small>Call Mr. Shrawan Jaiswal</small><strong>+91 92127 05931</strong></div>
                                </a>
                                <a href="mailto:medwinsoftware@gmail.com">
                                    <span><i class="far fa-envelope" aria-hidden="true"></i></span>
                                    <div><small>Email us</small><strong>medwinsoftware@gmail.com</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 contact-form-panel">
                        <div class="contact-form-panel__inner">
                            <h3>Request a demo</h3>
                            <p class="mb-4">Fields marked with * are required.</p>
                            @include('inc.contact-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        (function () {
            var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduceMotion || !('IntersectionObserver' in window)) {
                return;
            }

            var revealItems = [];

            function prepareReveal(selector, variant, stagger) {
                document.querySelectorAll(selector).forEach(function (element, index) {
                    element.classList.add('home-reveal');
                    if (variant) {
                        element.classList.add('home-reveal--' + variant);
                    }
                    if (stagger) {
                        element.style.setProperty('--reveal-delay', Math.min(index, 5) * stagger + 'ms');
                    }
                    revealItems.push(element);
                });
            }

            prepareReveal('.hero-copy > .section-eyebrow, .hero-copy > .hero-title, .hero-copy > .hero-lead, .hero-copy > .hero-actions, .hero-copy > .hero-checks', 'up', 65);
            prepareReveal('.hero-visual', 'right', 0);
            prepareReveal('.proof-strip__grid', 'scale', 0);
            prepareReveal('.solutions-section .row.align-items-end', 'up', 0);
            prepareReveal('.solution-card', 'up', 60);
            prepareReveal('.software-showcase-section .row', 'up', 0);
            prepareReveal('.features-section .section-heading-block', 'up', 0);
            prepareReveal('.feature-card', 'up', 55);
            prepareReveal('.workflow-section > .container > .row:first-child', 'up', 0);
            prepareReveal('.workflow-card', 'up', 65);
            prepareReveal('.why-section .col-lg-5', 'up', 0);
            prepareReveal('.why-item', 'up', 65);
            prepareReveal('.contact-shell', 'scale', 0);

            document.documentElement.classList.add('motion-ready');

            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    var element = entry.target;
                    observer.unobserve(element);
                    element.addEventListener('transitionend', function () {
                        element.classList.remove('home-reveal', 'home-reveal--up', 'home-reveal--right', 'home-reveal--scale', 'is-visible');
                        element.style.removeProperty('--reveal-delay');
                    }, { once: true });

                    window.requestAnimationFrame(function () {
                        element.classList.add('is-visible');
                    });
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -40px 0px'
            });

            revealItems.forEach(function (element) {
                observer.observe(element);
            });
        }());
    </script>

    @if ($errors->any())
        <script>
            $(function () {
                var contactSection = document.getElementById('contact');
                if (contactSection) {
                    $('html, body').animate({
                        scrollTop: $(contactSection).offset().top - 88
                    }, 350, function () {
                        $('.contact-form-panel .alert').first().attr('tabindex', '-1').trigger('focus');
                    });
                }
            });
        </script>
    @endif
@endsection
