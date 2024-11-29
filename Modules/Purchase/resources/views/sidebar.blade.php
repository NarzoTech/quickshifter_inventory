<li class="menu-item {{ Route::is('admin.purchase.*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class='menu-icon tf-icons bx bx-store'></i>
        <div class="text-truncate" data-i18n="{{ __('Manage Purchase') }}">{{ __('Manage Purchase') }}</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ Route::is('admin.purchase.create') ? 'active' : '' }}">
            <a href="{{ route('admin.purchase.create') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Add Purchase') }}">{{ __('Add Purchase') }}</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('admin.purchase.index') ? 'active' : '' }}">
            <a href="{{ route('admin.purchase.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Manage Purchase') }}">{{ __('Manage Purchase') }}</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('admin.purchase.return.index') ? 'active' : '' }}">
            <a href="{{ route('admin.purchase.return.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Purchases Return List') }}">
                    {{ __('Purchases Return List') }}</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('admin.purchase.return.type.list') ? 'active' : '' }}">
            <a href="{{ route('admin.purchase.return.type.list') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Purchases Return Type') }}">
                    {{ __('Purchases Return Type') }}</div>
            </a>
        </li>
    </ul>
</li>
