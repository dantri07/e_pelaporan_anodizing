@extends('layouts.app')
@section('header', 'Machine Report Detail')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Machine Report Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('machine-reports.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>User:</strong> {{ $report->user->name }}</p>
                            <p><strong>Machine:</strong> {{ $report->machine_name }}</p>
                            <p><strong>Description:</strong> {{ $report->report_description }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date:</strong> {{ $report->report_date }}</p>
                            <p><strong>Technician:</strong> {{ $report->technician ? $report->technician->name : '-' }}</p>
                        </div>
                    </div>
                    @if($report->media && $report->media->count() > 0)
                        <hr>
                        <h5>Media:</h5>
                        <div class="row">
                            @foreach($report->media as $media)
                                <div class="col-md-3 mb-2">
                                    @if($media->file_type === 'image')
                                        <img src="{{ asset('storage/' . $media->file_path) }}" class="img-fluid rounded border" style="max-height:120px;">
                                    @elseif($media->file_type === 'video')
                                        <video controls style="max-width:100%; max-height:120px;">
                                            <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <a href="{{ asset('storage/' . $media->file_path) }}" target="_blank">Download File</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    @if(Auth::user()->can('action-list') || Auth::id() === $report->technician_id)
                        @if($report->actions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                            <th>Technician</th>
                                            <th>Spare Part</th>
                                            <th>Quantity</th>
                                            <th>Report Media</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($report->actions as $action)
                                        <tr id="action-row-{{ $action->action_id }}">
                                            <td>
                                                <span class="badge badge-{{ $action->status === 'completed' ? 'success' : ($action->status === 'in_progress' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($action->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $action->description }}
                                                @if($action->images && $action->images->count() > 0)
                                                    <div class="mt-2">
                                                        <strong>Images:</strong>
                                                        <div class="row">
                                                            @foreach($action->images as $img)
                                                                <div class="col-md-3 mb-2">
                                                                    <img src="{{ asset('storage/' . $img->file_path) }}" class="img-fluid rounded border" style="max-height:120px;">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $action->date ? $action->date->format('d M Y H:i') : '-' }}</td>
                                            <td>{{ $action->technician ? $action->technician->name : '-' }}</td>
                                            <td>{{ $action->sparePart ? $action->sparePart->name : '-' }}</td>
                                            <td>{{ $action->quantity ?? '-' }}</td>
                                            <td>
                                                @foreach($report->media as $media)
                                                    @if($media->file_type === 'image')
                                                        <img src="{{ asset('storage/' . $media->file_path) }}" style="max-width:40px;max-height:40px;" class="mr-1 mb-1">
                                                    @elseif($media->file_type === 'video')
                                                        <video style="max-width:40px;max-height:40px;" controls>
                                                            <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                                        </video>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @if(Auth::id() === $action->technician_id)
                                                    <a href="{{ route('actions.edit', $action->action_id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-action" data-id="{{ $action->action_id }}">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                No actions have been added to this report yet.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            You do not have permission to view actions for this report.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
$(document).on('click', '.btn-delete-action', function() {
    var actionId = $(this).data('id');
    if (confirm('Are you sure you want to delete this action?')) {
        $.ajax({
            url: '/actions/' + actionId,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#action-row-' + actionId).remove();
                } else {
                    alert(response.error || 'Failed to delete action.');
                }
            },
            error: function(xhr) {
                alert('An error occurred while deleting the action.');
            }
        });
    }
});
</script>
@endpush
@endsection 