<li class="nav-item dropdown {{ isRoute(['admin.expense.*', 'admin.expense.type.index'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Manage Expense') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.expense.create', 'active') }}">
            <a class="nav-link" href="{{ route('admin.expense.index') }}">
                {{ __('New Expense') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.expense.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.expense.index') }}">
                {{ __('Expense List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.expense.type.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.expense.type.index') }}">
                {{ __('Expense Type') }}
            </a>
        </li>
    </ul>
</li>
