<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo ">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset($setting->logo) }}" alt="Logo">
            </span>
            {{-- <span class="app-brand-text demo menu-text fw-bold ms-2">sneat</span> --}}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <li class="menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class='menu-icon tf-icons bx bx-home-smile'></i>
                <div class="text-truncate" data-i18n="Basic">{{ __('Dashboard') }}</div>
            </a>
        </li>

        @if (Module::isEnabled('Supplier'))
            @include('supplier::sidebar')
        @endif


        @if (Module::isEnabled('Customer'))
            @include('customer::sidebar')
        @endif

        @if (Module::isEnabled('Product'))
            @include('product::sidebar')
        @endif

        @if (Module::isEnabled('Purchase'))
            @include('purchase::sidebar')
        @endif

        <li class="menu-item {{ Route::is('admin.stock.index') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons bx bx-detail'></i>
                <div class="text-truncate" data-i18n="{{ __('Inventory') }}">{{ __('Inventory') }}</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::is('admin.stock.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.stock.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="{{ __('Stock') }}">{{ __('Stock') }}</div>
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

        <li class="menu-item {{ Route::is('admin.quotation*') ? 'active open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons bx bx-list-ul'></i>
                <div class="text-truncate" data-i18n="{{ __('Quotations') }}">{{ __('Quotations') }}</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ Route::is('admin.quotation.create') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.quotation.create') }}">
                        {{ __('Add Quotation') }}
                    </a>
                </li>
                <li
                    class="menu-item {{ Route::is('admin.quotation*') && !Route::is('admin.quotation.create') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.quotation.index') }}">
                        {{ __('Quotation Manage') }}
                    </a>
                </li>
            </ul>
        </li>

        @if (Module::isEnabled('Report'))
            @include('report::sidebar')
        @endif

        @if (Module::isEnabled('Expense'))
            @include('expense::sidebar')
        @endif

        <li
            class="menu-item {{ Route::is('admin.asset-category*') || Route::is('admin.assets*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons bx bx-dollar-circle'></i>
                <div class="text-truncate" data-i18n="{{ __('Assets') }}">{{ __('Assets') }}</div>
            </a>

            <ul class="menu-sub">
                <li class="{{ Route::is('admin.assets*') ? 'active' : '' }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.assets.index') }}">
                        {{ __('Asset List') }}
                    </a>
                </li>
                <li class="{{ Route::is('admin.asset-category*') ? 'active' : '' }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.asset-category.index') }}">
                        {{ __('Asset Type') }}
                    </a>
                </li>
            </ul>
        </li>

        @if (Module::isEnabled('Employee'))
            @include('employee::sidebar')
        @endif

        @if (Module::isEnabled('Attendance'))
            @include('attendance::sidebar')
        @endif
        <li
            class="menu-item {{ isRoute(['admin.settings', 'admin.print.settings', 'admin.business*', 'admin.reset.database', 'admin.cache.clear'], 'active open') }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons bx bx-cog'></i>
                <div class="text-truncate" data-i18n="{{ __('Settings') }}">{{ __('Settings') }}</div>
            </a>

            <ul class="menu-sub">
                <li class="{{ isRoute('admin.settings', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.settings') }}">
                        {{ __('Business Settings') }}
                    </a>
                </li>
                {{-- <li class="{{ isRoute('admin.print.settings', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.print.settings') }}">
                        {{ __('Print Settings') }}
                    </a>
                </li>
                <li class="{{ isRoute('admin.business*', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.business.index') }}">
                        {{ __('Business Branches') }}
                    </a>
                </li>
                <li class="{{ isRoute('admin.notice.create', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.notice.create') }}">
                        {{ __('Notice Send') }}
                    </a>
                </li>
                <li class="{{ isRoute('admin.courier.settings', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.courier.settings') }}">
                        {{ __('Courier Settings') }}
                    </a>
                </li> --}}
                <li class="{{ isRoute('admin.reset.database', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.reset.database') }}">
                        {{ __('Reset Database') }}
                    </a>
                </li>
                <li class="{{ isRoute('admin.cache.clear', 'active') }} menu-item ">
                    <a class="menu-link" href="{{ route('admin.cache.clear') }}">
                        {{ __('Clear Cache') }}
                    </a>
                </li>
                {{-- @if (Module::isEnabled('Tax'))
                    @include('tax::sidebar')
                @endif --}}
            </ul>
        </li>

        <li class="mb-5"></li>
    </ul>
</aside>
