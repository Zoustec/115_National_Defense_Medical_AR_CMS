@extends('adminlte::page')

@section('title', __('users.title_list'))

@section('content_header')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6 d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-link p-0 shadow-none text-secondary mr-2"
                title="{{ __('common.back') }}" style="transition:color 0.2s;">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="mb-0">{{ __('users.title_list') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('common.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('users.title_list') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Tabs --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ul class="nav nav-tabs flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'student' ? 'active' : '' }}"
                           href="{{ route('admin.users.index', ['tab' => 'student']) }}">
                            <i class="fas fa-user-graduate"></i> {{ __('users.tab_students') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'teacher' ? 'active' : '' }}"
                           href="{{ route('admin.users.index', ['tab' => 'teacher']) }}">
                            <i class="fas fa-chalkboard-teacher"></i> {{ __('users.tab_teachers') }}
                        </a>
                    </li>
                </ul>
                <button type="button" class="btn btn-outline-primary ml-2" data-toggle="modal" data-target="#exportUsersModal">
                    <i class="fas fa-file-export"></i> {{ __('exports.export_csv') }}
                </button>
            </div>

            {{-- Export Users Modal --}}
            <div class="modal fade" id="exportUsersModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form method="GET" action="{{ route('admin.users.export') }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('exports.users_section') }}</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted">{{ __('exports.users_description') }}</p>
                                <div class="form-group">
                                    <label>{{ __('exports.col_account_status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="">{{ __('common.all') }}</option>
                                        <option value="1">{{ __('exports.status_active') }}</option>
                                        <option value="0">{{ __('exports.status_inactive') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('exports.role') }}</label>
                                    <select name="role" class="form-control" value="{{ $tab === 'teacher' ? '1' : '0' }}">
                                        <option value="">{{ __('common.all') }}</option>
                                        <option value="0" {{ $tab === 'student' ? 'selected' : '' }}>{{ __('exports.role_student') }}</option>
                                        <option value="1" {{ $tab === 'teacher' ? 'selected' : '' }}>{{ __('exports.role_teacher') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-download"></i> {{ __('exports.export_csv') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" id="searchForm">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="keyword">{{ __('common.search_label') }}</label>
                                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                                    class="form-control" placeholder="{{ __('users.search_placeholder') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="status">{{ __('users.status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">{{ __('common.all') }}</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('common.active') }}</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('common.inactive') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <fieldset class="btn-group">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> {{ __('common.search') }}
                                    </button>
                                    <a href="{{ route('admin.users.index', ['tab' => $tab]) }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> {{ __('common.reset') }}
                                    </a>
                                </fieldset>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> {{ __('users.sso_readonly_notice') }}
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-fixed mb-0">
                            <colgroup>
                                <col style="width:18%;">
                                <col style="width:14%;">
                                <col style="width:24%;">
                                <col style="width:110px;">
                                <col style="width:110px;">
                                <col style="width:150px;">
                                <col style="width:90px;">
                            </colgroup>
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('users.hash_id') }}</th>
                                    <th>{{ __('users.emp_id') }}</th>
                                    <th>{{ __('users.cname') }}</th>
                                    <th>{{ __('users.role') }}</th>
                                    <th>{{ __('users.status') }}</th>
                                    <th>{{ __('users.last_login_at') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="text-truncate"><code title="{{ $user->hash_id ?? $user->id }}">{{ $user->hash_id ?? $user->id }}</code></td>
                                        <td class="text-truncate" title="{{ $user->emp_id ?? '-' }}">{{ $user->emp_id ?? '-' }}</td>
                                        <td class="text-truncate" title="{{ $user->cname ?? $user->username }}">{{ $user->cname ?? $user->username }}</td>
                                        <td>
                                            @if ($user->role === \App\Models\User::ROLE_TEACHER)
                                                <span class="badge badge-primary">{{ __('users.role_teacher') }}</span>
                                            @else
                                                <span class="badge badge-info">{{ __('users.role_student') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->is_active)
                                                <span class="badge badge-success">{{ __('users.status_active') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ __('users.status_suspended') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($user->last_login_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                                class="btn btn-sm btn-info" title="{{ __('common.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <p class="mb-0">{{ __('users.no_users_found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="d-flex align-items-center">
                            <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline">
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
                            @if ($users->total() > 0)
                                <div class="text-muted small ml-3 mr-3">
                                    {{ __('common.showing_results', [
                                        'first' => $users->firstItem() ?? 0,
                                        'last' => $users->lastItem() ?? 0,
                                        'total' => $users->total(),
                                    ]) }}
                                </div>
                            @endif
                            @if ($users->hasPages())
                                <div>
                                    {{ $users->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Fixed table layout: columns keep their declared widths regardless of content length */
        .table-fixed {
            table-layout: fixed;
            width: 100%;
        }

        /* Long text is clipped with an ellipsis instead of stretching the column */
        .table-fixed td.text-truncate,
        .table-fixed th {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 0;
        }
    </style>
@stop

@section('js')
@stop
