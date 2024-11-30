<li class="menu-item {{ isRoute(['admin.sales.index', 'admin.pos'], 'active open') }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class='menu-icon tf-icons bx bx-basket'></i>
        <div class="text-truncate" data-i18n="{{ __('Sales') }}">{{ __('Sales') }}</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ isRoute('admin.pos', 'active') }}">
            <a href="{{ route('admin.pos') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('POS') }}">{{ __('POS') }}</div>
            </a>
        </li>
        <li class="menu-item {{ isRoute('admin.sales.*', 'active') }}">
            <a href="{{ route('admin.sales.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Manage Sales') }}">{{ __('Manage Sales') }}</div>
            </a>
        </li>
        <li class="menu-item {{ isRoute('admin.sales.return.list', 'active') }}">
            <a href="{{ route('admin.sales.return.list') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Sales Return List') }}">{{ __('Sales Return List') }}
                </div>
            </a>
        </li>
    </ul>
</li>
