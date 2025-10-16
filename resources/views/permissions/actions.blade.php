<div class="btn-group">
    @can('permission-edit')
        <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-info">
            <i class="fas fa-edit"></i> Edit
        </a>
    @endcan
    @can('permission-delete')
        <button type="button" class="btn btn-sm btn-danger delete-permission ml-2" data-id="{{ $permission->id }}">
            <i class="fas fa-trash"></i> Delete
        </button>
    @endcan
</div> 