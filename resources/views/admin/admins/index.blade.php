@extends('adminlte::page')

@section('title', __('admins.title_list'))

@section('content_header')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6 d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-link p-0 shadow-none text-secondary mr-2"
                title="{{ __('common.back') }}" style="transition:color 0.2s;">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="mb-0">{{ __('admins.title_list') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('common.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admins.title_list') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @include('admin._flash')

    <div class="row">
        <div class="col-12">
            <div class="card-header bg-light">
                <div class="card-tools">
                    <a href="{{ route('admin.admins.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('admins.add_admin') }}
                    </a>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.admins.index') }}" id="searchForm">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="keyword">{{ __('common.search_label') }}</label>
                                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                                    class="form-control" placeholder="{{ __('common.search_placeholder') }}">
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <fieldset class="btn-group">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> {{ __('common.search') }}
                                    </button>
                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> {{ __('common.reset') }}
                                    </a>
                                </fieldset>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('admins.id') }}</th>
                                <th>{{ __('admins.name') }}</th>
                                <th>{{ __('admins.email') }}</th>
                                <th>{{ __('admins.status') }}</th>
                                <th>{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($admins as $admin)
                                <tr>
                                    <td>{{ $admin->id }}</td>
                                    <td>{{ $admin->name ?? '-' }}</td>
                                    <td>{{ $admin->email ?? '-' }}</td>
                                    <td>
                                        <input type="checkbox" class="status-switch" data-id="{{ $admin->id }}"
                                            data-toggle="toggle" data-on="{{ __('common.active') }}"
                                            data-off="{{ __('common.inactive') }}" data-onstyle="success"
                                            data-offstyle="secondary" data-size="small"
                                            {{ $admin->is_active ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $admin->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p class="mb-0">{{ __('admins.no_admins_found') }}</p>
                                        @if (request()->hasAny(['keyword']))
                                            <small class="text-muted">{{ __('admins.adjust_search_criteria') }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="d-flex align-items-center">
                            <form method="GET" action="{{ route('admin.admins.index') }}" class="form-inline">
                                @foreach (request()->except('per_page') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <label for="pagination" class="mr-2 mb-0">{{ __('common.per_page') }}:</label>
                                <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
                                    @foreach (\App\Constants\Pagination::OPTIONS as $option)
                                        <option value="{{ $option }}"
                                            {{ request('per_page', \App\Constants\Pagination::PER_PAGE) == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            @if ($admins->total() > 0)
                                <div class="text-muted small ml-3 mr-3">
                                    {{ __('common.showing_results', [
                                        'first' => $admins->firstItem() ?? 0,
                                        'last' => $admins->lastItem() ?? 0,
                                        'total' => $admins->total(),
                                    ]) }}
                                </div>
                            @endif
                            @if ($admins->hasPages())
                                <div>
                                    {{ $admins->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/admin/admin-crud.js') }}"></script>
    <script>
        window.usersConfig = {
            deleteUrl: '{{ url('admin/admins') }}/:id',
            toggleStatusUrl: '{{ url('admin/admins') }}/:id/toggle-status',
            csrfToken: '{{ csrf_token() }}',
            messages: {
                deleteTitle: '{{ __('common.delete_confirmation_title') }}',
                deleteText: '{{ __('common.delete_confirmation_text') }}',
                deleteConfirm: '{{ __('common.delete_confirm_button') }}',
                deleteCancel: '{{ __('common.cancel') }}',
                deleteSuccess: '{{ __('common.delete_success') }}',
                deleteError: '{{ __('common.delete_failed') }}',
                enableTitle: '{{ __('admins.confirm_enable_title') }}',
                enableText: '{{ __('admins.confirm_enable_text') }}',
                disableTitle: '{{ __('admins.confirm_disable_title') }}',
                disableText: '{{ __('admins.confirm_disable_text') }}',
                statusSuccess: '{{ __('common.status_updated_successfully') }}',
                statusError: '{{ __('common.status_update_failed') }}',
            }
        };

        function confirmDelete(userId) {
            if (window.UsersModule && window.UsersModule.confirmDelete) {
                window.UsersModule.confirmDelete(userId);
            } else {
                console.error('UsersModule not found');
            }
        }

        let isProcessing = false;

        $(document).ready(function() {
            $('#searchForm').on('submit', function(e) {
                const currentPerPage = '{{ request('per_page') }}';
                if (currentPerPage) {
                    $(this).append($('<input>').attr({
                        type: 'hidden',
                        name: 'per_page',
                        value: currentPerPage
                    }));
                }
            });

            $('.status-switch').bootstrapToggle();

            $('.status-switch').change(function() {
                if (isProcessing) return;

                const userId = $(this).data('id');
                const isEnabling = $(this).prop('checked');
                const switchElement = $(this);
                const config = window.usersConfig;

                const title = isEnabling ? config.messages.enableTitle : config.messages.disableTitle;
                const text = isEnabling ? config.messages.enableText : config.messages.disableText;

                isProcessing = true;
                isEnabling ? switchElement.bootstrapToggle('off') : switchElement.bootstrapToggle('on');
                isProcessing = false;

                Swal.fire({
                    title: title,
                    text: text,
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '<i class="fas fa-check"></i> ' +
                        '{{ __('common.confirm') }}',
                    cancelButtonText: '<i class="fas fa-times"></i> ' +
                        '{{ __('common.cancel') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        const url = config.toggleStatusUrl.replace(':id', userId);
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: {
                                _token: config.csrfToken
                            },
                            success: function(response) {
                                if (response.success) {
                                    isProcessing = true;
                                    isEnabling ? switchElement.bootstrapToggle('on') :
                                        switchElement.bootstrapToggle('off');
                                    isProcessing = false;
                                    Swal.fire({
                                        type: 'success',
                                        title: '{{ __('common.success') }}',
                                        text: config.messages.statusSuccess,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: '{{ __('common.error') }}',
                                        text: config.messages.statusError
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    type: 'error',
                                    title: '{{ __('common.error') }}',
                                    text: config.messages.statusError
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop

@section('plugins.Bootstrap4Toggle', true)
