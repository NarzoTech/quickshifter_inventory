<li class="app-sidebar__heading">{{ __('Manage Product') }}</li>

<li
    class="{{ Route::is('admin.product.*') || Route::is('admin.unit.*') || Route::is('admin.category.*') || Route::is('admin.brand.*') ? 'mm-active' : '' }}">
    <a href="javascript:;">

        <i class="metismenu-icon pe-7s-plugin"></i> {{ __('Manage Product') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>

    <ul
        class="mm-collapse {{ Route::is('admin.product.*') || Route::is('admin.unit.*') || Route::is('admin.category.*') || Route::is('admin.brand.*') ? 'mm-show' : '' }}">
        <li>
            <a href="{{ route('admin.product.index') }}"
                class="{{ Route::is('admin.product.index') || Route::is('admin.product.edit') || Route::is('admin.product.show') ? 'mm-active' : '' }}">
                {{ __('Product List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.product.create') }}"
                class="{{ Route::is('admin.product.create') ? 'mm-active' : '' }}">
                {{ __('Add Product') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.unit.index') }}" class="{{ Route::is('admin.unit*') ? 'mm-active' : '' }}">
                {{ __('Unit Type') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.category.index') }}"
                class="{{ Route::is('admin.category*') ? 'mm-active' : '' }}">
                {{ __('Category') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.brand.index') }}" class="{{ Route::is('admin.brand*') ? 'mm-active' : '' }}">
                {{ __('Brand') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.attribute.index') }}"
                class="{{ Route::is('admin.attribute*') ? 'mm-active' : '' }}">
                {{ __('Attribute') }}
            </a>
        </li>


        <li>
            <a href="{{ route('admin.product.barcode') }}"
                class="{{ Route::is('admin.product.barcode') ? 'mm-active' : '' }}">
                {{ __('Print Barcode') }} / {{ __('Label') }}
            </a>
        </li>
    </ul>
</li>
