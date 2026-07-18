@php
    $contactType = $product['contactType'];
    if (request()->query('segment') === 'wholesale') {
        $contactType = [
            'retailPharma' => 'wholeSalePharma',
            'retailFootwear' => 'wholeSaleFootwear',
        ][$contactType] ?? $contactType;
    }
    $preview = $product['preview'] ?? null;
@endphp

<header class="product-hero py-5">
    <div class="container site-container py-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-6 pr-lg-5">
                <p class="section-eyebrow mb-3">{{ $product['eyebrow'] }}</p>
                <h1 class="display-title mb-4">{{ $product['title'] }}</h1>
                <p class="lead text-muted mb-4">{{ $product['intro'] }}</p>

                <div class="d-flex flex-column flex-sm-row align-items-sm-center">
                    <a href="{{ route('contact', ['type_of_soft' => $contactType]) }}"
                        class="btn btn-primary btn-lg px-4 mb-3 mb-sm-0 mr-sm-3">
                        Request a demo
                    </a>
                    <a href="#product-capabilities" class="btn btn-outline-primary btn-lg px-4">
                        Explore features
                    </a>
                </div>
            </div>

            <div class="col-lg-6 mt-5 mt-lg-0">
                @if ($product['imageApproved'] ?? false)
                    <div class="windows-laptop product-page-laptop">
                        <div class="windows-laptop__lid">
                            <span class="windows-laptop__camera" aria-hidden="true"></span>
                            <div class="windows-laptop__display">
                                <a href="{{ asset($product['image']) }}" data-lightbox="{{ $product['lightbox'] }}"
                                    data-title="{{ $product['imageTitle'] }}" class="d-block">
                                    <img src="{{ asset($product['image']) }}"
                                        class="windows-laptop__image product-page-laptop__image{{ ($product['imageFit'] ?? 'cover') === 'contain' ? ' product-page-laptop__image--contain' : '' }}"
                                        alt="{{ $product['imageAlt'] }}">
                                </a>
                            </div>
                            <span class="windows-laptop__brand" aria-hidden="true">
                                <i class="fab fa-windows"></i> Windows
                            </span>
                        </div>
                        <div class="windows-laptop__base" aria-hidden="true"></div>
                    </div>
                @elseif ($preview)
                    <div class="product-screen">
                        <div class="product-screen__bar d-flex align-items-center justify-content-between px-3 py-2">
                            <span>{{ $product['screenLabel'] }}</span>
                            <span class="small text-muted">Medwin Softwares</span>
                        </div>
                        <div class="product-ui-preview" role="img" aria-label="{{ $product['imageAlt'] }}">
                            <div class="preview-summary d-flex align-items-center justify-content-between">
                                <div>
                                    <small>{{ $preview['eyebrow'] }}</small>
                                    <strong>{{ $preview['title'] }}</strong>
                                </div>
                                <span class="preview-mode">LOCAL</span>
                            </div>

                            <div class="preview-grid">
                                <div class="preview-grid__row preview-grid__head">
                                    @foreach ($preview['columns'] as $column)
                                        <span>{{ $column }}</span>
                                    @endforeach
                                </div>
                                @foreach ($preview['rows'] as $row)
                                    <div class="preview-grid__row">
                                        @foreach ($row as $cell)
                                            <span>{{ $cell }}</span>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <div class="preview-total d-flex align-items-center justify-content-between">
                                <span>{{ $preview['totalLabel'] }}</span>
                                <strong>{{ $preview['totalValue'] }}</strong>
                            </div>
                            <small class="preview-note">Illustrative workflow preview with demo data</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>

<section id="product-capabilities" class="py-5">
    <div class="container site-container py-lg-4">
        <div class="row mb-4">
            <div class="col-lg-8">
                <p class="section-eyebrow mb-2">Built around your daily work</p>
                <h2 class="section-heading mb-3">{{ $product['capabilityTitle'] }}</h2>
                <p class="text-muted mb-0">{{ $product['capabilityIntro'] }}</p>
            </div>
        </div>

        <div class="row">
            @foreach($product['capabilities'] as $capability)
                <div class="col-md-6 col-lg-3 mb-4">
                    <article class="capability-card h-100 p-4">
                        <div class="icon-box d-flex align-items-center justify-content-center mb-4">
                            <i class="{{ $capability['icon'] }}" aria-hidden="true"></i>
                        </div>
                        <h3 class="h5 mb-3">{{ $capability['title'] }}</h3>
                        <p class="text-muted mb-0">{{ $capability['description'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="product-process-section bg-light py-5">
    <div class="container site-container py-lg-4">
        <div class="row product-process-heading justify-content-center text-center mb-5">
            <div class="col-lg-8">
                <p class="section-eyebrow mb-2">A clearer way to work</p>
                <h2 class="section-heading mb-3">{{ $product['processTitle'] }}</h2>
                <p class="text-muted mb-0">{{ $product['processIntro'] }}</p>
            </div>
        </div>

        <div class="row">
            @foreach($product['process'] as $step)
                <div class="col-md-4 mb-4 mb-md-0">
                    <article class="process-card h-100 p-4">
                        <span class="section-eyebrow d-block mb-3">0{{ $loop->iteration }}</span>
                        <h3 class="h5 mb-3">{{ $step['title'] }}</h3>
                        <p class="text-muted mb-0">{{ $step['description'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="product-cta-section py-5">
    <div class="container site-container">
        <div class="cta-panel p-4 p-lg-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <p class="section-eyebrow mb-2">Talk to Medwin</p>
                    <h2 class="section-heading mb-3">{{ $product['ctaTitle'] }}</h2>
                    <p class="text-muted mb-0">{{ $product['ctaText'] }}</p>
                </div>
                <div class="col-lg-4 text-lg-right mt-4 mt-lg-0">
                    <a href="{{ route('contact', ['type_of_soft' => $contactType]) }}"
                        class="btn btn-primary btn-lg px-4">
                        Request a demo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
