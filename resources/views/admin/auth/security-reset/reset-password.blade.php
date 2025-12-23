@extends('admin.auth.app')
@section('title')
    <title>{{ __('Set New Password') }}</title>
@endsection
@section('content')
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-12 pt-5">
            <h4 class="mb-1">{{ __('Set New Password') }}</h4>
            <p class="mb-6">{{ __('Enter your new password below.') }}</p>

            <form action="{{ route('admin.security-reset.reset-password') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-6 form-password-toggle">
                    <label for="password" class="form-label">{{ __('New Password') }}</label>
                    <div class="input-group input-group-merge">
                        <input id="password" type="password" class="form-control" name="password"
                            tabindex="1" autofocus required placeholder="{{ __('Enter new password') }}">
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                </div>

                <div class="mb-6 form-password-toggle">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                    <div class="input-group input-group-merge">
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                            tabindex="2" required placeholder="{{ __('Confirm new password') }}">
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary d-grid w-100 mb-6" tabindex="3">
                    {{ __('Reset Password') }}
                </button>

                <div class="text-center">
                    <a href="{{ route('admin.login') }}">
                        {{ __('Back to Login') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
