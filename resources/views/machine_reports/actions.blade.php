@php
    $isAssignedTechnician = Auth::user()->hasRole('technician') && $report->technician_id === Auth::id();
    $canViewActions = Auth::user()->can('action-list') || $isAssignedTechnician;
    $canEdit = Auth::id() === $report->user_id && Auth::user()->can('machine-report-edit');
@endphp

@if($isAssignedTechnician)
    <a href="{{ route('actions.create', ['report_id' => $report->id]) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus"></i> Add Action
    </a>
@endif

@if($canViewActions)
    <a href="{{ route('machine-reports.show', $report->id) }}" class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i> View
    </a>
@endif

@if($canEdit)
    <a href="{{ route('machine-reports.edit', $report->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $report->id }}"><i class="fas fa-trash"></i> Delete</button>
@endif

<script>
$(document).on('click', '.btn-delete', function() {
    var id = $(this).data('id');
    if (confirm('Are you sure you want to delete this machine report?')) {
        $.ajax({
            url: '/machine-reports/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#machine-reports-table').DataTable().ajax.reload();
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr) {
                alert('An error occurred while deleting the machine report.');
            }
        });
    }
});
</script> 