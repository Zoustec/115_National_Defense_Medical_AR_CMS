<span class="badge badge-{{ $unit->status ? 'success' : 'secondary' }}">
    {{ $unit->status ? __('common.active') : __('common.inactive') }}
</span>
