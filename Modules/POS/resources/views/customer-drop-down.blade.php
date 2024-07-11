<option value="" disabled>{{ __('Select Customer') }}</option>
<option value="walk-in-customer" selected>{{ __('walk-in-customer') }}</option>
@foreach ($customers as $customer)
    <option value="{{ $customer->id }}">{{ $customer->name }} -
        {{ $customer->phone }}</option>
@endforeach
