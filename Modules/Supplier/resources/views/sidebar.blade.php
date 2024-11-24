<li class="{{ isRoute(['admin.suppliers.*', 'admin.supplierGroup.index']) ? 'mm-active' : '' }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-browser"></i> {{ __('Manage Suppliers') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul class="mm-collapse {{ isRoute(['admin.suppliers.*', 'admin.supplierGroup.index']) ? 'mm-show' : '' }}">
        <li>
            <a href="{{ route('admin.suppliers.index') }}"
                class="{{ isRoute(['admin.suppliers.index', 'admin.suppliers.ledger'], 'mm-active') }}">
                {{ __('Supplier List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.suppliers.due-pay-history') }}"
                class="{{ isRoute('admin.suppliers.due-pay-history', 'mm-active') }}">
                {{ __('Supplier Due Paid List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.supplierGroup.index') }}"
                class="{{ isRoute('admin.supplierGroup.index') ? 'mm-active' : '' }}">
                {{ __('Supplier Group') }}
            </a>
        </li>
    </ul>
</li>
