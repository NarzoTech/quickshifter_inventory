@if (Module::isEnabled('SupportTicket') && Route::has('admin.support.ticket'))
    <li class="{{ isRoute('admin.support.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.support.ticket') }}">
            <i class="fas fa-ticket-alt"></i> <span>{{ __('Support Tickets') }} @if (false)
                    <small class="badge badge-info">0</small>
                @endif
            </span>
        </a>
    </li>
@endif
