@extends('layouts.app')
@section('header', 'Machine Reports')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Machine Reports</h3>
                    <div class="card-tools">
                        @can('machine-report-create')
                        <a href="{{ route('machine-reports.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Machine Report
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="machine-reports-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Machine Name</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Media</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#machine-reports-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{!! route('machine-reports.index') !!}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'user', name: 'user' },
            { data: 'machine_name', name: 'machine_name' },
            { data: 'report_description', name: 'report_description' },
            { data: 'report_date', name: 'report_date' },
            { data: 'media', name: 'media', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush 