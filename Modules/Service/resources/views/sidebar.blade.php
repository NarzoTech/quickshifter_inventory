<li class="{{ isRoute(['admin.serviceCategory.*', 'admin.service.*'], 'mm-active') }}">
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-display2"></i> {{ __('Manage Services') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul class="mm-collapse {{ isRoute(['admin.serviceCategory.*', 'admin.service.*'], 'mm-show') }}">
        <li class="{{ isRoute('admin.service.index', 'mm-active') }}">
            <a class="nav-link" href="{{ route('admin.service.index') }}">
                {{ __('Service List') }}
            </a>
        </li>
        <li class="{{ isRoute('admin.serviceCategory.index', 'mm-active') }}">
            <a class="nav-link" href="{{ route('admin.serviceCategory.index') }}">
                {{ __('Service Category') }}
            </a>
        </li>
    </ul>
</li>
