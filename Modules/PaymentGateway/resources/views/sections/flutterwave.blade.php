<div class="tab-pane fade" id="flutterwave_tab" role="tabpanel">
    <form action="{{ route('admin.flutterwave-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label for="">{{ __('Currency Name') }}</label>
                <select name="flutterwave_currency_id" id="" class="form-control">
                    <option value="">{{ __('Select Currency') }}</option>
                    @foreach ($currencies as $currency)
                        <option {{ $payment_setting->flutterwave_currency_id == $currency->id ? 'selected' : '' }}
                            value="{{ $currency->id }}">
                            {{ $currency->currency_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="flutterwave_charge"
                    value="{{ $payment_setting->flutterwave_charge }}">
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Public key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="flutterwave_public_key"
                        value="flutterwave-test-348949439-public-key">
                @else
                    <input type="text" class="form-control" name="flutterwave_public_key"
                        value="{{ $payment_setting->flutterwave_public_key }}">
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Secret key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="flutterwave_secret_key"
                        value="demo-flutterwave-8384934-key-secret">
                @else
                    <input type="text" class="form-control" name="flutterwave_secret_key"
                        value="{{ $payment_setting->flutterwave_secret_key }}">
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Flutterwave App Name') }}</label>
                <input type="text" class="form-control" name="flutterwave_app_name"
                    value="{{ $payment_setting->flutterwave_app_name }}">
            </div>


            <div class="form-group col-md-6">
                <label for="">{{ __('Status') }}</label>
                <select name="flutterwave_status" id="flutterwave_status" class="form-control">
                    <option {{ $payment_setting->flutterwave_status == 'active' ? 'selected' : '' }} value="active">
                        {{ __('Enable') }}</option>
                    <option {{ $payment_setting->flutterwave_status == 'inactive' ? 'selected' : '' }}
                        value="inactive">{{ __('Disable') }}</option>
                </select>
            </div>

        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Image') }}</label>
            <div>
                <img src="{{ asset($payment_setting->flutterwave_image) }}" alt="" class="w_200"
                    id="flutterwave_img_preview">
            </div>
        </div>

        <div class="form-group">
            <label for="flutterwave_img_input">{{ __('New Image') }}</label>
            <input type="file" name="flutterwave_image" id="flutterwave_img_input" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
