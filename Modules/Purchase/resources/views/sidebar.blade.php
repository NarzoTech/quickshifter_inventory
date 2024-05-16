<li
    class="nav-item dropdown {{ isRoute(['admin.purchase.*'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Manage Purchase') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.purchase.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.purchase.index') }}">
                {{ __('Purchase List') }}
            </a>
        </li>
    </ul>
</li>
