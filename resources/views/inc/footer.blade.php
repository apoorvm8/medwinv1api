<footer id="footer" class="site-footer">
    <div class="container site-container">
        <div class="row footer-main">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <a class="brand-lockup brand-lockup--footer" href="{{ route('index') }}" aria-label="Medwin Softwares home">
                    <span class="brand-mark" aria-hidden="true">M</span>
                    <span class="brand-copy">
                        <strong>Medwin</strong>
                        <small>Softwares</small>
                    </span>
                </a>
                <p class="footer-intro mt-4">
                    Practical software for billing, stock, purchases, GST reports and accounts across retail and wholesale businesses.
                </p>
                <button type="button" class="btn btn-outline-light btn-sm mt-2" data-toggle="modal" data-target="#customerLoginModal">
                    <i class="far fa-user mr-2" aria-hidden="true"></i>Customer login
                </button>
            </div>

            <div class="col-6 col-md-4 col-lg-2 mb-4 mb-lg-0">
                <h2 class="footer-heading">Solutions</h2>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('pharmacy') }}">Pharmacy</a></li>
                    <li><a href="{{ route('departmental') }}">Departmental store</a></li>
                    <li><a href="{{ route('bookstore') }}">Bookstore</a></li>
                    <li><a href="{{ route('footwear') }}">Footwear</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-4 col-lg-2 mb-4 mb-lg-0">
                <h2 class="footer-heading">Company</h2>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('index') }}#why-medwin">Why Medwin</a></li>
                    <li><a href="{{ route('index') }}#features">Features</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div class="col-md-4 col-lg-3">
                <h2 class="footer-heading">Get in touch</h2>
                <ul class="list-unstyled footer-contact">
                    <li>
                        <i class="fas fa-phone-alt" aria-hidden="true"></i>
                        <a href="tel:+919212705931">+91 92127 05931</a>
                    </li>
                    <li>
                        <i class="far fa-envelope" aria-hidden="true"></i>
                        <a href="mailto:medwinsoftware@gmail.com">medwinsoftware@gmail.com</a>
                    </li>
                    <li>
                        <i class="far fa-envelope" aria-hidden="true"></i>
                        <a href="mailto:logicsoft@yahoo.com">logicsoft@yahoo.com</a>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        <span>New Delhi, India</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom d-md-flex align-items-center justify-content-between">
            <p class="mb-2 mb-md-0">&copy; {{ now()->year }} Medwin Softwares. All rights reserved.</p>
            <div class="footer-legal">
                <a href="{{ url('sitemap.xml') }}">Sitemap</a>
            </div>
        </div>
    </div>
</footer>
