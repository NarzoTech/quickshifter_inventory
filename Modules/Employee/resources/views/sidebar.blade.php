<li class="nav-item dropdown {{ isRoute(['admin.employee.*'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Employees') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.employee.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.employee.index') }}">
                {{ __('Employee List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.employee.create', 'active') }}">
            <a class="nav-link" href="{{ route('admin.employee.create') }}">
                {{ __('Add New Employee') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.employee.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.employee.index') }}">
                {{ __('All Paid Salary') }}
            </a>
        </li>
    </ul>
</li>
