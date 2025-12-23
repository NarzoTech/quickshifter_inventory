@extends('admin.auth.app')
@section('title')
    <title>{{ __('Reset Password') }}</title>
@endsection
@section('content')
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-12 pt-5">
            <h4 class="mb-1">{{ __('Reset Password') }}</h4>
            <p class="mb-6">{{ __('Enter your email to reset your password using your security question.') }}</p>

            <form action="{{ route('admin.security-reset.verify-email') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="1"
                        autofocus value="{{ old('email') }}" required>
                </div>

                <button type="submit" class="btn btn-primary d-grid w-100 mb-6" tabindex="2">
                    {{ __('Continue') }}
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
