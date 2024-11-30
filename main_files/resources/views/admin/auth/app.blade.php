<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" href="" type="image/x-icon">
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">


    @yield('title')

    <link rel="icon" href="{{ asset($setting->favicon) }}">
    <link rel="stylesheet" href="{{ url('backend/assets/css/base.min.css') }}">
    <script src="{{ asset('global/js/jquery-3.7.1.min.js') }}"></script>

    <style>
        .app-logo {
            background-image: url({{ asset($setting->logo) }});
        }
    </style>

</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow">
        <div class="app-container">
            <div class="h-100">
                @yield('content')
            </div>
        </div>
    </div>

    @include('admin.layouts.javascripts')

    <script>
        @if (Session::has('messege'))
            var type = "{{ Session::get('alert-type', 'info') }}"
            switch (type) {
                case 'info':
                    toastr.info("{{ Session::get('messege') }}");
                    break;
                case 'success':
                    toastr.success("{{ Session::get('messege') }}");
                    break;
                case 'warning':
                    toastr.warning("{{ Session::get('messege') }}");
                    break;
                case 'error':
                    toastr.error("{{ Session::get('messege') }}");
                    break;
            }
        @endif
    </script>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                toastr.error('{{ $error }}');
            </script>
        @endforeach
    @endif

</body>

</html>
