<li
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
</li>
