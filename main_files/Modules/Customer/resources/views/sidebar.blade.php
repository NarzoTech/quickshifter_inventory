<li
    class="menu-item {{ isRoute(['admin.customers.index', 'admin.vehicle.index', 'admin.area.index', 'admin.customerGroup.index', 'admin.customers.due-receive.list'], 'active open') }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class='menu-icon tf-icons bx bx-user'></i>
        <div class="text-truncate" data-i18n="{{ __('Manage Customer') }}">{{ __('Manage Customer') }}</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ isRoute('admin.customers.index', 'active') }}">
            <a href="{{ route('admin.customers.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Landing">{{ __('Customers') }}</div>
            </a>
        </li>
        <li class="menu-item {{ isRoute('admin.customers.due-receive.list', 'active') }}">
            <a href="{{ route('admin.customers.due-receive.list') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Due Receive List') }}">{{ __('Due Receive List') }}</div>
            </a>
        </li>
        <li class="menu-item {{ isRoute('admin.customerGroup.index', 'active') }}">
            <a href="{{ route('admin.customerGroup.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Customer Group') }}">{{ __('Customer Group') }}</div>
            </a>
        </li>
        <li class="menu-item {{ isRoute('admin.vehicle.index', 'active') }}">
            <a href="{{ route('admin.vehicle.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Vehicle List') }}">{{ __('Vehicle List') }}</div>
            </a>
        </li>
        <li class="menu-item {{ isRoute('admin.area.index', 'active') }}">
            <a href="{{ route('admin.area.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Help Center">{{ __('Area List') }}</div>
            </a>
        </li>
    </ul>
</li>
