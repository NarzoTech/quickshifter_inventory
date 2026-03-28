@if (checkAdminHasPermission('stock.adjustment.view') || checkAdminHasPermission('stock.adjustment.create'))
    <li class="{{ isRoute(['admin.stock-adjustment.*'], 'active open') }} menu-item">
        <a href="{{ route('admin.stock-adjustment.index') }}" class="menu-link">
            <div class="text-truncate" data-i18n="{{ __('Stock Adjustment') }}">{{ __('Stock Adjustment') }}</div>
        </a>
    </li>
@endif
