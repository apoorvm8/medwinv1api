<!-- Sidebar -->
<nav id="sidebar" class="d-lg-none d-block">
    <div id="dismiss">
        <i class="fa fa-times fa-2x mt-4"></i>
    </div>
    <div class="sidebar-header">
        <h3>Medwin Softwares</h3>
    </div>

    <ul class="list-unstyled components">
        <!-- <p>Dummy Heading</p> -->
        <li class="{{request()->is('/') ? 'active' : ''}}">
            <a id="homeAnchor" href="{{ !(request()->is('/')) ? '/' : '#home' }}"><i class="fa fa-home"></i>
                Home</a>
        </li>
        <li>
            <a href="#retailSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                    class="fa fa-shopping-cart"></i> Retail</a>
            <ul class="collapse list-unstyled" id="retailSubmenu">
                <li>
                    <a href="{{route('pharmacy')}}"><i class="fa fa-prescription-bottle-alt"></i> Pharma</a>
                </li>
                <li>
                    <a href="{{route('bookstore')}}"><i class="fa fa-book"></i> Bookstore</a>
                </li>
                <li>
                    <a href="{{route('departmental')}}"><i class="fa fa-shopping-basket"></i> Departmental</a>
                </li>
                <li>
                    <a href="{{route('footwear')}}"><i class="fa fa-socks"></i> Footwear</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="#wholesaleSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i
                    class="fa fa-truck"></i> Wholesale</a>
            <ul class="collapse list-unstyled" id="wholesaleSubmenu">
                <li>
                    <a href="{{route('pharmacy')}}"><i class="fa fa-prescription-bottle-alt"></i> Pharma</a>
                </li>
                <li>
                    <a href="{{route('bookstore')}}"><i class="fa fa-book"></i> Bookstore</a>
                </li>
                <li>
                    <a href="{{route('departmental')}}"><i class="fa fa-shopping-basket"></i> Departmental</a>
                </li>
                <li>
                    <a href="{{route('footwear')}}"><i class="fa fa-socks"></i> Footwear</a>
                </li>
            </ul>
        </li>
        {{-- <li class="{{request()->is('downloads') ? 'active' : ''}}">
            <a class="nav-link" href="{{route('downloads')}}"><i class="fa fa-download"></i> Download</a>
        </li> --}}
        <li class="{{request()->is('contact') ? 'active' : ''}}">
            <a id="contactAnchor" href="{{ !(request()->is('/')) ? route('contact') : '#contact' }}"><i
                    class="fa fa-address-book"></i> Contact</a>
        </li>
        <hr>
        {{-- <li class="nav-item my-auto">
            <a data-toggle="modal" data-target="#customerLoginModal" class="btn btn-primary btn-sm w-50 ml-2 px-0 pb-2 pt-1"
                href="javascript:void(0);"><i class="fa fa-user"></i> Login
            </a>
        </li> --}}
    </ul>
</nav>

<nav id="navbar" class="navbar sticky-top navbar-expand-lg pt-3 d-lg-block d-none">
    <div class="container">
        <a class="navbar-brand" href="/">Medwin Softwares</a>
        <button class="navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item {{request()->is('/') ? 'active' : ''}}">
                    <a class="nav-link mr-3" href="{{ !(request()->is('/')) ? '/' : '#home' }}">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Retail
                    </a>
                    <div class="dropdown-menu slideIn" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{route('pharmacy')}}">Pharma</a>
                        <a class="dropdown-item" href="{{route('bookstore')}}">Bookstore</a>
                        <a class="dropdown-item" href="{{route('departmental')}}">Departmental</a>
                        <a class="dropdown-item" href="{{route('footwear')}}">Footwear</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Wholesale
                    </a>
                    <div class="dropdown-menu slideIn" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{route('pharmacy')}}">Pharma</a>
                        <a class="dropdown-item" href="{{route('bookstore')}}">Bookstore</a>
                        <a class="dropdown-item" href="{{route('departmental')}}">Departmental</a>
                        <a class="dropdown-item" href="{{route('footwear')}}">Footwear</a>
                    </div>
                </li>

                {{-- <li class="nav-item {{request()->is('downloads') ? 'active' : ''}}">
                    <a class="nav-link" href="{{route('downloads')}}">Download</a>
                </li> --}}
                <li class="nav-item mr-3 {{ request()->is('contact') ? 'active' : ''}}">
                    <a class="toggle-to-error nav-link"
                        href="{{ !(request()->is('/')) ? route('contact') : '#contact' }}">Contact</a>
                </li>
                {{-- <li class="nav-item my-auto">
                    <a data-toggle="modal" data-target="#customerLoginModal" class="btn btn-primary btn-sm"
                        href="javascript:void(0);"><i class="fa fa-user mr-1"></i> Login
                    </a>
                </li> --}}
            </ul>
        </div>
    </div>
</nav>