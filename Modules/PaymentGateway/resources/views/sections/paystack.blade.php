<div class="tab-pane fade" id="paystack_tab" role="tabpanel">
    <form action="{{ route('admin.paystack-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label for="">{{ __('Currency Name') }}</label>
                <select name="paystack_currency_id" id="" class="form-control">
                    <option value="">{{ __('Select Currency') }}</option>
                    @foreach ($currencies as $currency)
                        <option {{ $payment_setting->paystack_currency_id == $currency->id ? 'selected' : '' }}
                            value="{{ $currency->id }}">
                            {{ $currency->currency_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="paystack_charge"
                    value="{{ $payment_setting->paystack_charge }}">
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Public key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="paystack_public_key"
                        value="paystack-test-348949439-public-key">
                @else
                    <input type="text" class="form-control" name="paystack_public_key"
                        value="{{ $payment_setting->paystack_public_key }}">
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Secret key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="paystack_secret_key"
                        value="demo-paystack-8384934-key-secret">
                @else
                    <input type="text" class="form-control" name="paystack_secret_key"
                        value="{{ $payment_setting->paystack_secret_key }}">
                @endif
            </div>

            <div class="form-group col-md-12">
                <label for="">{{ __('Status') }}</label>
                <select name="paystack_status" id="paystack_status" class="form-control">
                    <option {{ $payment_setting->paystack_status == 'active' ? 'selected' : '' }} value="active">
                        {{ __('Enable') }}</option>
                    <option {{ $payment_setting->paystack_status == 'inactive' ? 'selected' : '' }} value="inactive">
                        {{ __('Disable') }}</option>
                </select>
            </div>

        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Image') }}</label>
            <div>
                <img src="{{ asset($payment_setting->paystack_image) }}" alt="" class="w_200"
                    id="paystack_img_preview">
            </div>
        </div>

        <div class="form-group">
            <label for="paystack_img_input">{{ __('New Image') }}</label>
            <input type="file" name="paystack_image" id="paystack_img_input" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
