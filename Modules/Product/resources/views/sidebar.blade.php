{{-- <li class="menu-header">{{ __('Manage Product') }}</li>

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

 --}}




<li>
    <a href="javascript:;">
        <i class="metismenu-icon pe-7s-plugin"></i> {{ __('Manage Product') }}
        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
    </a>
    <ul>
        <li>
            <a href="{{ route('admin.product.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Product List') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.product.create') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Add Product') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.unit.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Unit Type') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.category.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Category') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.brand.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Brand') }}
            </a>
        </li>
        <li>
            <a href="{{ route('admin.attribute.index') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Attribute') }}
            </a>
        </li>


        <li>
            <a href="{{ route('admin.product.barcode') }}">
                <i class="metismenu-icon">
                </i>
                {{ __('Print Barcode') }} / {{ __('Label') }}
            </a>
        </li>
    </ul>
</li>
