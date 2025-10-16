@extends('layouts.app')
@section('header', 'Machine Detail')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Machine Detail</h3>
                    <div class="card-tools">
                        <a href="{{ route('machines.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $machine->name }}</p>
                            <p><strong>Description:</strong> {{ $machine->description }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($machine->media && $machine->media->count() > 0)
                                <h5>Media Files:</h5>
                                <div class="row">
                                    @foreach($machine->media as $media)
                                        <div class="col-md-6 mb-3">
                                            @if($media->file_type === 'image')
                                                <img src="{{ asset('storage/' . $media->file_path) }}" alt="Machine Image" class="img-fluid rounded border" style="max-height:200px;">
                                            @elseif($media->file_type === 'video')
                                                <video controls class="img-fluid rounded border" style="max-height:200px;">
                                                    <source src="{{ asset('storage/' . $media->file_path) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p><em>No media files uploaded.</em></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 