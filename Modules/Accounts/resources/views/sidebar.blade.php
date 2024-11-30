<li
    class="menu-item  {{ isRoute(['admin.accounts.*', 'admin.bank.index', 'admin.cashflow', 'admin.opening-balance', 'admin.balance.transfer'], 'active open') }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class='menu-icon tf-icons bx bx-store'></i>
        <div class="text-truncate" data-i18n="{{ __('Manage Accounts') }}">{{ __('Manage Accounts') }}</div>
    </a>

    <ul class="menu-sub">
        <li class="{{ isRoute('admin.cashflow', 'active') }} menu-item">
            <a href="{{ route('admin.cashflow') }}" class="menu-link">
                {{ __('Cash Flow') }}
            </a>
        </li>

        <li class="{{ isRoute('admin.accounts.create', 'active') }} menu-item">
            <a href="{{ route('admin.accounts.create') }}" class="menu-link">
                {{ __('Create Account') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.accounts.index', 'active') }} menu-item">
            <a href="{{ route('admin.accounts.index') }}" class="menu-link">
                {{ __('Account List') }}
            </a>
        </li>

        <li class="{{ isRoute('admin.balance.transfer', 'active') }} menu-item">
            <a href="{{ route('admin.balance.transfer') }}"class="menu-link">
                {{ __('Balance Transfer') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.opening-balance', 'active') }} menu-item">
            <a href="{{ route('admin.opening-balance') }}" class="menu-link">
                {{ __('Deposit') }}/{{ __('Withdraw') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.bank.index', 'active') }} menu-item">
            <a href="{{ route('admin.bank.index') }}" class="menu-link">
                {{ __('Bank') }}
            </a>
        </li>
    </ul>
</li>
