<li
    class="nav-item dropdown {{ isRoute('admin.clubpoint-setting') || isRoute('admin.clubpoint-history') ? 'active' : '' }}">
    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-gem"></i>
        <span>{{ __('Club Point') }} </span>

    </a>
    <ul class="dropdown-menu">

        <li class="{{ isRoute('admin.clubpoint-setting') ? 'active' : '' }}"><a class="nav-link"
                href="{{ route('admin.clubpoint-setting') }}">{{ __('Configuration') }}</a></li>

        <li class="{{ isRoute('admin.clubpoint-history') ? 'active' : '' }}"><a class="nav-link"
                href="{{ route('admin.clubpoint-history') }}">{{ __('Clubpoint Logs') }}</a></li>

    </ul>
</li>
