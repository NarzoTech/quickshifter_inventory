@php
    $header_admin = Auth::guard('admin')->user();
@endphp
<!DOCTYPE html>

<html lang="en"
    class="light-style layout-menu-fixed layout-compact layout-navbar-fixed {{ isRoute(['admin.pos'], 'layout-menu-collapsed') }}"
    dir="ltr" data-theme="theme-default" data-style="light" data-assets-path="{{ asset('backend/assets') }}/"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    @yield('title')

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset($setting->favicon) }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    @include('admin.layouts.styles')

    @stack('css')


    <style>
        .template-customizer-open-btn {
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            @include('admin.layouts.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                @include('admin.layouts.nav')

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->

                    <div class="container-fluid flex-grow-1 container-p-y">
                        @yield('content')
                    </div>

                    @if (!isRoute('admin.pos*'))
                        <!-- Footer -->
                        @include('admin.layouts.footer')
                    @endif
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <x-admin.delete-modal />

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    @include('admin.layouts.javascripts')



    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

</body>

</html>
