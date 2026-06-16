@extends('adminlte::page')

@section('title', __('activity.user_history'))

@section('content_header')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6 d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-link p-0 shadow-none text-secondary mr-2"
                title="{{ __('common.back') }}">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="mb-0">{{ __('activity.user_history') }} — {{ $user->cname ?? $user->username }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('common.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.activity-logs.index') }}">{{ __('activity.title_list') }}</a></li>
                <li class="breadcrumb-item active">{{ __('activity.user_history') }}</li>
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
        \App\Models\ActivityLog::FILTER_LOGIN_LOGOUT => __('activity.action_login_logout'),
    ];
    $activeActionLabel = $action ? ($actionLabels[$action] ?? $action) : null;
@endphp

@section('content')
    {{-- ── Learning behavior (requirement A.1) ──────────────────────── --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-graduation-cap mr-2"></i>{{ __('activity.learning_behavior') }}</h3>
        </div>
        <div class="card-body">
            @forelse ($progress as $attempt)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">
                            {{ optional($attempt->learningUnit)->name ?? '-' }}
                            <small class="text-muted">{{ __('activity.attempt', ['no' => $attempt->session_no ?? 1]) }}</small>
                        </h5>
                        <div>
                            @if ($attempt->status === \App\Models\UserProgress::STATUS_COMPLETED)
                                <span class="badge badge-success">{{ __('activity.status_completed') }}</span>
                                @if ($attempt->completed_at)
                                    <small class="text-muted ml-1">{{ $attempt->completed_at->format('Y-m-d H:i') }}</small>
                                @endif
                            @else
                                <span class="badge badge-warning">{{ __('activity.status_in_progress') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('activity.food_item') }}</th>
                                    <th style="width:18%;">{{ __('activity.category') }}</th>
                                    <th style="width:16%;">{{ __('activity.status') }}</th>
                                    <th style="width:16%;">{{ __('activity.read_duration') }}</th>
                                    <th style="width:18%;">{{ __('activity.started_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attempt->details as $detail)
                                    <tr>
                                        <td>{{ optional($detail->item)->name ?? '-' }}</td>
                                        <td>{{ optional(optional($detail->item)->category)->name ?? '-' }}</td>
                                        <td>
                                            @if ($detail->status === \App\Models\UserProgressDetail::STATUS_SWAP)
                                                <span class="badge badge-primary">{{ __('activity.detail_status_swap') }}</span>
                                            @else
                                                <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('activity.detail_status_visit') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ __('activity.seconds', ['value' => number_format($detail->duration)]) }}</td>
                                        <td>{{ optional($detail->start_time)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-2">{{ __('activity.no_details') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted py-3 mb-0">{{ __('activity.no_progress') }}</p>
            @endforelse
        </div>
    </div>

    {{-- ── Login / click activity (requirements A.2–A.4) ────────────── --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title mb-0"><i class="fas fa-clipboard-list mr-2"></i>{{ __('activity.login_activity') }}</h3>
            @if ($activeActionLabel)
                <span>
                    <span class="badge badge-info">{{ $activeActionLabel }}</span>
                    <a href="{{ route('admin.activity-logs.user-history', $user->id) }}"
                        class="btn btn-sm btn-link text-muted">
                        <i class="fas fa-times"></i> {{ __('activity.show_all_actions') }}
                    </a>
                </span>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:30%;">{{ __('activity.action') }}</th>
                            <th>{{ __('activity.detail') }}</th>
                            <th style="width:200px;">{{ __('activity.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <td>{{ $actionLabels[$activity->action] ?? $activity->action }}</td>
                                <td>
                                    @if (is_array($activity->metadata) && isset($activity->metadata['learningUnitId']))
                                        {{ __('activity.learning_unit') }}:
                                        {{ $activity->learning_unit_name ?? ('#' . $activity->metadata['learningUnitId']) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ optional($activity->created_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">{{ __('activity.no_activities') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
