<li class="{{ isRoute(['admin.expense.*', 'admin.expense.type.index'], 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Manage Expense') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul class="mm-collapse {{ isRoute(['admin.expense.*', 'admin.expense.type.index'], 'mm-show') }}">
        <li>
            <a href="{{ route('admin.expense.index') }}?type=new"
                class="{{ isRoute('admin.expense.create', 'mm-active') }}">
                {{ __('New Expense') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.expense.index') }}" class="{{ isRoute('admin.expense.index', 'mm-active') }}">
                {{ __('Expense List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.expense.type.index') }}"
                class="{{ isRoute('admin.expense.type.index', 'mm-active') }}">
                {{ __('Expense Type') }}
            </a>
        </li>
    </ul>
</li>
