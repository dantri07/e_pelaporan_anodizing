@can('machine-list')
<a href="{{ route('machines.show', $machine->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
@endcan
@can('machine-edit')
<a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
@endcan
@can('machine-delete')
<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $machine->id }}"><i class="fas fa-trash"></i></button>
@endcan 