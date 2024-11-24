<li class="{{ isRoute(['admin.employee.*', 'admin.salary.*'], 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Employees') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul>
        <li>
            <a href="{{ route('admin.employee.index') }}" class="{{ isRoute('admin.employee.index', 'mm-active') }}">
                {{ __('Employee List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.employee.create') }}" class="{{ isRoute('admin.employee.create', 'mm-active') }}">
                {{ __('Add New Employee') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.salary.list') }}" class="{{ isRoute('admin.salary.list', 'mm-active') }}">
                {{ __('All Paid Salary') }}
            </a>
        </li>
    </ul>
</li>
