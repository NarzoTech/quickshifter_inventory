<li class="nav-item dropdown {{ isRoute(['admin.customers.index', 'admin.vehicle.index'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-users"></i><span>{{ __('Manage Customer') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.customers.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers.index') }}">
                {{ __('Customers') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.customers.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers.index') }}">
                {{ __('Customer Due List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.customers.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers.index') }}">
                {{ __('Customer Group') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.vehicle.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.vehicle.index') }}">
                {{ __('Vehicle List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.customers.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers.index') }}">
                {{ __('Area List') }}
            </a>
        </li>
    </ul>
</li>
