@extends('admin.auth.app')
@section('title')
    <title>{{ __('Login') }}</title>
@endsection
@section('content')
    <div class="h-100 no-gutters row">
        <div class="d-none d-lg-block col-lg-4">
            <div class="slider-light">
                <div class="slick-slider">
                    <div>
                        <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-plum-plate"
                            tabindex="-1">
                            <div class="slide-img-bg"
                                style="background-image: url('{{ asset('backend/assets/images/originals/city.jpg') }}');">
                            </div>
                            <div class="slider-content">
                                <h3>Perfect Balance</h3>
                                <p>ArchitectUI is like a dream. Some think it's too good to be true!
                                    Extensive collection of unified React Boostrap Components and Elements.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-premium-dark"
                            tabindex="-1">
                            <div class="slide-img-bg"
                                style="background-image: url({{ asset('backend/assets/images/originals/citynights.jpg') }});">
                            </div>
                            <div class="slider-content">
                                <h3>Scalable, Modular, Consistent</h3>
                                <p>Easily exclude the components you don't require. Lightweight, consistent
                                    Bootstrap based styles across all elements and components</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-sunny-morning"
                            tabindex="-1">
                            <div class="slide-img-bg"
                                style="background-image: url({{ asset('backend/assets/images/originals/citydark.jpg') }});">
                            </div>
                            <div class="slider-content">
                                <h3>Complex, but lightweight</h3>
                                <p>We've included a lot of components that cover almost all use cases for
                                    any type of application.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-100 d-flex bg-white justify-content-center align-items-center col-md-12 col-lg-8">
            <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                <div class="app-logo"></div>
                <h4 class="mb-0">
                    <span class="d-block">Welcome back,</span>
                    <span>Please sign in to your account.</span>
                </h4>
                <div class="divider row"></div>
                <div>
                    <form novalidate="" id="adminLoginForm" action="{{ route('admin.store-login') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="email" class="">Email</label>
                                    @if (app()->isLocal() && app()->hasDebugModeEnabled())
                                        <input id="email exampleInputEmail" type="email" class="form-control"
                                            name="email" tabindex="1" autofocus value="admin@gmail.com">
                                    @else
                                        <input id="email exampleInputEmail" type="email" class="form-control"
                                            name="email" tabindex="1" autofocus value="{{ old('email') }}">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <label for="password" class="">Password</label>
                                    @if (app()->isLocal() && app()->hasDebugModeEnabled())
                                        <input id="password exampleInputPassword" type="password" class="form-control"
                                            name="password" tabindex="2" value="1234">
                                    @else
                                        <input id="password exampleInputPassword" type="password" class="form-control"
                                            name="password" tabindex="2">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="position-relative form-check"><input name="remember" id="exampleCheck" type="checkbox"
                                class="form-check-input" {{ old('remember') ? 'checked' : '' }}><label for="exampleCheck"
                                class="form-check-label">Keep me logged in</label></div>
                        <div class="divider row"></div>
                        <div class="d-flex align-items-center">
                            <div class="ml-auto"><a href="{{ route('admin.password.request') }}"
                                    class="btn-lg btn btn-link">Recover
                                    Password</a>
                                <button class="btn btn-primary btn-lg" id="adminLoginBtn" type="submit">Login to
                                    Dashboard</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
