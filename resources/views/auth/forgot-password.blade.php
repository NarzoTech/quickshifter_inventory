@extends('layout')

@section('main-body')
    <form method="POST" action="{{ route('forget-password') }}">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="form-group inflanar-form-input">
                    <label>{{ __('Email') }}*</label>
                    <input class="ecom-wc__form-input" type="email" name="email" placeholder="{{ __('Email') }}">
                </div>

                @if ($setting->recaptcha_status == 'active')
                    <div class="form-group inflanar-form-input mg-top-20">
                        <div class="g-recaptcha" data-sitekey="{{ $setting->recaptcha_site_key }}"></div>
                    </div>
                @endif

                <!-- Login Button Group -->
                <div class="form-group mg-top-30">
                    <button type="submit" class="inflanar-btn"><span>{{ __('Send Reset Link') }}</span></button>
                </div>
                <div class="inflanar-signin__bottom">
                    <p class="inflanar-signin__text mg-top-20">{{ __('Go to login page') }} <a
                            href="{{ route('login') }}">{{ __('Login') }}</a></p>
                </div>
            </div>
        </div>
    </form>
@endsection
