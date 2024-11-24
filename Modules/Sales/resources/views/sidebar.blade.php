{{-- <li
    class="nav-item dropdown {{ isRoute(['admin.sales.index','admin.pos'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-users"></i><span>{{ __('Sales') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.pos', 'active') }}">
            <a class="nav-link" href="{{ route('admin.pos') }}">
                {{ __('Pos') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.sales.*', 'active') }}">
            <a class="nav-link" href="{{ route('admin.sales.index') }}">
                {{ __('Manage Sales') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.sales.return.list', 'active') }}">
            <a class="nav-link" href="{{ route('admin.sales.return.list') }}">
                {{ __('Sales Return List') }}
            </a>
        </li>
    </ul>
</li> --}}



<li>
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Sales') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul>
        <li>
            <a href="{{ route('admin.pos') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Pos') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.sales.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Manage Sales') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.sales.return.list') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Sales Return List') }}
            </a>
        </li>
    </ul>
</li>
