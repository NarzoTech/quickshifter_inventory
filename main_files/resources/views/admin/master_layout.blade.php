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
            @if (!Route::is('admin.pos'))
                <div class="navbar-bg"></div>
                <nav class="navbar navbar-expand-lg main-navbar">
                    <div class="mr-auto form-inline">
                        <ul class="mr-3 navbar-nav d-flex align-items-center">
                            <li><a href="#" data-bs-toggle="sidebar" class="nav-link nav-link-lg"><i
                                        class="fas fa-bars"></i></a></li>
                            <li><a href="#" data-bs-toggle="search" class="nav-link nav-link-lg d-none"><i
                                        class="fas fa-search"></i></a></li>
                            @if (Module::isEnabled('Language') && Route::has('set-language'))
                                <form id="setLanguageHeader" action="{{ route('set-language') }}">
                                    <select class="bg-transparent form-control-sm border-light text-light"
                                        name="code">
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
                            <a href="{{ route('admin.pos') }}" class="nav-link nav-link-lg">
                                <i class="fa fa-money"></i> {{ __('Sale Report') }}</i>
                            </a>
                        </li>
                        <li class="dropdown dropdown-list-toggle">
                            <a href="{{ route('admin.stock.index') }}" class="nav-link nav-link-lg">
                                <i class="fas fa-box"></i> {{ __('Stock') }}</i>
                            </a>
                        </li>
                        <li class="dropdown dropdown-list-toggle">
                            <a href="{{ route('admin.pos') }}" class="nav-link nav-link-lg">
                                <i class="fas fa-chart-bar"></i> {{ __('Today\'s Summery') }}</i>
                            </a>
                        </li>
                        <li class="dropdown dropdown-list-toggle">
                            <a href="{{ route('admin.sales.return.list') }}" class="nav-link nav-link-lg">
                                <i class="fas fa-shopping-bag"></i> {{ __('Return Orders') }}</i>
                            </a>
                        </li>
                        <li class="dropdown dropdown-list-toggle">
                            <a href="{{ route('admin.pos') }}" class="nav-link nav-link-lg">
                                <i class="fas fa-cart-plus"></i> {{ __('POS') }}</i>
                            </a>
                        </li>

                        <li class="dropdown"><a href="#" data-bs-toggle="dropdown"
                                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                                @if ($header_admin->image)
                                    <img alt="image" src="{{ asset($header_admin->image) }}"
                                        class="mr-1 rounded-circle">
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
            @endif
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


    <div class="modal fade" tabindex="-1" role="dialog" id="deleteModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Item Delete Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are You sure want to delete this item ?') }}</p>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Delete') }}</button>

                    </form>
                </div>
            </div>
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
