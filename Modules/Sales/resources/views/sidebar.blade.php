<li class="{{ isRoute(['admin.sales.index', 'admin.pos'], 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Sales') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul class="mm-collapse {{ isRoute(['admin.sales.*', 'admin.pos', 'admin.sales.return.list'], 'mm-show') }}">
        <li>
            <a href="{{ route('admin.pos') }}" class="{{ isRoute('admin.pos', 'mm-active') }}">
                {{ __('Pos') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.sales.index') }}" class="{{ isRoute('admin.sales.*', 'mm-active') }}">
                {{ __('Manage Sales') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.sales.return.list') }}"
                class="{{ isRoute('admin.sales.return.list', 'mm-active') }}">
                {{ __('Sales Return List') }}
            </a>
        </li>
    </ul>
</li>
