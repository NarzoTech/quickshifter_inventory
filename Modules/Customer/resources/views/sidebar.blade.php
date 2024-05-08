<li
    class="nav-item dropdown {{ isRoute(['admin.customers', 'admin.active-customers', 'admin.non-verified-customers', 'admin.banned-customers', 'admin.customer-show', 'admin.send-bulk-mail'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-users"></i><span>{{ __('Manage People') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.customers', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers') }}">
                {{ __('Customers') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.customers', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers') }}">
                {{ __('Suppliers') }}
            </a>
        </li>
    </ul>
</li>
