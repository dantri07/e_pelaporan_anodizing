@extends('layouts.app')
@section('header', 'Spare Parts Management')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Spare Parts Management</h3>
                    <div class="card-tools">
                        @can('spare-part-create')
                            <a href="{{ route('spare-parts.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Spare Part
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="spare-parts-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Description</th>
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

<!-- Add CSRF Token Meta Tag -->
@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@endsection

@push('scripts')
<script>
$(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    var table = $('#spare-parts-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('spare-parts.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'quantity', name: 'quantity'},
            {data: 'description', name: 'description'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    // Delete functionality
    $(document).on('click', '.delete-spare-part', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('spare-parts') }}/" + id,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            );
                        }
                    },
                    error: function(response) {
                        Swal.fire(
                            'Error!',
                            response.responseJSON.error || 'Something went wrong!',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush 