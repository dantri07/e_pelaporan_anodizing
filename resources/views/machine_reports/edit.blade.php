@extends('layouts.app')
@section('header', 'Edit Machine Report')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Machine Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('machine-reports.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('machine-reports.update', $report->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="user_id">User</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        </div>
                        <div class="form-group">
                            <label for="machine_name">Machine Name</label>
                            <input type="text" class="form-control @error('machine_name') is-invalid @enderror" id="machine_name" name="machine_name" value="{{ old('machine_name', $report->machine_name) }}" required>
                            @error('machine_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="report_description">Description</label>
                            <textarea class="form-control @error('report_description') is-invalid @enderror" id="report_description" name="report_description" required>{{ old('report_description', $report->report_description) }}</textarea>
                            @error('report_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="report_date">Date</label>
                            <input type="date" class="form-control @error('report_date') is-invalid @enderror" id="report_date" name="report_date" value="{{ old('report_date', $report->report_date) }}" required>
                            @error('report_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="technician_id">Assign Technician</label>
                            <select class="form-control @error('technician_id') is-invalid @enderror" id="technician_id" name="technician_id">
                                <option value="">-- Select Technician --</option>
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}" {{ old('technician_id', $report->technician_id) == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select a technician to handle this report. They will be notified and can add actions later.</small>
                            @error('technician_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Current Media Files</label>
                            @if($report->media && $report->media->count() > 0)
                                <div class="row">
                                    @foreach($report->media as $media)
                                        <div class="col-md-3 mb-2">
                                            @if($media->file_type === 'image')
                                                <div class="position-relative">
                                                    <img src="{{ asset('storage/' . $media->file_path) }}" class="img-fluid rounded border" style="max-height:120px;">
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 5px;" 
                                                            onclick="deleteMedia({{ $media->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @elseif($media->file_type === 'video')
                                                <div class="position-relative">
                                                    <video controls style="max-width:100%; max-height:120px;">
                                                        <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 5px;"
                                                            onclick="deleteMedia({{ $media->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No media files uploaded yet.</p>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="media">Upload New Images or Videos</label>
                            <input type="file" class="form-control @error('media') is-invalid @enderror" id="media" name="media[]" multiple accept="image/*,video/*">
                            <small class="form-text text-muted">You can upload multiple images or videos (max 10MB each).</small>
                            @error('media')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Machine Report</button>
                    </div>
                </form>
            </div>

            {{-- Actions Section --}}
            @if(Auth::id() !== $report->user_id)
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                    @if($report->technician_id === Auth::id() && Auth::user()->hasRole('technician'))
                    <div class="card-tools">
                        <a href="{{ route('actions.create', ['report_id' => $report->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Action
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
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
                                        @if($report->technician_id === Auth::id() && Auth::user()->hasRole('technician'))
                                        <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report->actions as $action)
                                    <tr>
                                        <td>
                                            <span class="badge badge-{{ $action->action_status === 'Completed' ? 'success' : ($action->action_status === 'In Progress' ? 'warning' : 'info') }}">
                                                {{ $action->action_status }}
                                            </span>
                                        </td>
                                        <td>{{ $action->action_description }}</td>
                                        <td>{{ $action->action_date }}</td>
                                        <td>{{ $action->technician->name }}</td>
                                        <td>{{ $action->sparePart->name ?? '-' }}</td>
                                        <td>{{ $action->spare_part_quantity ?? '-' }}</td>
                                        @if($report->technician_id === Auth::id() && Auth::user()->hasRole('technician'))
                                        <td>
                                            <a href="{{ route('actions.edit', $action->action_id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-action" data-id="{{ $action->action_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No actions have been added to this report yet.
                            @if($report->technician_id === Auth::id() && Auth::user()->hasRole('technician'))
                                Click the "Add Action" button above to add an action.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-delete-action').click(function() {
        var actionId = $(this).data('id');
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
                    url: '/actions/' + actionId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'Action has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.error,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the action.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});

function deleteMedia(mediaId) {
    if (confirm('Are you sure you want to delete this media file?')) {
        fetch(`/machine-reports/media/${mediaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the media element from the DOM
                const mediaElement = document.querySelector(`[data-media-id="${mediaId}"]`);
                if (mediaElement) {
                    mediaElement.closest('.col-md-3').remove();
                }
                // Show success message
                Swal.fire('Success', 'Media file deleted successfully', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to delete media file');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Failed to delete media file', 'error');
        });
    }
}
</script>
@endpush 