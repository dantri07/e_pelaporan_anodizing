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
                data: 'action_status', 
                name: 'action_status',
                render: function(data) {
                    let badgeClass = 'info';
                    if (data === 'Completed') badgeClass = 'success';
                    else if (data === 'In Progress') badgeClass = 'warning';
                    return `<span class="badge badge-${badgeClass}">${data}</span>`;
                }
            },
            { data: 'action_description', name: 'action_description' },
            { data: 'action_date', name: 'action_date' },
            { data: 'technician', name: 'technician' },
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
                data: 'spare_part', 
                name: 'spare_part',
                render: function(data) {
                    if (!data) return '-';
                    return `<span class="text-muted">${data.name}</span>`;
                }
            },
            { 
                data: 'spare_part_quantity', 
                name: 'spare_part_quantity',
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