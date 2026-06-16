@extends('adminlte::page')

@section('title', __('dashboard.title'))

@section('content_header')
    <div class="d-flex align-items-center mb-2">
        <h1 class="mb-0">{{ __('dashboard.overview') }}</h1>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users mr-2"></i>{{ __('dashboard.user_statistics') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalAdmins) }}" text="{{ __('dashboard.total_admins') }}"
                        icon="fas fa-user-shield" theme="danger" url="{{ route('admin.admins.index') }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalUsers) }}" text="{{ __('dashboard.total_users') }}"
                        icon="fas fa-users" theme="info" url="{{ route('admin.users.index') }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-book mr-2"></i>{{ __('dashboard.content_statistics') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalLearningUnits) }}" text="{{ __('dashboard.total_learning_units') }}"
                        icon="fas fa-graduation-cap" theme="primary" url="{{ route('admin.learning-units.index') }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalCategories) }}" text="{{ __('dashboard.total_categories') }}"
                        icon="fas fa-folder" theme="warning" url="{{ route('admin.categories.index') }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalItems) }}" text="{{ __('dashboard.total_items') }}"
                        icon="fas fa-cube" theme="success" url="{{ route('admin.items.index') }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-list mr-2"></i>{{ __('dashboard.activity_statistics') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalLogins) }}" text="{{ __('dashboard.total_logins') }}"
                        icon="fas fa-sign-in-alt" theme="info" url="{{ route('admin.activity-logs.index', ['action' => \App\Models\ActivityLog::FILTER_LOGIN_LOGOUT]) }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalLearningBehaviors) }}" text="{{ __('dashboard.total_learning_behaviors') }}"
                        icon="fas fa-graduation-cap" theme="teal" url="{{ route('admin.activity-logs.index') }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalArOpens) }}" text="{{ __('dashboard.total_ar_opens') }}"
                        icon="fas fa-vr-cardboard" theme="primary" url="{{ route('admin.activity-logs.index', ['action' => 'ar_open']) }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalVirtualPatientOpens) }}" text="{{ __('dashboard.total_virtual_patient_opens') }}"
                        icon="fas fa-user-injured" theme="warning" url="{{ route('admin.activity-logs.index', ['action' => 'virtual_patient_open']) }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <x-adminlte-small-box title="{{ number_format($totalSmartQaOpens) }}" text="{{ __('dashboard.total_smart_qa_opens') }}"
                        icon="fas fa-comments" theme="success" url="{{ route('admin.activity-logs.index', ['action' => 'smart_qa_open']) }}"
                        urlText="{{ __('dashboard.view_more') }}" />
                </div>
            </div>
        </div>
    </div>
@stop
