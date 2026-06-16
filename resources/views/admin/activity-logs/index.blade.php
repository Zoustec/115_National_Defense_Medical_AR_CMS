@extends('adminlte::page')

@section('title', __('activity.title_list'))

@section('content_header')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6 d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-link p-0 shadow-none text-secondary mr-2"
                title="{{ __('common.back') }}" style="transition:color 0.2s;">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="mb-0">{{ __('activity.page_title') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('common.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('activity.title_list') }}</li>
            </ol>
        </div>
    </div>
@stop

@php
    $actionLabels = [
        \App\Models\ActivityLog::ACTION_LOGIN => __('activity.action_login'),
        \App\Models\ActivityLog::ACTION_LOGOUT => __('activity.action_logout'),
        \App\Models\ActivityLog::ACTION_AR_OPEN => __('activity.action_ar_open'),
        \App\Models\ActivityLog::ACTION_VIRTUAL_PATIENT_OPEN => __('activity.action_virtual_patient_open'),
        \App\Models\ActivityLog::ACTION_SMART_QA_OPEN => __('activity.action_smart_qa_open'),
    ];
    $actionThemes = [
        \App\Models\ActivityLog::ACTION_LOGIN => 'info',
        \App\Models\ActivityLog::ACTION_LOGOUT => 'secondary',
        \App\Models\ActivityLog::ACTION_AR_OPEN => 'primary',
        \App\Models\ActivityLog::ACTION_VIRTUAL_PATIENT_OPEN => 'warning',
        \App\Models\ActivityLog::ACTION_SMART_QA_OPEN => 'success',
    ];

    // Login + logout collapse into one filter option / stat card. The table
    // badges still use $actionLabels above to show each row's exact action.
    $loginLogout = \App\Models\ActivityLog::FILTER_LOGIN_LOGOUT;
    $loginLogoutCount = ($counts[\App\Models\ActivityLog::ACTION_LOGIN] ?? 0)
        + ($counts[\App\Models\ActivityLog::ACTION_LOGOUT] ?? 0);
    $filterOptions = [
        $loginLogout => __('activity.action_login_logout'),
        \App\Models\ActivityLog::FILTER_LEARNING_BEHAVIOR => __('activity.learning_behavior'),
        \App\Models\ActivityLog::ACTION_AR_OPEN => $actionLabels[\App\Models\ActivityLog::ACTION_AR_OPEN],
        \App\Models\ActivityLog::ACTION_VIRTUAL_PATIENT_OPEN => $actionLabels[\App\Models\ActivityLog::ACTION_VIRTUAL_PATIENT_OPEN],
        \App\Models\ActivityLog::ACTION_SMART_QA_OPEN => $actionLabels[\App\Models\ActivityLog::ACTION_SMART_QA_OPEN],
    ];
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Quick stat row --}}
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($loginLogoutCount) }}"
                        text="{{ __('activity.action_login_logout') }}" icon="fas fa-sign-in-alt"
                        theme="{{ $actionThemes[\App\Models\ActivityLog::ACTION_LOGIN] }}" />
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($learningBehaviorCount) }}"
                        text="{{ __('activity.learning_behavior') }}" icon="fas fa-graduation-cap"
                        theme="teal" />
                </div>
                @foreach ([
                    \App\Models\ActivityLog::ACTION_AR_OPEN => 'fas fa-vr-cardboard',
                    \App\Models\ActivityLog::ACTION_VIRTUAL_PATIENT_OPEN => 'fas fa-user-injured',
                    \App\Models\ActivityLog::ACTION_SMART_QA_OPEN => 'fas fa-comments',
                ] as $action => $icon)
                    <div class="col-lg-3 col-md-6 col-12">
                        <x-adminlte-small-box title="{{ number_format($counts[$action] ?? 0) }}"
                            text="{{ $actionLabels[$action] }}" icon="{{ $icon }}"
                            theme="{{ $actionThemes[$action] }}" />
                    </div>
                @endforeach
            </div>

            {{-- Filters --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="keyword">{{ __('common.search_label') }}</label>
                                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                                    class="form-control" placeholder="{{ __('activity.search_placeholder') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="action">{{ __('activity.filter_action') }}</label>
                                <select name="action" id="action" class="form-control">
                                    <option value="">{{ __('activity.all_actions') }}</option>
                                    @foreach ($filterOptions as $value => $label)
                                        <option value="{{ $value }}" {{ request('action') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="date_from">{{ __('activity.date_from') }}</label>
                                <input type="text" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                    class="form-control activity-datepicker" placeholder="YYYY/MM/DD" autocomplete="off" readonly>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="date_to">{{ __('activity.date_to') }}</label>
                                <input type="text" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                    class="form-control activity-datepicker" placeholder="YYYY/MM/DD" autocomplete="off" readonly>
                            </div>
                            <div class="form-group col-md-2 d-flex align-items-end">
                                <fieldset class="btn-group">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> {{ __('common.search') }}
                                    </button>
                                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> {{ __('common.reset') }}
                                    </a>
                                </fieldset>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Unified feed: activity_logs + learning-behaviour rows interleaved by time --}}
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:24%;">{{ __('activity.user') }}</th>
                                    <th style="width:16%;">{{ __('activity.action') }}</th>
                                    <th>{{ __('activity.detail') }}</th>
                                    <th style="width:160px;">{{ __('activity.created_at') }}</th>
                                    <th style="width:90px;">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $row)
                                    @if ($row->row_type === 'behavior')
                                        @php
                                            $detail = $row->model;
                                            $rowUser = optional($detail->progress)->user;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ optional($rowUser)->cname ?? optional($rowUser)->username ?? '-' }}
                                                @if (optional($rowUser)->emp_id)
                                                    <small class="text-muted">({{ $rowUser->emp_id }})</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-teal">{{ __('activity.learning_behavior') }}</span>
                                            </td>
                                            <td>
                                                {{ optional(optional($detail->progress)->learningUnit)->name ?? '-' }}
                                                @if (optional($detail->item)->name)
                                                    · {{ $detail->item->name }}
                                                @endif
                                                @if ($detail->status === \App\Models\UserProgressDetail::STATUS_SWAP)
                                                    <span class="badge badge-primary ml-1">{{ __('activity.detail_status_swap') }}</span>
                                                @else
                                                    <span class="badge badge-success ml-1"><i class="fas fa-check"></i> {{ __('activity.detail_status_visit') }}</span>
                                                @endif
                                                <small class="text-muted ml-1">{{ __('activity.seconds', ['value' => number_format($detail->duration)]) }}</small>
                                            </td>
                                            <td>{{ optional($detail->created_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                            <td>
                                                @if ($rowUser)
                                                    <a href="{{ route('admin.activity-logs.user-history', ['user' => $rowUser->id, 'action' => \App\Models\ActivityLog::FILTER_LEARNING_BEHAVIOR]) }}"
                                                        class="btn btn-sm btn-info" title="{{ __('activity.view_history') }}">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        @php $log = $row->model; @endphp
                                        <tr>
                                            <td>
                                                {{ optional($log->user)->cname ?? optional($log->user)->username ?? '-' }}
                                                @if (optional($log->user)->emp_id)
                                                    <small class="text-muted">({{ $log->user->emp_id }})</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $actionThemes[$log->action] ?? 'light' }}">
                                                    {{ $actionLabels[$log->action] ?? $log->action }}
                                                </span>
                                            </td>
                                            <td>
                                                @if (is_array($log->metadata) && isset($log->metadata['learningUnitId']))
                                                    {{ __('activity.learning_unit') }}:
                                                    {{ $log->learning_unit_name ?? ('#' . $log->metadata['learningUnitId']) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ optional($log->created_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                            <td>
                                                @if ($log->user)
                                                    @php
                                                        $rowAction = in_array($log->action, \App\Models\ActivityLog::ACTION_GROUP_LOGIN, true)
                                                            ? \App\Models\ActivityLog::FILTER_LOGIN_LOGOUT
                                                            : $log->action;
                                                    @endphp
                                                    <a href="{{ route('admin.activity-logs.user-history', ['user' => $log->user_id, 'action' => $rowAction]) }}"
                                                        class="btn btn-sm btn-info" title="{{ __('activity.view_history') }}">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                            <p class="mb-0">{{ __('activity.no_logs_found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-end align-items-center">
                        @if ($logs->total() > 0)
                            <div class="text-muted small mr-3">
                                {{ __('common.showing_results', [
                                    'first' => $logs->firstItem() ?? 0,
                                    'last' => $logs->lastItem() ?? 0,
                                    'total' => $logs->total(),
                                ]) }}
                            </div>
                        @endif
                        @if ($logs->hasPages())
                            <div>{{ $logs->appends(request()->query())->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <style>
        /* readonly keeps typing disabled (date is chosen via the picker only),
           but the browser greys readonly inputs — force a white background so
           the date fields match the search / select filters. */
        .activity-datepicker[readonly] {
            background-color: #fff;
            cursor: pointer;
        }

        /* Bootstrap 4 has no badge-teal; match the teal small-box accent so
           learning-behaviour rows are visually distinct in the feed. */
        .badge-teal {
            color: #fff;
            background-color: #20c997;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(function () {
            var $from = $('input[name="date_from"]');
            var $to = $('input[name="date_to"]');

            $('.activity-datepicker').datepicker({
                format: 'yyyy/mm/dd',
                autoclose: true,
                todayHighlight: true,
                clearBtn: true,
            });

            // Keep the range valid: 結束日期 cannot be earlier than 起始日期, and
            // 起始日期 cannot be later than 結束日期. The picker greys out (blocks)
            // the disallowed days so an invalid range can't be selected at all.
            function syncRangeBounds() {
                // bootstrap-datepicker returns [] when the field is empty.
                var fromDates = $from.datepicker('getDates');
                var toDates = $to.datepicker('getDates');
                var fromDate = fromDates.length ? fromDates[0] : null;
                var toDate = toDates.length ? toDates[0] : null;

                // 結束日期 calendar starts at 起始日期; 起始日期 calendar ends at 結束日期.
                $to.datepicker('setStartDate', fromDate);
                $from.datepicker('setEndDate', toDate);
            }

            $from.on('changeDate', syncRangeBounds);
            $to.on('changeDate', syncRangeBounds);

            // Apply the bounds immediately so a range arriving pre-filled from the
            // URL query (e.g. after a search) is constrained on first render, not
            // only after the user re-touches a field.
            syncRangeBounds();
        });
    </script>
@stop
