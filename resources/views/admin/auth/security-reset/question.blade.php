@extends('admin.auth.app')
@section('title')
    <title>{{ __('Security Question') }}</title>
@endsection
@section('content')
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-12 pt-5">
            <h4 class="mb-1">{{ __('Security Question') }}</h4>
            <p class="mb-6">{{ __('Answer your security question to reset your password.') }}</p>

            <form action="{{ route('admin.security-reset.verify-answer') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-6">
                    <label class="form-label">{{ __('Your Security Question') }}</label>
                    <div class="alert alert-secondary">
                        {{ $security_question }}
                    </div>
                </div>

                <div class="mb-6">
                    <label for="security_answer" class="form-label">{{ __('Your Answer') }}</label>
                    <input id="security_answer" type="password" class="form-control" name="security_answer"
                        tabindex="1" autofocus required placeholder="{{ __('Enter your answer') }}">
                </div>

                <button type="submit" class="btn btn-primary d-grid w-100 mb-6" tabindex="2">
                    {{ __('Verify Answer') }}
                </button>

                <div class="text-center">
                    <a href="{{ route('admin.security-reset') }}">
                        {{ __('Start Over') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
