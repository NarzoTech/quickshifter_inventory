@extends('admin.auth.app')
@section('title')
    <title>{{ __('Login') }}</title>
@endsection
@section('content')
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>{{ __('Welcome to Login') }}</h4>
                        </div>

                        <div class="card-body">
                            <form novalidate="" id="adminLoginForm" action="{{ route('admin.store-login') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="email">{{ __('Email') }}</label>

                                    @if (app()->isLocal() && app()->hasDebugModeEnabled())
                                        <input id="email exampleInputEmail" type="email" class="form-control"
                                            name="email" tabindex="1" autofocus value="admin@gmail.com">
                                    @else
                                        <input id="email exampleInputEmail" type="email" class="form-control"
                                            name="email" tabindex="1" autofocus value="{{ old('email') }}">
                                    @endif

                                </div>

                                <div class="form-group">
                                    <div class="d-block">
                                        <label for="password" class="control-label">{{ __('Password') }}</label>
                                        <div class="float-right">
                                            <a href="{{ route('admin.password.request') }}" class="text-small">
                                                {{ __('Forgot Password?') }}
                                            </a>
                                        </div>
                                    </div>
                                    @if (app()->isLocal() && app()->hasDebugModeEnabled())
                                        <input id="password exampleInputPassword" type="password" class="form-control"
                                            name="password" tabindex="2" value="1234">
                                    @else
                                        <input id="password exampleInputPassword" type="password" class="form-control"
                                            name="password" tabindex="2">
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="remember" class="custom-control-input" tabindex="3"
                                            id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="remember">{{ __('Remember Me') }}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button id="adminLoginBtn" type="submit" class="btn btn-primary btn-lg btn-block"
                                        tabindex="4">
                                        {{ __('Login') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
