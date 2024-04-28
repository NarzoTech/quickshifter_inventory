<div class="tab-pane fade" id="instamojo_tab" role="tabpanel">
    <form action="{{ route('admin.instamojo-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label for="">{{ __('Currency Name') }}</label>
                <select name="instamojo_currency_id" id="" class="form-control">
                    <option value="">{{ __('Select Currency') }}</option>
                    @foreach ($currencies as $currency)
                        <option {{ $payment_setting->instamojo_currency_id == $currency->id ? 'selected' : '' }}
                            value="{{ $currency->id }}">
                            {{ $currency->currency_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="instamojo_charge"
                    value="{{ $payment_setting->instamojo_charge }}">
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('API key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="instamojo_api_key"
                        value="instamojo-test-348949439-api-key">
                @else
                    <input type="text" class="form-control" name="instamojo_api_key"
                        value="{{ $payment_setting->instamojo_api_key }}">
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Auth token') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="instamojo_auth_token"
                        value="instamojo-auth-348949439-token">
                @else
                    <input type="text" class="form-control" name="instamojo_auth_token"
                        value="{{ $payment_setting->instamojo_auth_token }}">
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Account Mode') }}</label>
                <select name="instamojo_account_mode" id="instamojo_account_mode" class="form-control">
                    <option {{ $payment_setting->instamojo_account_mode == 'Sandbox' ? 'selected' : '' }}
                        value="Sandbox">{{ __('Sandbox') }}</option>
                    <option {{ $payment_setting->instamojo_account_mode == 'Live' ? 'selected' : '' }} value="Live">
                        {{ __('Live') }}</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Status') }}</label>
                <select name="instamojo_status" id="instamojo_status" class="form-control">
                    <option {{ $payment_setting->instamojo_status == 'active' ? 'selected' : '' }} value="active">
                        {{ __('Enable') }}</option>
                    <option {{ $payment_setting->instamojo_status == 'inactive' ? 'selected' : '' }} value="inactive">
                        {{ __('Disable') }}</option>
                </select>
            </div>



        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Image') }}</label>
            <div>
                <img src="{{ asset($payment_setting->instamojo_image) }}" alt="" class="w_200"
                    id="instamojo_img_preview">
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('New Image') }}</label>
            <input type="file" name="instamojo_image" id="instamojo_img_input" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
