@extends('layouts.app')


@section('content')
@if(request()->is('downloads'))
<style>
    @media (min-width: 300px) and (max-width: 639px) {
        #guest-files {
            margin-bottom: 6rem;
        }
    }

    @media(min-width: 500px) and (max-width: 767px) {
        #guest-files {
            margin-bottom: 18rem;
        }
    }
</style>
@endif
<section id="guest-files" class="mt-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 m-auto">
                <div class="card">
                    <div class="card-header">
                        <p id="noFileFeedback"></p>
                        @if(session('noFiles'))
                        <p class="alert alert-danger">{{session('noFiles')}}</p>
                        @endif
                        <h4><i class="fa fa-downloads"></i> Downloads </h4>
                    </div>
                    <div class="table-responsive">
                        <table id="fileTable" class="table table-bordered w-100">
                            <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>Last Updated</th>
                                <th>Size</th>
                            </thead>
                            <tbody>                             
                                @foreach($backupFiles as $file) 
                                    <tr>
                                        <td>{{$loop->index + 1}}</td>
                                        <td>
                                            <a href="{{route('pages.download', $file->eid)}}">
                                               {{ $file->id == 31 ? "Backup (OLD)" : ($file->id == 158 ? "Backup (NEW)" : "") }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ 
                                                $file->lastUploadedAt ? $file->lastUploadedAt->format('d/m/Y, g:i A') : $file->created_at->format('d/m/Y, g:i A')
                                            }}
                                        </td>
                                        <td>{{number_format($file->fileSize / 1048576,2) . ' MB'}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@section('scripts')
    <script>
        $('#fileTable').DataTable();
    </script>
@endsection