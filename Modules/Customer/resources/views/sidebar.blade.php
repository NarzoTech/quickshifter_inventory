<li
    class="nav-item dropdown {{ isRoute(['admin.customers.index', 'admin.active-customers', 'admin.non-verified-customers', 'admin.banned-customers', 'admin.customer-show', 'admin.send-bulk-mail'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-users"></i><span>{{ __('Manage Customer') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.customers.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.customers.index') }}">
                {{ __('Customers') }}
            </a>
        </li>
    </ul>
</li>
