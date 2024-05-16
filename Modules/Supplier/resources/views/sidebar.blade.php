<li
    class="nav-item dropdown {{ isRoute(['admin.suppliers.*'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-users"></i><span>{{ __('Manage Suppliers') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.suppliers.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.suppliers.index') }}">
                {{ __('Supplier List') }}
            </a>
        </li>
    </ul>
</li>
