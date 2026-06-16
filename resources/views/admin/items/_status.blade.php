<span class="badge badge-{{ $item->status ? 'success' : 'secondary' }}">
    {{ $item->status ? __('common.active') : __('common.inactive') }}
</span>
