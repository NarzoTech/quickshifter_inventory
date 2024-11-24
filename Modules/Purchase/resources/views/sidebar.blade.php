{{-- <li>
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Manage Purchase') }}</span>
    </a>

    <ul class="dropdown-menu">
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
</li> --}}




<li>
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-plugin"></i> {{ __('Manage Purchase') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul>
        <li>
            <a href="{{ route('admin.purchase.create') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Add Purchase') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.purchase.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Manage Purchase') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.purchase.return.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Purchases Return List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.purchase.return.type.list') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Purchases Return Type') }}
            </a>
        </li>
    </ul>
</li>
