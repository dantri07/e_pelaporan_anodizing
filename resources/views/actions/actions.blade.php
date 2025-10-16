<a href="{{ route('actions.edit', $action->action_id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $action->action_id }}"><i class="fas fa-trash"></i> Delete</button>
@if(isset($action) && $action->images && $action->images->count() > 0)
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
<script>
$(document).on('click', '.btn-delete', function() {
    var id = $(this).data('id');
    if (confirm('Are you sure you want to delete this action?')) {
        $.ajax({
            url: '/actions/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#actions-table').DataTable().ajax.reload();
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr) {
                alert('An error occurred while deleting the action.');
            }
        });
    }
});
</script> 