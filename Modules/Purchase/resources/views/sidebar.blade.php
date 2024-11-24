<li class="{{ Route::is('admin.purchase.*') ? 'mm-active' : '' }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-plugin"></i> {{ __('Manage Purchase') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul class="mm-collapse {{ Route::is('admin.purchase.*') ? 'mm-show' : '' }}">
        <li>
            <a class="nav-link" href="{{ route('admin.purchase.create') }}">
                {{ __('Add Purchase') }}
            </a>
        </li>
        <li>
            <a class="nav-link" href="{{ route('admin.purchase.index') }}">
                {{ __('Manage Purchase') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.purchase.return.index') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.purchase.return.index') }}">
                {{ __('Purchases Return List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.purchase.return.type.list') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.purchase.return.type.list') }}">
                {{ __('Purchases Return Type') }}
            </a>
        </li>
    </ul>
</li>
