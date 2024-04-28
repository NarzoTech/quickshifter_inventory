@php
    $header_admin = Auth::guard('admin')->user();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="" type="image/x-icon">
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    @yield('title')
    <link rel="icon" href="{{ asset($setting->favicon) }}">
    @include('admin.partials.styles')
    @stack('css')
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <div class="mr-auto form-inline">
                    <ul class="mr-3 navbar-nav d-flex align-items-center">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i
                                    class="fas fa-bars"></i></a></li>
                        <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-none"><i
                                    class="fas fa-search"></i></a></li>
                        @if (Module::isEnabled('Language') && Route::has('set-language'))
                            <form id="setLanguageHeader" action="{{ route('set-language') }}">
                                <select class="bg-transparent form-control-sm border-light text-light" name="code">
                                    @forelse (allLanguages() as $language)
                                        <option class="text-dark" value="{{ $language->code }}"
                                            {{ getSessionLanguage() == $language->code ? 'selected' : '' }}>
                                            {{ $language->name }}
                                        </option>
                                    @empty
                                        <option value="en" {{ getSessionLanguage() == 'en' ? 'selected' : '' }}>
                                            English
                                        </option>
                                    @endforelse
                                </select>
                            </form>
                        @endif
                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                    <li class="dropdown dropdown-list-toggle">
                        <a target="_blank" href="{{ route('home') }}" class="nav-link nav-link-lg">
                            <i class="fas fa-home"></i> {{ __('Visit Website') }}</i>
                        </a>
                    </li>

                    <li class="dropdown"><a href="#" data-toggle="dropdown"
                            class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            @if ($header_admin->image)
                                <img alt="image" src="{{ asset($header_admin->image) }}" class="mr-1 rounded-circle">
                            @else
                                <img alt="image" src="" class="mr-1 rounded-circle">
                            @endif

                            <div class="d-sm-none d-lg-inline-block">{{ $header_admin->name }}</div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @adminCan(['admin.profile.view', 'admin.profile.edit'])
                                <a href="{{ route('admin.edit-profile') }}" class="dropdown-item has-icon">
                                    <i class="far fa-user"></i> {{ __('Profile') }}
                                </a>
                            @endadminCan
                            <div class="dropdown-divider"></div>

                            <button class="dropdown-item has-icon text-danger"
                                onclick="event.preventDefault();
                                document.getElementById('admin-logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                            </button>
                        </div>
                    </li>

                </ul>
            </nav>

            @if (request()->routeIs(
                    'admin.general-setting',
                    'admin.crediential-setting',
                    'admin.email-configuration',
                    'admin.edit-email-template',
                    'admin.currency.*',
                    'admin.seo-setting',
                    'admin.custom-code',
                    'admin.cache-clear',
                    'admin.database-clear',
                    'admin.system-update.index',
                    'admin.admin.*',
                    'admin.languages.*',
                    'admin.basicpayment',
                    'admin.paymentgateway',
                    'admin.addons.*',
                    'admin.role.*'))
                @include('admin.settings.sidebar')
            @else
                @include('admin.sidebar')
            @endif
            @yield('admin-content')

            <footer class="main-footer">
                <div class="footer-left">
                    {{ $setting->copyright_text }}
                </div>
                <div class="footer-right">
                    <span>{{ __('version') }}: {{ $setting->version ?? '' }}</span>
                </div>
            </footer>

        </div>
    </div>

    {{-- start admin logout form --}}
    <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    {{-- end admin logout form --}}
    @include('admin.partials.javascripts')

    @stack('js')

</body>

</html>
