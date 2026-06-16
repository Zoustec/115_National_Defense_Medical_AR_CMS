@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', __('auth.admin_login'))
@section('auth_body')
    @if ($errors->any())
        <div class="text-danger text-center mb-4">
            {{ $errors->first('error') }}
        </div>
    @endif
    <form action="{{ url('admin/login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">{{ __('auth.email') }} <span class="text-danger">*</span></label>
            <div class="input-group mb-3">
                <input type="text" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="{{ __('auth.email') }}" value="{{ old('email') }}" autofocus>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                </div>
            </div>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('auth.password') }} <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="password" id="password" name="password"
                    class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('auth.password') }}">
                <div class="input-group-append">
                    <span class="input-group-text" style="cursor: pointer;"
                        role="button" tabindex="0"
                        onclick="togglePassword('password', 'togglePasswordIcon')"
                        onkeydown="if(event.key==='Enter'||event.key===' ')togglePassword('password','togglePasswordIcon')">
                        <i id="togglePasswordIcon" class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block">{{ __('auth.sign_in') }}</button>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/admin/common/common.js') }}"></script>
@endsection
