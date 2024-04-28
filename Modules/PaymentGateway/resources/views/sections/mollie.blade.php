<div class="tab-pane fade" id="mollie_tab" role="tabpanel">
    <form action="{{ route('admin.mollie-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label for="">{{ __('Currency Name') }}</label>
                <select name="mollie_currency_id" id="" class="form-control">
                    <option value="">{{ __('Select Currency') }}</option>
                    @foreach ($currencies as $currency)
                        <option {{ $payment_setting->mollie_currency_id == $currency->id ? 'selected' : '' }}
                            value="{{ $currency->id }}">
                            {{ $currency->currency_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="mollie_charge"
                    value="{{ $payment_setting->mollie_charge }}">
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Mollie key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="mollie_key" value="mollie-test-348949439-key">
                @else
                    <input type="text" class="form-control" name="mollie_key"
                        value="{{ $payment_setting->mollie_key }}">
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Status') }}</label>
                <select name="mollie_status" id="mollie_status" class="form-control">
                    <option {{ $payment_setting->mollie_status == 'active' ? 'selected' : '' }} value="active">
                        {{ __('Enable') }}</option>
                    <option {{ $payment_setting->mollie_status == 'inactive' ? 'selected' : '' }} value="inactive">
                        {{ __('Disable') }}</option>
                </select>
            </div>

        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Image') }}</label>
            <div>
                <img src="{{ asset($payment_setting->mollie_image) }}" alt="" class="w_200"
                    id="mollie_img_preview">
            </div>
        </div>

        <div class="form-group">
            <label for="mollie_img_input">{{ __('New Image') }}</label>
            <input type="file" name="mollie_image" id="mollie_img_input" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
