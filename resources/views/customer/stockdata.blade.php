@extends('customer.layouts.app')

@section('styles')
<style>
    .stock-data-card .card-body { padding: 0.5rem; }
    #stockDataTable_wrapper .dataTables_wrapper { padding: 0.25rem 0; }
    #stockDataTable { font-size: 0.8rem; }
    #stockDataTable thead th, #stockDataTable tbody td { padding: 0.35rem 0.5rem; }
    #stockDataTable_wrapper .dataTables_length, #stockDataTable_wrapper .dataTables_filter { margin-bottom: 0.5rem; }
    #stockDataTable_wrapper .dataTables_info, #stockDataTable_wrapper .dataTables_paginate { margin-top: 0.5rem; padding: 0.25rem 0; }
    #stockDataTable_wrapper .dataTables_filter label { font-weight: bold; font-size: 1.15rem; display: flex; align-items: center; width: 100%; margin-bottom: 0; }
    #stockDataTable_wrapper .dataTables_filter input { flex: 1; min-width: 0; margin-left: 0.5rem; }
    #stockDataTable .col-item-name { width: 25%; }
    @media (max-width: 767px) {
        .stock-data-page .content-header { padding-left: 0; padding-right: 0; }
        .stock-data-page .content { padding-left: 0; padding-right: 0; }
        .stock-data-table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; max-width: 100%; }
        /* Pagination only: fit inside card on vertical layout */
        #stockDataTable_wrapper .row:last-child { overflow: visible; }
        #stockDataTable_wrapper .row:last-child .col-sm-12 { max-width: 100%; flex: 0 0 100%; }
        #stockDataTable_wrapper .dataTables_info { width: 100%; text-align: center; margin-bottom: 0.5rem; font-size: 0.75rem; }
        #stockDataTable_wrapper .dataTables_paginate { width: 100%; max-width: 100%; overflow: visible; white-space: normal; text-align: center; padding: 0.25rem 0; }
        #stockDataTable_wrapper .dataTables_paginate ul.pagination { margin: 0; padding: 0; gap: 0; white-space: normal; flex-wrap: wrap; justify-content: center; font-size: 0.7rem; }
        #stockDataTable_wrapper .dataTables_paginate ul.pagination .page-item { margin: 0 1px; }
        #stockDataTable_wrapper .dataTables_paginate ul.pagination .page-link { padding: 0.15rem 0.35rem; font-size: 0.7rem; }
    }
    .stock-data-table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
</style>
@endsection

@section('content')
<div class="content-wrapper stock-data-page">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 id="orderListTitle" class="m-0"><span class="pb-md-1" style='font-size:1.3rem; border-bottom:2px solid black;'>Stock Data</span></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('customer.dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active">Stock Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="outletNameHeading" class="row mb-3 text-center" style="display: none;">
                <div class="col-12">
                    <h2 class="mb-0 font-weight-bold text-dark" style="font-size: 1.75rem;"></h2>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 col-md-4">
                    <label for="outletSelect" class="mr-2">Outlet</label>
                    <select id="outletSelect" class="form-control" style="width: auto; display: inline-block;">
                        @foreach($outletOptions as $opt)
                            <option value="{{ $opt['value'] }}" {{ $opt['value'] == $defaultOutlet ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card stock-data-card" style="margin-bottom: 0.5rem;">
                        <div class="card-body">
                            <table id="stockDataTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S NO</th>
                                        <th>OUTLET ID</th>
                                        <th>OUTLET NAME</th>
                                        <th>ITEM NAME</th>
                                        <th>COMPANY NAME</th>
                                        <th>PACKING</th>
                                        <th>PACKSIZE</th>
                                        <th>MRP</th>
                                        <th>STOCK</th>
                                        <th>LAST DATE</th>
                                        <th>LAST TIME</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    var stockDataTable = $('#stockDataTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
        ajax: {
            url: '{{ route("customer.stockdata.table") }}',
            type: 'GET',
            data: function(d) {
                d.outlet = $('#outletSelect').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, render: function(data, type, row, meta) { return type === 'display' ? (meta.settings._iDisplayStart + meta.row + 1) : data; } },
            { data: 'Outlet_Id', name: 'Outlet_Id', searchable: false, orderable: false },
            { data: 'Outlet_Name', name: 'Outlet_Name', searchable: false, orderable: false },
            { data: 'Item_Name', name: 'Item_Name', orderable: true, className: 'col-item-name' },
            { data: 'Company_name', name: 'Company_name', orderable: true },
            { data: 'PackDesc', name: 'PackDesc', searchable: false, orderable: false },
            { data: 'PackSize', name: 'PackSize', searchable: false, orderable: false },
            { data: 'MRP', name: 'MRP', searchable: false, orderable: true, className: 'col-mrp' },
            { data: 'BatchQty', name: 'BatchQty', searchable: false, orderable: true },
            { data: 'Dateofsending', name: 'Dateofsending', searchable: false, orderable: false },
            { data: 'Timeofsending', name: 'Timeofsending', searchable: false, orderable: false }
        ],
        order: [[3, 'asc']],
        pagingType: 'full_numbers',
        dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-8'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        drawCallback: function() {
            var isAll = $('#outletSelect').val() === 'all';
            stockDataTable.columns([1, 2]).visible(isAll);
            if (!$('#stockDataTable').parent().hasClass('stock-data-table-scroll')) {
                $('#stockDataTable').wrap('<div class="stock-data-table-scroll">');
            }
        }
    });
    if (!$('#stockDataTable').parent().hasClass('stock-data-table-scroll')) {
        $('#stockDataTable').wrap('<div class="stock-data-table-scroll">');
    }
    var isAll = $('#outletSelect').val() === 'all';
    stockDataTable.columns([1, 2]).visible(isAll);
    function updateOutletNameHeading() {
        var sel = $('#outletSelect');
        var val = sel.val();
        var text = sel.find('option:selected').text();
        var heading = $('#outletNameHeading h2');
        if (val === 'all') {
            $('#outletNameHeading').hide();
        } else {
            var name = text.indexOf(' -> ') !== -1 ? text.split(' -> ')[1] : text;
            heading.text(name);
            $('#outletNameHeading').show();
        }
    }
    updateOutletNameHeading();
    $('#outletSelect').on('change', function() {
        updateOutletNameHeading();
        stockDataTable.ajax.reload();
    });
});
</script>
@endsection
