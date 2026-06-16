@extends('adminlte::page')

@section('title', __('admins.update_admin'))

@section('content_header')
    <div class="row mb-2 align-items-center">
        <div class="col-sm-6 d-flex align-items-center">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-link p-0 shadow-none text-secondary mr-2" title="{{ __('common.back') }}" style="transition:color 0.2s;">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="mb-0">{{ __('admins.update_admin') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('common.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">{{ __('admins.title_list') }}</a></li>
                <li class="breadcrumb-item active">{{ __('admins.update_admin') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">{{ __('admins.name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}">
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="email">{{ __('admins.email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="password">{{ __('admins.password') }}</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('admins.leave_blank_if_not_change') }}">
                        <div class="input-group-append">
                            <span class="input-group-text" style="cursor: pointer;"
                                role="button" tabindex="0"
                                onclick="togglePassword('password', 'togglePasswordIcon')"
                                onkeydown="if(event.key==='Enter'||event.key===' ')togglePassword('password','togglePasswordIcon')">
                                <i id="togglePasswordIcon" class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> {{ __('common.save') }}
                    </button>
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> {{ __('common.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon  = document.getElementById(iconId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
@stop
