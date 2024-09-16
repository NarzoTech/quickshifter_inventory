<li
    class="nav-item dropdown {{ Route::is('admin.report.other-income') || Route::is('admin.report.dts') ? 'active' : '' }}">
    <a href="javascript:void()" class="nav-link has-dropdown"><i
            class="fas fa-newspaper"></i><span>{{ __('DTS') }}</span></a>

    <ul class="dropdown-menu">
        <li class="{{ Route::is('admin.report.other-income') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.other-income') }}">
                {{ __('Other Income') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('DTS') }}
            </a>
        </li>
    </ul>
</li>

<li
    class="nav-item dropdown {{ Route::is('admin.report.other-income') || Route::is('admin.report.dts') ? 'active' : '' }}">
    <a href="javascript:void()" class="nav-link has-dropdown"><i
            class="fas fa-newspaper"></i><span>{{ __('Reports') }}</span></a>

    <ul class="dropdown-menu">
        <li class="{{ Route::is('admin.report.other-income') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.other-income') }}">
                {{ __('All Reports') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.barcode-wise-product') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.barcode-wise-product') }}">
                {{ __('Barcode Wise Product Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.barcode-sale') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.barcode-sale') }}">
                {{ __('Barcode Wise Sale Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Categories Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Customers Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Customers Ledger') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Due Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Discounts Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Detail Sales Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Due Date Sales Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Daily Sales Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Employee Sales Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Exchange Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Expense Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Master Sales Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Monthly Sales Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Profit/Loss Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Products Sales Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Preorder Sales Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Payment Received Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Purchases Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Suppliers Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Suppliers Ledger') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Suppliers Payment') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Salary Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Stock Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Low Stock Product Report') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Summary') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('Yearly Sales Report') }}
            </a>
        </li>
    </ul>
</li>
