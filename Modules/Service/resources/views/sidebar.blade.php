<li class="nav-item dropdown {{ isRoute(['admin.serviceCategory.*', 'admin.service.*'], 'active') }}">
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
</li>
