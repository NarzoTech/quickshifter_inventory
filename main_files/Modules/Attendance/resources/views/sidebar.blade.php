@if (Module::isEnabled('Attendance') && Route::has('admin.attendance.index'))
    {{-- @if (checkAdminHasPermission('attendance.list') || checkAdminHasPermission('attendance.create')) --}}
    {{-- <li class="nav-item dropdown {{ Route::is('admin.attendance.*') ? 'active' : '' }}">
            <a href="javascript:void()" class="nav-link has-dropdown"><i
                    class="fas fa-address-book"></i><span>{{ __('Member Attendance') }}</span></a>

            <ul class="dropdown-menu">
                @adminCan('attendance.create')
                    <li class="{{ Route::is('admin.attendance.create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.attendance.create') }}">
                            {{ __('Attendance') }}
                        </a>
                    </li>
                @endadminCan
                @adminCan('attendance.list')
                    <li class="{{ Route::is('admin.attendance.index') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.attendance.index') }}">
                            {{ __('Attendance Sheet') }}
                        </a>
                    </li>
                @endadminCan
            </ul>
        </li> --}}

    <li class="menu-item {{ Route::is('admin.attendance.*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class='menu-icon tf-icons bx bx-detail'></i>
            <div class="text-truncate" data-i18n="{{ __('Employee Attendance') }}">{{ __('Employee Attendance') }}</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <div class="text-truncate" data-i18n="Settings">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('admin.attendance.settings.weekdays') }}" class="menu-link">
                            <div class="text-truncate" data-i18n="Weekend Setup">{{ __('Weekend Setup') }}</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="app-ecommerce-product-add.html" class="menu-link">
                            <div class="text-truncate" data-i18n="Holiday Setup">{{ __('Holiday Setup') }}</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ Route::is('admin.attendance.create') ? 'active' : '' }}">
                <a href="{{ route('admin.attendance.create') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="{{ __('Attendance') }}">{{ __('Attendance') }}</div>
                </a>
            </li>
            <li class="menu-item {{ Route::is('admin.attendance.index') ? 'active' : '' }}">
                <a href="{{ route('admin.attendance.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="{{ __('Attendance Sheet') }}">{{ __('Attendance Sheet') }}
                    </div>
                </a>
            </li>
        </ul>
    </li>

    {{-- @endif --}}
@endif
