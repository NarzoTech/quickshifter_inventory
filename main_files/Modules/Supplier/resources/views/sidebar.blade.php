<li class="menu-item {{ isRoute(['admin.suppliers.*', 'admin.supplierGroup.index']) ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-file"></i>
        <div class="text-truncate" data-i18n="Layouts">{{ __('Manage Suppliers') }}</div>
    </a>

    <ul class="menu-sub">
        <li class="menu-item {{ isRoute(['admin.suppliers.index', 'admin.suppliers.ledger'], 'active') }}">
            <a href="{{ route('admin.suppliers.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Without menu">{{ __('Supplier List') }}</div>
            </a>
        </li>
        <li class="menu-item {{ isRoute('admin.suppliers.due-pay-history', 'active') }}">
            <a href="{{ route('admin.suppliers.due-pay-history') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Without navbar">{{ __('Supplier Due Paid List') }}</div>
            </a>
        </li>
        <li class="menu-item {{ isRoute('admin.supplierGroup.index', 'active') }}">
            <a href="{{ route('admin.supplierGroup.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Fluid">{{ __('Supplier Group') }}</div>
            </a>
        </li>
    </ul>
</li>
