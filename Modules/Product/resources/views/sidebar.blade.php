<li class="menu-header">{{ __('Manage Product') }}</li>

<li
    class="nav-item dropdown {{ Route::is('admin.product.*') || Route::is('admin.unit.*') || Route::is('admin.category.*') || Route::is('admin.brand.*') ? 'active' : '' }}">
    <a href="javascript:void()" class="nav-link has-dropdown"><i
            class="fas fa-newspaper"></i><span>{{ __('Manage Products') }}</span></a>

    <ul class="dropdown-menu">
        <li
            class="{{ Route::is('admin.product.index') || Route::is('admin.product.edit') || Route::is('admin.product.show') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.product.index') }}">
                {{ __('Product List') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.product.create') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.product.create') }}">
                {{ __('Add Product') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.unit*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.unit.index') }}">
                {{ __('Unit Type') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.category*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.category.index') }}">
                {{ __('Category') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.brand*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.brand.index') }}">
                {{ __('Brand') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.attribute*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.attribute.index') }}">
                {{ __('Attribute') }}
            </a>
        </li>


        <li class="{{ Route::is('admin.product.barcode') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.product.barcode') }}">
                {{ __('Print Barcode') }} / {{ __('Label') }}
            </a>
        </li>
    </ul>
</li>
