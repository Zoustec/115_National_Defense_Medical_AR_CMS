{{-- $editUrl, $deleteUrl --}}
<a href="{{ $editUrl }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
<button type="button" class="btn btn-sm btn-danger js-btn-delete"
        data-url="{{ $deleteUrl }}" data-csrf="{{ csrf_token() }}">
    <i class="fas fa-trash"></i>
</button>
