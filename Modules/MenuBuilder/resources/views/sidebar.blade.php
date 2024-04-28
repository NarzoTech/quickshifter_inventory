@if (Module::isEnabled('MenuBuilder') && Route::has('admin.menus.index'))
    <li class="{{ isRoute('admin.menus.*', 'active') }}">
        <a class="nav-link" href="{{ route('admin.menus.index', getSessionLanguage()) }}">
            <i class="fas fa-bars"></i> <span>{{ __('Manage Menu') }}</span>
        </a>
    </li>
@endif
