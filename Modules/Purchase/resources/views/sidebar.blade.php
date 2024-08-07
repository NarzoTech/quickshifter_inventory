<li class="nav-item dropdown {{ isRoute(['admin.purchase.*'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Manage Purchase') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.purchase.create', 'active') }}">
            <a class="nav-link" href="{{ route('admin.purchase.create') }}">
                {{ __('Add Purchase') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.purchase.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.purchase.index') }}">
                {{ __('Manage Purchase') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.purchase.return.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.purchase.return.index') }}">
                {{ __('Purchases Return List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.purchase.return.type.list') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.purchase.return.type.list') }}">
                {{ __('Purchases Return Type') }}
            </a>
        </li>
    </ul>
</li>
