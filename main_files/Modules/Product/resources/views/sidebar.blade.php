<li
    class="menu-item {{ Route::is('admin.product.*') || Route::is('admin.unit.*') || Route::is('admin.category.*') || Route::is('admin.brand.*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class='menu-icon tf-icons bx bx-store'></i>
        <div class="text-truncate" data-i18n="Front Pages">{{ __('Manage Product') }}</div>
    </a>
    <ul class="menu-sub">
        <li
            class="menu-item {{ Route::is('admin.product.index') || Route::is('admin.product.edit') || Route::is('admin.product.show') ? 'active' : '' }}">
            <a href="{{ route('admin.product.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="{{ __('Product List') }}">{{ __('Product List') }}</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('admin.product.create') ? 'active' : '' }}">
            <a href="{{ route('admin.product.create') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Pricing">{{ __('Add Product') }}</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('admin.unit.*') ? 'active' : '' }}">
            <a href="{{ route('admin.unit.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Payment">{{ __('Unit Type') }}</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('admin.category.*') ? 'active' : '' }}">
            <a href="{{ route('admin.category.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Checkout">{{ __('Category') }}</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('admin.brand.*') ? 'active' : '' }}">
            <a href="{{ route('admin.brand.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Help Center">{{ __('Brand') }}</div>
            </a>
        </li>


        <li class="menu-item {{ Route::is('admin.attribute.*') ? 'active' : '' }}">
            <a href="{{ route('admin.attribute.index') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Help Center">{{ __('Attribute') }}</div>
            </a>
        </li>
        <li class="menu-item {{ Route::is('admin.product.barcode') ? 'active' : '' }}">
            <a href="{{ route('admin.product.barcode') }}" class="menu-link">
                <div class="text-truncate" data-i18n="Help Center">{{ __('Print Barcode') }} / {{ __('Label') }}
                </div>
            </a>
        </li>


    </ul>
</li>
