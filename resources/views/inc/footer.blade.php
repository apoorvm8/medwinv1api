<!-- Footer -->
<footer id="footer" class="py-4 px-4 px-md-0">
    <div class="container">
        <div class="row mt-3">
            <div class="col-sm-12 col-lg-6 pt-2">
                <p>
                    Copyright &copy; 2024 Medwin Softwares, All Rights Reserved
                </p>
            </div>
            <div class="col-sm-12 col-lg-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ !(request()->is('/')) ? '/' : '#home' }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ !(request()->is('/')) ? route('contact') : '#contact' }}">Contact</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{url('sitemap.xml')}}">Sitemap</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Terms and Conditions</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</footer>