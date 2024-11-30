<li class="{{ isRoute(['admin.employee.*', 'admin.salary.*'], 'active open') }} menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class='menu-icon tf-icons bx bx-store'></i>
        <div class="text-truncate" data-i18n="{{ __('Employees') }}">{{ __('Employees') }}</div>
    </a>
    <ul class="menu-sub">
        <li class="{{ isRoute('admin.employee.index', 'active') }} menu-item">
            <a href="{{ route('admin.employee.index') }}" class="menu-link">
                {{ __('Employee List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.employee.create', 'active') }} menu-item">
            <a href="{{ route('admin.employee.create') }}" class="menu-link">
                {{ __('Add New Employee') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.salary.list', 'active') }} menu-item">
            <a href="{{ route('admin.salary.list') }}" class="menu-link">
                {{ __('All Paid Salary') }}
            </a>
        </li>
    </ul>
</li>
