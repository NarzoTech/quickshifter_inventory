<div class="tab-pane fade active show" id="razorpay_tab" role="tabpanel">
    <form action="{{ route('admin.razorpay-update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label for="">{{ __('Currency Name') }}</label>
                <select name="razorpay_currency_id" id="" class="form-control">
                    <option value="">{{ __('Select Currency') }}</option>
                    @foreach ($currencies as $currency)
                        <option {{ $payment_setting->razorpay_currency_id == $currency->id ? 'selected' : '' }}
                            value="{{ $currency->id }}">{{ $currency->currency_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Gateway charge (%)') }}</label>
                <input type="text" class="form-control" name="razorpay_charge"
                    value="{{ $payment_setting->razorpay_charge }}">
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Razorpay key') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="razorpay_key"
                        value="demo-razorpay-39394343-test-key">
                @else
                    <input type="text" class="form-control" name="razorpay_key"
                        value="{{ $payment_setting->razorpay_key }}">
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Razorpay secret') }}</label>
                @if (env('APP_MODE') == 'DEMO')
                    <input type="text" class="form-control" name="razorpay_secret"
                        value="demo-razorpay-8384934-test-secret">
                @else
                    <input type="text" class="form-control" name="razorpay_secret"
                        value="{{ $payment_setting->razorpay_secret }}">
                @endif
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Razorpay App Name') }}</label>
                <input type="text" class="form-control" name="razorpay_name"
                    value="{{ $payment_setting->razorpay_name }}">
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Razorpay Description') }}</label>
                <input type="text" class="form-control" name="razorpay_description"
                    value="{{ $payment_setting->razorpay_description }}">
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Theme color') }}</label>
                <input type="color" class="form-control" name="razorpay_theme_color"
                    value="{{ $payment_setting->razorpay_theme_color }}">
            </div>

            <div class="form-group col-md-6">
                <label for="">{{ __('Status') }}</label>
                <select name="razorpay_status" id="razorpay_status" class="form-control">
                    <option {{ $payment_setting->razorpay_status == 'active' ? 'selected' : '' }} value="active">
                        {{ __('Enable') }}</option>
                    <option {{ $payment_setting->razorpay_status == 'inactive' ? 'selected' : '' }} value="inactive">
                        {{ __('Disable') }}</option>
                </select>
            </div>

        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Image') }}</label>
            <div>
                <img src="{{ asset($payment_setting->razorpay_image) }}" alt="" class="w_200"
                    id="razorpay_img_preview">
            </div>
        </div>

        <div class="form-group">
            <label for="razorpay_img_input">{{ __('New Image') }}</label>
            <input type="file" name="razorpay_image" id="razorpay_img_input" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
