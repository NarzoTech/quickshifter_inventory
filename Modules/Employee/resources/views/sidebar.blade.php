<li class="nav-item dropdown {{ isRoute(['admin.accounts.*', 'admin.bank.index', 'admin.cashflow'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Manage Accounts') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.cashflow', 'active') }}">
            <a class="nav-link" href="{{ route('admin.cashflow') }}">
                {{ __('Cash Flow') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.accounts.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.accounts.index') }}">
                {{ __('Account List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.accounts.create', 'active') }}">
            <a class="nav-link" href="{{ route('admin.accounts.create') }}">
                {{ __('Create Account') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.bank.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.bank.index') }}">
                {{ __('Bank') }}
            </a>
        </li>
    </ul>
</li>
