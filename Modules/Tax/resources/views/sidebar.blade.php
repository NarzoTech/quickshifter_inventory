<li class="{{ Route::is('admin.tax.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.tax.index') }}">
        <span>{{ __('Tax') }}</span>
    </a>
</li>
