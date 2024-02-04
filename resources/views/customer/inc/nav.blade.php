<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="p-0 px-2 py-2 nav-link btn btn-sm btn-secondary text-light" href="javascript:history.back()"><i class="fa fa-arrow-left"></i> Back</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{route('customer.dashboard')}}" class="nav-link">Dashboard</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="settingDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-cog"></i> Setting(s)
        </a>
        <div class="dropdown-menu" aria-labelledby="settingDropdown">
          <a class="dropdown-item" href="{{route('customer.changepassword')}}"><i class="fa fa-key"></i> Change Password</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link text-danger font-weight-bold" data-toggle="dropdown" href="{{ route('customer.logout') }}" onclick="event.preventDefault();
        document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> EXIT
            <form id="logout-form" action="{{ route('customer.logout') }}" method="POST"
            style="display: none;">
            @csrf
            </form>
          </a>
  
      </li>
    </ul>
</nav>