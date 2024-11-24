<li
    class="{{ isRoute(['admin.accounts.*', 'admin.bank.index', 'admin.cashflow', 'admin.opening-balance', 'admin.balance.transfer'], 'active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Manage Accounts') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul
        class="mm-collapse {{ isRoute(['admin.accounts.*', 'admin.bank.index', 'admin.cashflow', 'admin.opening-balance', 'admin.balance.transfer'], 'mm-show') }}">
        <li>
            <a href="{{ route('admin.cashflow') }}" class="{{ isRoute('admin.cashflow', 'active') }}">
                {{ __('Cash Flow') }}
            </a>
        </li>

        <li>
            <a href="{{ route('admin.accounts.create') }}" class="{{ isRoute('admin.accounts.create', 'active') }}">
                {{ __('Create Account') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.accounts.index') }}" class="{{ isRoute('admin.accounts.index', 'active') }}">
                {{ __('Account List') }}
            </a>
        </li>

        <li>
            <a href="{{ route('admin.balance.transfer') }}" class="{{ isRoute('admin.balance.transfer', 'active') }}">
                {{ __('Balance Transfer') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.opening-balance') }}" class="{{ isRoute('admin.opening-balance', 'active') }}">
                {{ __('Deposit') }}/{{ __('Withdraw') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.bank.index') }}" class="{{ isRoute('admin.bank.index', 'active') }}">
                {{ __('Bank') }}
            </a>
        </li>
    </ul>
</li>
