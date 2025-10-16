<div class="btn-group">
    @can('user-edit')
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info">
            <i class="fas fa-edit"></i> Edit
        </a>
    @endcan
    @can('user-delete')
        <button type="button" class="btn btn-sm btn-danger delete-user ml-2" data-id="{{ $user->id }}">
            <i class="fas fa-trash"></i> Delete
        </button>
    @endcan
</div> 