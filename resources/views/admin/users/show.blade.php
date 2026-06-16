@extends('adminlte::page')

@section('title', __('users.user_details'))

@section('content_header')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6 d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-link p-0 shadow-none text-secondary mr-2"
                title="{{ __('common.back') }}" style="transition:color 0.2s;">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="mb-0">{{ __('users.user_details') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('common.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('users.title_list') }}</a></li>
                <li class="breadcrumb-item active">{{ __('users.user_details') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tbody>
                    <tr>
                        <th style="width: 30%;">{{ __('users.username') }}</th>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.cname') }}</th>
                        <td>{{ $user->cname ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.email') }}</th>
                        <td>{{ $user->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.emp_id') }}</th>
                        <td>{{ $user->emp_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.hash_id') }}</th>
                        <td>{{ $user->hash_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.unit_label') }}</th>
                        <td>{{ $user->unit_label ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.job_title') }}</th>
                        <td>{{ $user->job_title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.status') }}</th>
                        <td>
                            @if ($user->is_active)
                                <span class="badge badge-success">{{ __('common.active') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('common.inactive') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('users.last_login_at') }}</th>
                        <td>{{ optional($user->last_login_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.created_at') }}</th>
                        <td>{{ optional($user->created_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop
