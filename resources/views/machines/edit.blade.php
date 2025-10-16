@extends('layouts.app')
@section('header', 'Edit Machine')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Machine</h3>
                    <div class="card-tools">
                        <a href="{{ route('machines.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form action="{{ route('machines.update', $machine->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Machine Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $machine->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $machine->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Current Media Files</label>
                            @if($machine->media && $machine->media->count() > 0)
                                <div class="row">
                                    @foreach($machine->media as $media)
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
                        <button type="submit" class="btn btn-primary">Update Machine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteMedia(mediaId) {
    if (confirm('Are you sure you want to delete this media file?')) {
        fetch(`/machines/media/${mediaId}`, {
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