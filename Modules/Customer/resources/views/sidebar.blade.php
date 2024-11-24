{{-- <li
    class="nav-item dropdown {{ isRoute(['admin.customers.index', 'admin.vehicle.index', 'admin.area.index', 'admin.customerGroup.index', 'admin.customers.due-receive.list'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-users"></i><span>{{ __('Manage Customer') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.customers.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers.index') }}">
                {{ __('Customers') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.customers.due-receive.list', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers.due-receive.list') }}">
                {{ __('Due Receive List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.customerGroup.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customerGroup.index') }}">
                {{ __('Customer Group') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.vehicle.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.vehicle.index') }}">
                {{ __('Vehicle List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.area.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.area.index') }}">
                {{ __('Area List') }}
            </a>
        </li>
    </ul>
</li> --}}



<li>
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-plugin"></i> {{ __('Manage Customer') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul>
        <li>
            <a href="{{ route('admin.customers.index') }}">
                <i class="metismenu-icon">
                </i>{{ __('Customers') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.customers.due-receive.list') }}">
                <i class="metismenu-icon">
                </i>{{ __('Due Receive List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.customerGroup.index') }}">
                <i class="metismenu-icon">
                </i>{{ __('Customer Group') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.vehicle.index') }}">
                <i class="metismenu-icon">
                </i>{{ __('Vehicle List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.area.index') }}">
                <i class="metismenu-icon">
                </i>{{ __('Area List') }}
            </a>
        </li>
    </ul>
</li>
