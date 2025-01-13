@if (checkAdminHasPermission('expense.create') ||
        checkAdminHasPermission('expense.view') ||
        checkAdminHasPermission('expense.type.view'))


    <li class="{{ isRoute(['admin.expense.*', 'admin.expense.type.index'], 'active open') }} menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-store'></i>
            <div class="text-truncate" data-i18n="{{ __('Manage Expense') }}">{{ __('Manage Expense') }}</div>
        </a>

        <ul class="menu-sub">
            @adminCan('expense.create')
                <li class="{{ isRoute('admin.expense.create', 'active') }} menu-item">
                    <a href="{{ route('admin.expense.index') }}?type=new" class="menu-link">
                        {{ __('New Expense') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('expense.view')
                <li class="{{ isRoute('admin.expense.index', 'active') }} menu-item">
                    <a href="{{ route('admin.expense.index') }}" class="menu-link">
                        {{ __('Expense List') }}
                    </a>
                </li>
            @endadminCan
            @adminCan('expense.type.view')
                <li class="{{ isRoute('admin.expense.type.index', 'active') }} menu-item">
                    <a href="{{ route('admin.expense.type.index') }}" class="menu-link">
                        {{ __('Expense Type') }}
                    </a>
                </li>
            @endadminCan
        </ul>
    </li>
@endif
