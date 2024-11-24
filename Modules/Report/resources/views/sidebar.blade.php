<li class="{{ isRoute(['admin.report.other-income', 'admin.report.dts'], 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('DTS') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul class="mm-collapse {{ isRoute(['admin.report.other-income', 'admin.report.dts'], 'mm-show') }}">
        <li>
            <a href="{{ route('admin.report.other-income') }}"
                class="{{ Route::is('admin.report.other-income') ? 'mm-active' : '' }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Other Income') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.dts') }}" class="{{ Route::is('admin.report.dts') ? 'mm-active' : '' }}">
                <i class="metismenu-icon">
                </i>
                {{ __('DTS') }}
            </a>
        </li>
    </ul>
</li>

<li class="{{ isRoute(['admin.other-summery.customer', 'admin.other-summery.supplier'], 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Others Summery') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul class="mm-collapse {{ isRoute(['admin.other-summery.customer', 'admin.other-summery.supplier'], 'mm-show') }}">
        <li>
            <a href="{{ route('admin.other-summery.customer') }}"
                class="{{ Route::is('admin.other-summery.customer') ? 'mm-active' : '' }}">
                {{ __('Customer Other Due') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.other-summery.supplier') }}"
                class="{{ Route::is('admin.other-summery.supplier') ? 'mm-active' : '' }}">
                {{ __('Supplier Other Due') }}
            </a>
        </li>
    </ul>
</li>

@php
    $routeList = [
        'admin.report.barcode-wise-product',
        'admin.report.barcode-sale',
        'admin.report.categories',
        'admin.report.customers',
        'admin.report.receivable',
        'admin.report.details-sale',
        'admin.report.due-date-sale',
        'admin.report.expense',
        'admin.report.master-sale',
        'admin.report.monthly-sale',
        'admin.report.profit-loss',
        'admin.report.product-sale-report',
        'admin.report.received-report',
        'admin.report.purchase',
        'admin.report.supplier',
        'admin.report.supplier-payment',
        'admin.report.salary',
    ];
@endphp
<li class="{{ isRoute($routeList, 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Reports') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul class="mm-collapse {{ isRoute($routeList, 'mm-show') }}">
        <li>
            <a href="javascript:;">
                {{ __('All Reports') }}
            </a>
        </li>

        <li>
            <a href="{{ route('admin.report.barcode-wise-product') }}"
                class="{{ Route::is('admin.report.barcode-wise-product') ? 'mm-active' : '' }}">
                {{ __('Barcode Wise Product Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.barcode-sale') }}"
                class="{{ Route::is('admin.report.barcode-sale') ? 'mm-active' : '' }}">
                {{ __('Barcode Wise Sale Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.categories') }}"
                class="{{ Route::is('admin.report.categories') ? 'mm-active' : '' }}">
                {{ __('Categories Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.customers') }}"
                class="{{ Route::is('admin.report.customers') ? 'mm-active' : '' }}">
                {{ __('Customers Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.receivable') }}"
                class="{{ Route::is('admin.report.receivable') ? 'mm-active' : '' }}">
                {{ __('Due Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.details-sale') }}"
                class="{{ Route::is('admin.report.details-sale') ? 'mm-active' : '' }}">
                {{ __('Detail Sales Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.due-date-sale') }}"
                class="{{ Route::is('admin.report.due-date-sale') ? 'mm-active' : '' }}">
                {{ __('Due Date Sales Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.dts') }}" class="{{ Route::is('admin.report.dts') ? 'mm-active' : '' }}">
                {{ __('Daily Sales Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.expense') }}"
                class="{{ Route::is('admin.report.expense') ? 'mm-active' : '' }}">
                {{ __('Expense Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.master-sale') }}"
                class="{{ Route::is('admin.report.master-sale') ? 'mm-active' : '' }}">
                {{ __('Master Sales Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.monthly-sale') }}"
                class="{{ Route::is('admin.report.monthly-sale') ? 'mm-active' : '' }}">
                {{ __('Monthly Sales Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.profit-loss') }}"
                class="{{ Route::is('admin.report.profit-loss') ? 'mm-active' : '' }}">
                {{ __('Profit/Loss Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.product-sale-report') }}"
                class="{{ Route::is('admin.report.product-sale-report') ? 'mm-active' : '' }}">
                {{ __('Products Sales Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.received-report') }}"
                class="{{ Route::is('admin.report.received-report') ? 'mm-active' : '' }}">
                {{ __('Payment Received Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.purchase') }}"
                class="{{ Route::is('admin.report.purchase') ? 'mm-active' : '' }}">
                {{ __('Purchases Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.supplier') }}"
                class="{{ Route::is('admin.report.supplier') ? 'mm-active' : '' }}">
                {{ __('Suppliers Report') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.supplier-payment') }}"
                class="{{ Route::is('admin.report.supplier-payment') ? 'mm-active' : '' }}">
                {{ __('Suppliers Payment') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report.salary') }}"
                class="{{ Route::is('admin.report.salary') ? 'mm-active' : '' }}">
                {{ __('Salary Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.report.dts') }}">
                {{ __('Stock Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.report.dts') }}">
                {{ __('Low Stock Product Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.report.dts') }}">
                {{ __('Summary') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'mm-active' : '' }}">
            <a href="{{ route('admin.report.dts') }}">
                {{ __('Yearly Sales Report') }}
            </a>
        </li>
    </ul>
</li>
