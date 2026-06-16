<span class="badge badge-{{ $unit->is_locked ? 'warning' : 'light' }}">
    {{ $unit->is_locked ? __('common.yes') : __('common.no') }}
</span>
