{{-- <li
    class="nav-item dropdown {{ isRoute(['admin.accounts.*', 'admin.bank.index', 'admin.cashflow', 'admin.opening-balance', 'admin.balance.transfer'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Manage Accounts') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.cashflow', 'active') }}">
            <a class="nav-link" href="{{ route('admin.cashflow') }}">
                {{ __('Cash Flow') }}
            </a>
        </li>

        <li class="{{ isRoute('admin.accounts.create', 'active') }}">
            <a class="nav-link" href="{{ route('admin.accounts.create') }}">
                {{ __('Create Account') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.accounts.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.accounts.index') }}">
                {{ __('Account List') }}
            </a>
        </li>

        <li class="{{ isRoute('admin.balance.transfer', 'active') }}">
            <a class="nav-link" href="{{ route('admin.balance.transfer') }}">
                {{ __('Balance Transfer') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.opening-balance', 'active') }}">
            <a class="nav-link" href="{{ route('admin.opening-balance') }}">
                {{ __('Deposit') }}/{{ __('Withdraw') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.bank.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.bank.index') }}">
                {{ __('Bank') }}
            </a>
        </li>
    </ul>
</li> --}}




<li
    class="{{ isRoute(['admin.accounts.*', 'admin.bank.index', 'admin.cashflow', 'admin.opening-balance', 'admin.balance.transfer'], 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Manage Accounts') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul>
        <li>
            <a href="{{ route('admin.cashflow') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Cash Flow') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.accounts.create') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Create Account') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.accounts.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Account List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.balance.transfer') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Balance Transfer') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.opening-balance') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Deposit') }}/{{ __('Withdraw') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.bank.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Bank') }}
            </a>
        </li>
    </ul>
</li>
