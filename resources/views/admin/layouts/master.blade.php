@php
    $header_admin = Auth::guard('admin')->user();
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @yield('title')
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />

    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">

    <link rel="stylesheet" href="{{ url('backend/assets/css/base.min.css') }}">

    {{-- bootstrap 5 cdn --}}
    <link href="{{ url('backend/assets/css/bootstrap.min.css') }}">
    <link href="{{ url('backend/assets/css/all.css') }}">

    <link rel="stylesheet" href="{{ url('backend/assets/css/dev.css') }}">

    @stack('styles')

</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar">
        <!--Header START-->
        <div class="app-header header-shadow">
            <div class="app-header__logo">
                <div class="logo-src"></div>
                <div class="header__pane ml-auto">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                            data-class="closed-sidebar">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header__menu">
                <span>
                    <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header__content">
                <div class="app-header-left">
                    <div class="search-wrapper">
                        <div class="input-holder">
                            <input type="text" class="search-input" placeholder="Type to search">
                            <button class="search-icon"><span></span></button>
                        </div>
                        <button class="close"></button>
                    </div>
                </div>
                <div class="app-header-right">
                    <div class="header-dots">

                        <li class="dropdown dropdown-list-toggle">
                            <a href="{{ route('admin.pos') }}" class="nav-link nav-link-lg">
                                <i class="fas fa-money-bill-alt"></i> {{ __('Sale Report') }}</i>
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

                    </div>

                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="btn-group">
                                        <a data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            class="p-0 btn">
                                            @if ($header_admin->image)
                                                <img width="42" class="rounded-circle"
                                                    src="{{ asset($header_admin->image) }}" alt="">
                                            @else
                                                <img width="42" class="rounded-circle" src=""
                                                    alt="">
                                            @endif
                                            <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                        </a>
                                        <div tabindex="-1" role="menu" aria-hidden="true"
                                            class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">
                                            <div class="dropdown-menu-header">
                                                <div class="dropdown-menu-header-inner bg-info">
                                                    <div class="menu-header-image opacity-2"
                                                        style="background-image: url('{{ asset('/assets/images/dropdown-header/city3.jpg') }}');">
                                                    </div>
                                                    <div class="menu-header-content text-left">
                                                        <div class="widget-content p-0">
                                                            <div class="widget-content-wrapper">
                                                                <div class="widget-content-left mr-3">
                                                                    <img width="42" class="rounded-circle"
                                                                        src="{{ asset($header_admin->image) }}"
                                                                        alt="">
                                                                </div>
                                                                <div class="widget-content-left">
                                                                    <div class="widget-heading">
                                                                        {{ $header_admin->name }}
                                                                    </div>
                                                                    <div class="widget-subheading opacity-8">
                                                                        {{ $header_admin->getRoleNames()->first() }}
                                                                    </div>
                                                                </div>
                                                                <div class="widget-content-right mr-2">
                                                                    <button
                                                                        class="btn-pill btn-shadow btn-shine btn btn-focus"
                                                                        onclick="event.preventDefault();
                                document.getElementById('admin-logout-form').submit();">{{ __('Logout') }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="scroll-area-xs" style="height: 150px;">
                                                <div class="scrollbar-container ps">
                                                    <ul class="nav flex-column">
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0);" class="nav-link">Profile
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a href="javascript:void(0);" class="nav-link">Settings
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="widget-content-left  ml-3 header-user-info">
                                    <div class="widget-heading">
                                        {{ $header_admin->name }}
                                    </div>
                                    <div class="widget-subheading">
                                        {{ $header_admin->getRoleNames()->first() }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Header END-->
        <!--THEME OPTIONS START-->
        @include('admin.layouts.theme-settings')
        <!--THEME OPTIONS END-->
        <div class="app-main">
            <div class="app-sidebar sidebar-shadow">
                <div class="app-header__logo">
                    <div class="logo-src"></div>
                    <div class="header__pane ml-auto">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="app-header__mobile-menu">
                    <div>
                        <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="app-header__menu">
                    <span>
                        <button type="button"
                            class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                            <span class="btn-icon-wrapper">
                                <i class="fa fa-ellipsis-v fa-w-6"></i>
                            </span>
                        </button>
                    </span>
                </div>
                <div class="scrollbar-sidebar">
                    @include('admin.layouts.sidebar')
                </div>
            </div>
            <div class="app-main__outer">
                <div class="app-main__inner">
                    @yield('content')
                </div>
                @include('admin.layouts.footer')
            </div>
        </div>
    </div>

    <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    @include('admin.layouts.javascripts')

</body>

</html>
