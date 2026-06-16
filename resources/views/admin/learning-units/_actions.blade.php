{{-- $unit --}}
<a href="{{ route('admin.learning-units.edit', $unit) }}" class="btn btn-sm btn-primary" title="{{ __('common.edit') }}">
    <i class="fas fa-edit"></i>
</a>
<a href="{{ route('admin.learning-units.export-single', $unit) }}" class="btn btn-sm btn-outline-primary"
    title="{{ __('exports.export_single_tooltip') }}">
    <i class="fas fa-file-csv"></i>
</a>
<button type="button" class="btn btn-sm btn-danger js-btn-delete"
    data-url="{{ route('admin.learning-units.destroy', $unit) }}" data-csrf="{{ csrf_token() }}"
    title="{{ __('common.delete') }}">
    <i class="fas fa-trash"></i>
</button>
