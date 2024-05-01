<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}"><img class="w-75" src="{{ asset($setting->logo) ?? '' }}"
                alt="{{ $setting->app_name ?? '' }}"></a>
        </div>

        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard') }}"><img src="{{ asset($setting->favicon) ?? '' }}"
                    alt="{{ $setting->app_name ?? '' }}"></a>
        </div>

        <ul class="sidebar-menu">
            @adminCan('dashboard.view')
                <li class="{{ isRoute('admin.dashboard', 'active') }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
            @endadminCan

            @if (Module::isEnabled('Product'))
                @include('product::sidebar')
            @endif

            <li class="menu-header">{{ __('Manage Contents') }}</li>

            @if (Module::isEnabled('Media') && checkAdminHasPermission('media.view'))
                @include('media::sidebar')
            @endif

            @if (Module::isEnabled('Customer') && checkAdminHasPermission('customer.view'))
                @include('customer::sidebar')
            @endif


            <li class="menu-header">{{ __('Settings') }}</li>

            @if (Module::isEnabled('GlobalSetting') && checkAdminHasPermission('setting.view'))
                <li class="{{ isRoute('admin.settings', 'active') }}">
                    <a class="nav-link" href="{{ route('admin.settings') }}"><i class="fas fa-cog"></i>
                        <span>{{ __('Settings') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </aside>
</div>
