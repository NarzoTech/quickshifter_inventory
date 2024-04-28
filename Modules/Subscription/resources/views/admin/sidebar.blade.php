<li
    class="nav-item dropdown {{ isRoute(['admin.subscription-plan.*', 'admin.plan-transaction-history', 'admin.assign-plan', 'admin.purchase-history-show', 'admin.pending-plan-transaction', 'admin.subscription-history'], 'active') }}">
    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-dollar-sign"></i>
        <span>{{ __('Subscription') }}
            <small class="mr-3 badge badge-danger">{{ __('Add') }}</small>
        </span>

    </a>
    <ul class="dropdown-menu">
        <li class="{{ isRoute('admin.subscription-plan.*', 'active') }}"><a class="nav-link"
                href="{{ route('admin.subscription-plan.index') }}">{{ __('Subscription Plan') }}</a></li>

        <li
            class="{{ isRoute(['admin.subscription-history', 'admin.purchase-history-show'], 'active') }}">
            <a class="nav-link" href="{{ route('admin.subscription-history') }}">{{ __('Subscription History') }}</a>
        </li>

        <li class="{{ isRoute('admin.plan-transaction-history', 'active') }}"><a class="nav-link"
                href="{{ route('admin.plan-transaction-history') }}">{{ __('Transaction History') }}</a></li>

        <li class="{{ isRoute('admin.pending-plan-transaction', 'active') }}"><a class="nav-link"
                href="{{ route('admin.pending-plan-transaction') }}">{{ __('Pending Transaction') }}</a></li>

        <li class="{{ isRoute('admin.assign-plan', 'active') }}"><a class="nav-link"
                href="{{ route('admin.assign-plan') }}">{{ __('Assign Plan') }}</a></li>

    </ul>
</li>
