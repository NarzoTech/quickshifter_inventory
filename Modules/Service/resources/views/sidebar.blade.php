{{-- <li class="nav-item dropdown {{ isRoute(['admin.serviceCategory.*', 'admin.service.*'], 'active') }}">
    <a href="javascript:void()" class="nav-link has-dropdown">
        <i class="fas fa-cart-arrow-down"></i><span>{{ __('Manage Services') }}</span>
    </a>

    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.service.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.service.index') }}">
                {{ __('Service List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.serviceCategory.index', 'active') }}">
            <a class="nav-link" href="{{ route('admin.serviceCategory.index') }}">
                {{ __('Service Category') }}
            </a>
        </li>
    </ul>
</li> --}}


<li>
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Manage Services') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul>
        <li>
            <a href="{{ route('admin.service.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Service List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.serviceCategory.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Service Category') }}
            </a>
        </li>
    </ul>
</li>
