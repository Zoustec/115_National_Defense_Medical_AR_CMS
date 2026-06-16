{{--
    Shared flash → toastr notifications.
    Renders session('success'), session('error') and validation errors as
    auto-dismissing toasts (one toast per message).
--}}
@once
    @push('css')
        <link rel="stylesheet" href="{{ asset('vendor/toastr/toastr.min.css') }}">
    @endpush
@endonce

@php
    $flashToasts = [];

    if (session('success')) {
        $flashToasts[] = ['type' => 'success', 'message' => session('success')];
    }

    if (session('error')) {
        $flashToasts[] = ['type' => 'error', 'message' => session('error')];
    }

    // One toast per validation error so long lists stay readable.
    // $errors is shared on every view by ShareErrorsFromSession; guard anyway.
    foreach (($errors ?? collect())->all() as $validationError) {
        $flashToasts[] = ['type' => 'error', 'message' => $validationError];
    }
@endphp

@if (! empty($flashToasts))
    @push('js')
        <script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
        <script>
            $(function () {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    newestOnTop: false,
                    timeOut: 4000,
                    extendedTimeOut: 2000,
                    positionClass: 'toast-top-right',
                };

                var messages = @json($flashToasts);
                messages.forEach(function (item) {
                    if (typeof toastr[item.type] === 'function') {
                        toastr[item.type](item.message);
                    }
                });
            });
        </script>
    @endpush
@endif
