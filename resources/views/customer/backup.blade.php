@extends('customer.layouts.app')
@section('styles')
  <style>
       .customVal::after {
            content: ' *';
            color: red;
            font-weight:bold;
            font-size: 1.1rem;
        }

        table th, table td {
            font-size: 0.89rem;
        }
  </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 id="orderListTitle" class="m-0"><span class="pb-md-1" style='font-size:1.3rem; border-bottom:2px solid black;'>Your Order(s)</span></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/retailer">Home</a></li>
                    <li class="breadcrumb-item active">Backup</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-sm-12 col-md-4">
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-3 mt-1">
                            <span class="font-weight-bold">Filter By:</span>
                        </div>
                        <div class="col-sm-12 col-md-3 mt-1 ml-lg-n5">
                            <span>Backup Type:</span>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <select id='backup-type' class="form-control">
                                <option value='currentyear'>Current</option>
                                <option value='lastyear'>Previous</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 col-lg-7 col-sm-12">
                    <div class="table-responsive mb-5">
                        <table id="backupOrderTable" class="table table-sm table-bordered">
                            <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Last Updated</th>
                                <th>Size (In MB)</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach($fileArr as $key => $file)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$file["name"]}}</td>
                                        <td>{{$file["lastUploadedAt"] ? $file["lastUploadedAt"] : $file["created_at"]}}</td>
                                        <td>{{$file["fileSize"]}}</td>
                                        <td>
                                            <a href="{{url('') . '/customer/file-download/' . $file["id"]}}">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script>
    let token = $('meta[name="csrf-token"').attr('content');
    $("body").tooltip({ selector: '[rel=tooltip]' });

    $('.modal').on('shown.bs.modal', function() {
        $(this).find('[autofocus]').focus();
    });

    $('#backupOrderTable').DataTable({
        dom:  
        "<'row'<'col-sm-12'l>>" +
        "<'row'<'col-sm-6'i><'col-sm-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-6 text-left'i><'col-sm-6 text-right'p>>",
    });
    
    document.querySelector('#backup-type').addEventListener('change', (e) => {
        $.ajax({
            url: '{{route("customer.fetchbackup")}}',
            method: "GET",
            data: {backupType: e.target.value},
            success: function(res) {
                if(res.success) {
                    let dataArr = [];
                    $('#backupOrderTable').DataTable().clear().draw();
                    res.fileArr.forEach((el, index) => {          
                        dataArr.push([
                            index+1,
                            el.name,
                            el.lastUploadedAt ? el.lastUploadedAt : el.created_at,
                            el.fileSize,
                            `<a href='{{url('')}}/customer/file-download/${el.id}'>Download</a>`
                        ]);
                    });
                    $('#backupOrderTable').DataTable().rows.add(dataArr).draw();
                }
            },
            error: function(res) {
                if(res.status === 401) {
                    window.location.href = "{{url('/')}}";
                }
                alert("Error In fetching your backup. Please contact admin.");
            }
        })
    });
</script>
@endsection