@extends('customer.layouts.app')
@section('styles')
  <style>
     /* Responsive Design */
    @media only screen and (max-width: 320px) {
        .customCardBox {
            padding-bottom: 0.6rem !important;
        }
    }

    @media only screen and (min-width: 321px) and (max-width: 541px) {
        .customCardBox {
            /* padding-bottom: 1.09rem !important; */
        }
    }

    @media only screen and (min-width: 769px) and (max-width: 1024px) {
        .customCardBox h1 {
          font-size: 1.15rem !important;
        } 
    }
  </style>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            {{-- <h4 class="m-0">Dashboard</h4> --}}
            <p><span style="font-weight:500; font-size:1.4rem;">Welcome, {{Auth::guard('customer')->user()->subdesc}},</span>
              <br><span style="font-size:1rem;">{{Auth::guard('customer')->user()->subadd1}}, {{Auth::guard('customer')->user()->subadd2}} - {{Auth::guard('customer')->user()->subadd3}}</span>
            </p>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                  <a href="{{route('customer.backup')}}" class="small-box-footer" style="text-decoration: none; color:white;">
                    <div class="text-center inner customCardBox">
                      <h1 class="h3 d-none d-md-block mt-1" style="font-weight:300;">Backup</h1>
                      <h5 class="d-block d-md-none" style="font-size: 1.1rem;font-weight:400;">Backup</h5>
                        <i class="d-none d-md-block fas fa-database fa-3x"></i>
                        <i class="d-block d-md-none fas fa-database fa-2x"></i>
                    </div>
                    <hr>
                    Go <i class="fas fa-arrow-circle-right"></i>
                  </a>
                </div>
            </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection