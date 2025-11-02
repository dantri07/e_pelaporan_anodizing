@extends('layouts.app')
@section('header', 'Action List')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Action List</h3>
                    <div class="card-tools">
                        @can('action-create')
                        <a href="{{ route('actions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Action
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="actions-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Status</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Technician</th>
                                    <th>Machine Report</th>
                                    <th>Spare Part</th>
                                    <th>Quantity</th>
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
    $('#actions-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{!! route('actions.index') !!}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    let badgeClass = 'info';
                    if (data === 'Completed') badgeClass = 'success';
                    else if (data === 'In Progress') badgeClass = 'warning';
                    return `<span class="badge badge-${badgeClass}">${data}</span>`;
                }
            },
            { data: 'description', name: 'description' },
            { data: 'date', name: 'date' },
            { data: 'technician_id', name: 'technician_id' },
            { 
                data: 'machine_report', 
                name: 'machine_report',
                render: function(data) {
                    if (!data) return '-';
                    return `<a href="${data.edit_url}" class="text-primary">
                        <i class="fas fa-link"></i> ${data.machine_name}
                    </a>`;
                }
            },
            { 
                data: 'spare_part_id', 
                name: 'spare_part_id',
                render: function(data) {
                    if (!data) return '-';
                    return `<span class="text-muted">${data.name}</span>`;
                }
            },
            { 
                data: 'quantity', 
                name: 'quantity',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    return data;
                }
            },
        ],
        order: [[3, 'desc']], // Sort by date descending by default
        language: {
            search: "Search actions:",
            lengthMenu: "Show _MENU_ actions per page",
            info: "Showing _START_ to _END_ of _TOTAL_ actions",
            infoEmpty: "No actions found",
            infoFiltered: "(filtered from _MAX_ total actions)",
            zeroRecords: "No matching actions found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});
</script>
@endpush 