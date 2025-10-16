@extends('layouts.app')
@section('header', 'Edit Action')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Action</h3>
                    <div class="card-tools">
                        @if($action->machineReports->isNotEmpty())
                            <a href="{{ route('machine-reports.edit', $action->machineReports->first()->id) }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Report
                            </a>
                        @else
                            <a href="{{ route('actions.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        @endif
                    </div>
                </div>

                @if($action->machineReports->isNotEmpty())
                    @php
                        $report = $action->machineReports->first();
                    @endphp
                    <div class="card-header bg-info">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-info-circle"></i> Machine Report Information
                        </h5>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Machine:</strong> {{ $report->machine_name }}</p>
                                <p><strong>Description:</strong> {{ $report->report_description }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Report Date:</strong> {{ $report->report_date }}</p>
                                <p><strong>Reported By:</strong> {{ $report->user->name }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('actions.update', $action->action_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($action->images && $action->images->count() > 0)
                        <div class="form-group">
                            <label>Uploaded Images</label>
                            <div class="row">
                                @foreach($action->images as $img)
                                    <div class="col-md-3 mb-2">
                                        <img src="{{ asset('storage/' . $img->file_path) }}" class="img-fluid rounded border" style="max-height:120px;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="text" class="form-control @error('status') is-invalid @enderror" id="status" name="status" value="{{ old('status', $action->status) }}" required>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description', $action->description) }}</textarea>
                            <small class="form-text text-muted">Describe what actions you took or plan to take to address the machine report.</small>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="date">Action Date</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $action->date ? $action->date->format('Y-m-d') : '') }}" required>
                            @error('date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="spare_part_id">Spare Part Used (Optional)</label>
                            <select class="form-control @error('spare_part_id') is-invalid @enderror" id="spare_part_id" name="spare_part_id">
                                <option value="">-- Select Spare Part --</option>
                                @foreach($spareParts as $sparePart)
                                    <option value="{{ $sparePart->id }}" {{ old('spare_part_id', $action->spare_part_id) == $sparePart->id ? 'selected' : '' }}>
                                        {{ $sparePart->name }} (Available: {{ $sparePart->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Select a spare part if you used one to fix the machine.</small>
                            @error('spare_part_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="quantity">Spare Part Quantity</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $action->quantity) }}" min="1">
                            <small class="form-text text-muted">Enter the quantity of spare parts used.</small>
                            @error('quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="images">Upload Images</label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                            <small class="form-text text-muted">You can upload more than one image.</small>
                            @error('images')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide spare part quantity based on spare part selection
    $('#spare_part_id').change(function() {
        if ($(this).val()) {
            $('#quantity').prop('required', true);
        } else {
            $('#quantity').prop('required', false);
        }
    });

    // Initialize spare part quantity requirement
    if ($('#spare_part_id').val()) {
        $('#quantity').prop('required', true);
    }
});
</script>
@endpush 