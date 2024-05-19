<li class="nav-item dropdown {{ isRoute(['admin.accounts.*'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Manage Accounts') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.accounts.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.accounts.index') }}">
                {{ __('Create Account') }}
            </a>
        </li>
    </ul>
</li>
