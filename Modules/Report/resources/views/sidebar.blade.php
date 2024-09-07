<li
    class="nav-item dropdown {{ Route::is('admin.report.other-income') || Route::is('admin.report.dts') ? 'active' : '' }}">
    <a href="javascript:void()" class="nav-link has-dropdown"><i
            class="fas fa-newspaper"></i><span>{{ __('Reports') }}</span></a>

    <ul class="dropdown-menu">
        <li class="{{ Route::is('admin.report.other-income') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.other-income') }}">
                {{ __('Other Income') }}
            </a>
        </li>
        <li class="{{ Route::is('admin.report.dts') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.report.dts') }}">
                {{ __('DTS') }}
            </a>
        </li>
    </ul>
</li>
