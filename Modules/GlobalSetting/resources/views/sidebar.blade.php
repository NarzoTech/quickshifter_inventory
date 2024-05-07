<li class="menu-header">Settings</li>
<li class="{{ Route::is('admin.general-setting') ? 'active' : '' }}"><a class="nav-link"
        href="{{ route('admin.general-setting') }}"><i class="fas fa-cog"></i>
        <span>{{ __('General Settings') }}</span></a></li>

<li class="{{ Route::is('admin.crediential-setting') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.crediential-setting') }}"><i class="fas fa-key"></i>
        <span>{{ __('Credential Settings') }}</span>
    </a>
</li>

<li class="{{ Route::is('admin.email-configuration') || Route::is('admin.edit-email-template') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.email-configuration') }}"><i class="fas fa-envelope"></i>
        <span>{{ __('Email Configuration') }}</span>
    </a>
</li>

@if (Module::isEnabled('Language') && checkAdminHasPermission('language.view'))
    @include('language::sidebar')
@endif

<li class="menu-header">{{ __('Extra Settings') }}</li>

<li class="{{ Route::is('admin.cache-clear') ? 'active' : '' }}"><a class="nav-link"
        href="{{ route('admin.cache-clear') }}"><i class="fas fa-sync"></i>
        <span>{{ __('Clear cache') }}</span>
    </a></li>
