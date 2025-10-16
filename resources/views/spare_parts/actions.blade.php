<div class="btn-group">
    @can('spare-part-edit')
    <a href="{{ route('spare-parts.edit', $sparePart->id) }}" class="btn btn-sm btn-info">
        <i class="fas fa-edit"></i> Edit
    </a>
    @endcan
    @can('spare-part-delete')
    <button type="button" class="btn btn-sm btn-danger delete-spare-part ml-2" data-id="{{ $sparePart->id }}">
        <i class="fas fa-trash"></i> Delete
    </button>
    @endcan
</div> 