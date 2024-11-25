<div class="app-sidebar__inner">
    <ul class="vertical-nav-menu">
        <li class="app-sidebar__heading">Menu</li>
        <li class="{{ Route::is('admin.dashboard') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <i class="metismenu-icon pe-7s-graph">
                </i>Dashboard
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

        <li class="{{ Route::is('admin.stock.index') ? 'mm-active' : '' }}">
            <a href="javascript:;">
                <i class="metismenu-icon pe-7s-display2"></i> {{ __('Inventory') }}
                <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
            </a>

            <ul class="mm-collapse {{ Route::is('admin.stock.index') ? 'mm-show' : '' }}">
                <li>
                    <a href="{{ route('admin.stock.index') }}"
                        class="{{ Route::is('admin.stock.index') ? 'mm-active' : '' }}">
                        {{ __('Stock') }}
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
        <li class="{{ Route::is('admin.quotation*') ? 'mm-active' : '' }}">
            <a href="javascript:;">
                <i class="metismenu-icon pe-7s-display2"></i> {{ __('Quotations') }}
                <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
            </a>

            <ul class="mm-collapse {{ Route::is('admin.quotation*') ? 'mm-show' : '' }}">
                <li>
                    <a href="{{ route('admin.quotation.create') }}"
                        class="{{ Route::is('admin.quotation.create') ? 'active' : '' }}">
                        {{ __('Add Quotation') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.quotation.index') }}"
                        class="{{ Route::is('admin.quotation*') && !Route::is('admin.quotation.create') ? 'active' : '' }}">
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

        <li class="{{ Route::is('admin.asset-category*') || Route::is('admin.assets*') ? 'mm-active' : '' }}">
            <a href="javascript:;">
                <i class="metismenu-icon pe-7s-display2"></i> {{ __('Assets') }}
                <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
            </a>

            <ul
                class="mm-collapse {{ Route::is('admin.asset-category*') || Route::is('admin.assets*') ? 'mm-show' : '' }}">
                <li>
                    <a href="{{ route('admin.assets.index') }}"
                        class="{{ Route::is('admin.assets*') ? 'mm-active' : '' }}">
                        {{ __('Asset List') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.asset-category.index') }}"
                        class="{{ Route::is('admin.asset-category*') ? 'mm-active' : '' }}">
                        {{ __('Asset Type') }}
                    </a>
                </li>
            </ul>
        </li>

        @if (Module::isEnabled('Employee'))
            @include('employee::sidebar')
        @endif

        <li
            class="{{ isRoute(['admin.settings', 'admin.print.settings', 'admin.business*', 'admin.reset.database', 'admin.cache.clear'], 'mm-active') }}">

            <a href="javascript:;">
                <i class="metismenu-icon pe-7s-display2"></i> {{ __('Settings') }}
                <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
            </a>


            <ul
                class="mm-collapse isRoute(['admin.settings', 'admin.print.settings', 'admin.business*', 'admin.reset.database', 'admin.cache.clear'], 'mm-show') }}">
                <li>
                    <a href="{{ route('admin.settings') }}" class="{{ isRoute('admin.settings', 'mm-active') }}">
                        {{ __('Business Settings') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.print.settings') }}"
                        class="{{ isRoute('admin.print.settings', 'mm-active') }}">
                        {{ __('Print Settings') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.business.index') }}"
                        class="{{ isRoute('admin.business*', 'mm-active') }}">
                        {{ __('Business Branches') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.notice.create') }}"
                        class="{{ isRoute('admin.notice.create', 'mm-active') }}">
                        {{ __('Notice Send') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.courier.settings') }}"
                        class="{{ isRoute('admin.courier.settings', 'mm-active') }}">
                        {{ __('Courier Settings') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reset.database') }}"
                        class="{{ isRoute('admin.reset.database', 'mm-active') }}">
                        {{ __('Reset Database') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.cache.clear') }}"
                        class="{{ isRoute('admin.cache.clear', 'mm-active') }}">
                        {{ __('Clear Cache') }}
                    </a>
                </li>
                @if (Module::isEnabled('Tax'))
                    @include('tax::sidebar')
                @endif
            </ul>
        </li>

        <li class="mb-5"></li>
    </ul>
</div>
