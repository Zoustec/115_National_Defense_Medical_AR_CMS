@push('js')
<script>
    window.deleteConfirmMessages = {
        title:   @json(__('common.delete_confirmation_title')),
        text:    @json(__('common.delete_confirmation_text')),
        confirm: @json(__('common.delete_confirm_button')),
        cancel:  @json(__('common.cancel')),
    };
</script>
<script src="{{ asset('js/admin/delete-confirm.js') }}"></script>
@endpush
