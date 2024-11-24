<li
    class="{{ isRoute(['admin.customers.index', 'admin.vehicle.index', 'admin.area.index', 'admin.customerGroup.index', 'admin.customers.due-receive.list'], 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-plugin"></i> {{ __('Manage Customer') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul
        class="mm-collapse {{ isRoute(['admin.customers.index', 'admin.vehicle.index', 'admin.area.index', 'admin.customerGroup.index', 'admin.customers.due-receive.list'], 'mm-show') }}">
        <li>
            <a href="{{ route('admin.customers.index') }}" class="{{ isRoute('admin.customers.index', 'mm-active') }}">
                {{ __('Customers') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.customers.due-receive.list') }}"
                class="{{ isRoute('admin.customers.due-receive.list', 'mm-active') }}">
                {{ __('Due Receive List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.customerGroup.index') }}"
                class="{{ isRoute('admin.customerGroup.index', 'mm-active') }}">
                {{ __('Customer Group') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.vehicle.index') }}" class="{{ isRoute('admin.vehicle.index', 'mm-active') }}">
                {{ __('Vehicle List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.area.index') }}" class="{{ isRoute('admin.area.index', 'mm-active') }}">
                {{ __('Area List') }}
            </a>
        </li>
    </ul>
</li>
