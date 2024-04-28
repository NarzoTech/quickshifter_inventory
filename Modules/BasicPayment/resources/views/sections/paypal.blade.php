<div class="tab-pane fade" id="paypal_payment_tab" role="tabpanel">
    <form action="{{ route('admin.update-paypal') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Account Mode') }}</label>
                    <select name="paypal_account_mode" id="paypal_account_mode" class="form-control">
                        <option {{ $basic_payment->paypal_account_mode == 'live' ? 'selected' : '' }} value="live">
                            {{ __('Live') }}</option>
                        <option {{ $basic_payment->paypal_account_mode == 'sandbox' ? 'selected' : '' }}
                            value="sandbox">{{ __('Sandbox') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Currency Name') }}</label>
                    <select name="paypal_currency_id" id="" class="form-control">
                        <option value="">{{ __('Select Currency') }}</option>
                        @foreach ($currencies as $currency)
                            <option {{ $basic_payment->paypal_currency_id == $currency->id ? 'selected' : '' }}
                                value="{{ $currency->id }}">{{ $currency->currency_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Gateway charge (%)') }}</label>
                    <input type="text" class="form-control" name="paypal_charge"
                        value="{{ $basic_payment->paypal_charge }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Client Id') }}</label>
                    @if (env('APP_MODE') == 'DEMO')
                        <input type="text" class="form-control" name="paypal_client_id"
                            value="PAYPAL-TEST-CLIENT98934343-343-ID">
                    @else
                        <input type="text" class="form-control" name="paypal_client_id"
                            value="{{ $basic_payment->paypal_client_id }}">
                    @endif

                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Secret Key') }}</label>
                    @if (env('APP_MODE') == 'DEMO')
                        <input type="text" class="form-control" name="paypal_secret_key"
                            value="PAYPAL-TEST-398439483-SECRET-KEY">
                    @else
                        <input type="text" class="form-control" name="paypal_secret_key"
                            value="{{ $basic_payment->paypal_secret_key }}">
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Status') }}</label>
                    <select name="paypal_status" id="paypal_status" class="form-control">
                        <option {{ $basic_payment->paypal_status == 'active' ? 'selected' : '' }} value="active">
                            {{ __('Enable') }}</option>
                        <option {{ $basic_payment->paypal_status == 'inactive' ? 'selected' : '' }} value="inactive">
                            {{ __('Disable') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Image') }}</label>
            <div>
                <img src="{{ asset($basic_payment->paypal_image) }}" alt="" class="w_200"
                    id="paypal_img_preview">
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('New Image') }}</label>
            <input id="paypal_img_input" type="file" name="paypal_image" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
