@php
    $isProductPage = request()->routeIs('pharmacy', 'bookstore', 'departmental', 'footwear');
    $isWholesale = request()->query('segment') === 'wholesale';
    $productContactTypes = [
        'pharmacy' => $isWholesale ? 'wholeSalePharma' : 'retailPharma',
        'bookstore' => 'retailBookstore',
        'departmental' => 'retailSupermarket',
        'footwear' => $isWholesale ? 'wholeSaleFootwear' : 'retailFootwear',
    ];
    $currentRouteName = optional(request()->route())->getName();
    $contactUrl = request()->routeIs('index')
        ? '#contact'
        : ($isProductPage
            ? route('contact', ['type_of_soft' => $productContactTypes[$currentRouteName]])
            : route('contact'));
    $whyMedwinUrl = request()->routeIs('index') ? '#why-medwin' : route('index') . '#why-medwin';
@endphp

<div class="site-topbar d-none d-lg-block">
    <div class="container site-container">
        <div class="d-flex align-items-center justify-content-between">
            <p class="mb-0"><i class="fas fa-map-marker-alt mr-2" aria-hidden="true"></i>Pharmacy and retail software for Indian businesses</p>
            <div class="site-topbar__links d-flex align-items-center">
                <a href="tel:+919212705931"><i class="fas fa-phone-alt mr-2" aria-hidden="true"></i>+91 92127 05931</a>
                <a href="mailto:medwinsoftware@gmail.com"><i class="far fa-envelope mr-2" aria-hidden="true"></i>medwinsoftware@gmail.com</a>
            </div>
        </div>
    </div>
</div>

<nav id="navbar" class="navbar navbar-expand-lg navbar-light site-navbar sticky-top" aria-label="Main navigation">
    <div class="container site-container">
        <a class="navbar-brand brand-lockup" href="{{ route('index') }}" aria-label="Medwin Softwares home">
            <span class="brand-mark" aria-hidden="true">M</span>
            <span class="brand-copy">
                <strong>Medwin</strong>
                <small>Softwares</small>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#siteNavigation"
            aria-controls="siteNavigation" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="siteNavigation">
            <ul class="navbar-nav ml-auto align-items-lg-center">
                <li class="nav-item {{ request()->routeIs('index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('index') }}">Home</a>
                </li>

                <li class="nav-item dropdown {{ $isProductPage && !$isWholesale ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" id="retailDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Retail</a>
                    <div class="dropdown-menu" aria-labelledby="retailDropdown">
                        <span class="dropdown-label">Retail solutions</span>
                        <a class="dropdown-item" href="{{ route('pharmacy') }}">
                            <span class="dropdown-icon"><i class="fas fa-prescription-bottle-alt" aria-hidden="true"></i></span>
                            <span><strong>Pharmacy</strong><small>Batch, expiry and billing</small></span>
                        </a>
                        <a class="dropdown-item" href="{{ route('departmental') }}">
                            <span class="dropdown-icon"><i class="fas fa-shopping-basket" aria-hidden="true"></i></span>
                            <span><strong>Departmental store</strong><small>Barcode, stock and reports</small></span>
                        </a>
                        <a class="dropdown-item" href="{{ route('bookstore') }}">
                            <span class="dropdown-icon"><i class="fas fa-book-open" aria-hidden="true"></i></span>
                            <span><strong>Bookstore</strong><small>ISBN, titles and publishers</small></span>
                        </a>
                        <a class="dropdown-item" href="{{ route('footwear') }}">
                            <span class="dropdown-icon"><i class="fas fa-shoe-prints" aria-hidden="true"></i></span>
                            <span><strong>Footwear</strong><small>Style, size and colour</small></span>
                        </a>
                    </div>
                </li>

                <li class="nav-item dropdown {{ $isProductPage && $isWholesale ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" id="wholesaleDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Wholesale</a>
                    <div class="dropdown-menu" aria-labelledby="wholesaleDropdown">
                        <span class="dropdown-label">Wholesale solutions</span>
                        <a class="dropdown-item" href="{{ route('pharmacy', ['segment' => 'wholesale']) }}">
                            <span class="dropdown-icon"><i class="fas fa-capsules" aria-hidden="true"></i></span>
                            <span><strong>Pharmacy</strong><small>Purchases, stock and orders</small></span>
                        </a>
                        <a class="dropdown-item" href="{{ route('footwear', ['segment' => 'wholesale']) }}">
                            <span class="dropdown-icon"><i class="fas fa-box-open" aria-hidden="true"></i></span>
                            <span><strong>Footwear</strong><small>Variant-wise stock control</small></span>
                        </a>
                    </div>
                </li>

                <li class="nav-item d-lg-none d-xl-block">
                    <a class="nav-link" href="{{ $whyMedwinUrl }}">Why Medwin</a>
                </li>

                <li class="nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ $contactUrl }}">Contact</a>
                </li>

                <li class="nav-item nav-action nav-action--login">
                    <button type="button" class="btn btn-link nav-login" data-toggle="modal" data-target="#customerLoginModal">
                        <i class="far fa-user mr-2" aria-hidden="true"></i>Customer login
                    </button>
                </li>

                <li class="nav-item nav-action">
                    <a class="btn btn-primary btn-sm site-nav-cta" href="{{ $contactUrl }}">Request a demo</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
