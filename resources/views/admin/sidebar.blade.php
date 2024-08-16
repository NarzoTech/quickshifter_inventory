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

            @if (Module::isEnabled('Supplier') && checkAdminHasPermission('customer.view'))
                @include('supplier::sidebar')
            @endif

            @if (Module::isEnabled('Customer') && checkAdminHasPermission('customer.view'))
                @include('customer::sidebar')
            @endif

            @if (Module::isEnabled('Product'))
                @include('product::sidebar')
            @endif

            @if (Module::isEnabled('Purchase'))
                @include('purchase::sidebar')
            @endif

            <li class="nav-item dropdown {{ Route::is('admin.stock.index') ? 'active' : '' }}">
                <a href="javascript:void()" class="nav-link has-dropdown"><i
                        class="fas fa-box"></i><span>{{ __('Inventory') }}</span></a>

                <ul class="dropdown-menu">
                    <li class="{{ Route::is('admin.stock.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.stock.index') }}">
                            {{ __('Stock') }}
                        </a>
                    </li>
                    <li class="{{ Route::is('admin.category*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.category.index') }}">
                            {{ __('Adjustments') }}
                        </a>
                    </li>
                </ul>
            </li>

            @if (Module::isEnabled('Service'))
                @include('service::sidebar')
            @endif
            @if (Module::isEnabled('Sales'))
                @include('sales::sidebar')
            @endif

            @if (Module::isEnabled('Accounts'))
                @include('accounts::sidebar')
            @endif
            @if (Module::isEnabled('Expense'))
                @include('expense::sidebar')
            @endif

            <li
                class="nav-item dropdown {{ Route::is('admin.asset-category*') || Route::is('admin.assets*') ? 'active' : '' }}">
                <a href="javascript:void()" class="nav-link has-dropdown"><i
                        class="fas fa-box"></i><span>{{ __('Assets') }}</span></a>

                <ul class="dropdown-menu">
                    <li class="{{ Route::is('admin.assets*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.assets.index') }}">
                            {{ __('Asset List') }}
                        </a>
                    </li>
                    <li class="{{ Route::is('admin.asset-category*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.asset-category.index') }}">
                            {{ __('Asset Type') }}
                        </a>
                    </li>
                </ul>
            </li>

            @if (Module::isEnabled('Employee'))
                @include('employee::sidebar')
            @endif

            {{-- <li class="menu-header">{{ __('Manage Contents') }}</li> --}}

            {{-- @if (Module::isEnabled('Media') && checkAdminHasPermission('media.view'))
                @include('media::sidebar')
            @endif --}}




            <li class="menu-header">{{ __('Settings') }}</li>

            {{-- <li class="{{ isRoute('admin.warehouse.*', 'active') }}">
                <a class="nav-link" href="{{ route('admin.warehouse.index') }}"><i class="fas fa-store"></i>
                    <span>{{ __('Warehouse') }}</span>
                </a>
            </li>
            <li class="{{ isRoute('admin.pos.settings', 'active') }}">
                <a class="nav-link" href="{{ route('admin.pos.settings') }}"><i class="fas fa-store"></i>
                    <span>{{ __('Pos Settings') }}</span>
                </a>
            </li> --}}

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
