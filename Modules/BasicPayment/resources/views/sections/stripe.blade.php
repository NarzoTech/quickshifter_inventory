<div class="tab-pane fade active show" id="stripe_payment_tab" role="tabpanel">
    <form action="{{ route('admin.update-stripe') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Currency Name') }}</label>
                    <select name="stripe_currency_id" id="" class="form-control">
                        <option value="">{{ __('Select Currency') }}</option>
                        @foreach ($currencies as $currency)
                            <option {{ $basic_payment->stripe_currency_id == $currency->id ? 'selected' : '' }}
                                value="{{ $currency->id }}">{{ $currency->currency_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ __('Gateway charge (%)') }}</label>
                    <input type="text" class="form-control" name="stripe_charge"
                        value="{{ $basic_payment->stripe_charge }}">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('Stripe Key') }}</label>
            @if (env('APP_MODE') == 'DEMO')
                <input type="text" class="form-control" name="stripe_key" value="DEMO-STRIPE-83493483-TEST-KEY">
            @else
                <input type="text" class="form-control" name="stripe_key" value="{{ $basic_payment->stripe_key }}">
            @endif

        </div>

        <div class="form-group">
            <label for="">{{ __('Stripe Secret') }}</label>
            @if (env('APP_MODE') == 'DEMO')
                <input type="text" class="form-control" name="stripe_secret" value="STRIPE-TEST98384934-SECRET-KEY">
            @else
                <input type="text" class="form-control" name="stripe_secret"
                    value="{{ $basic_payment->stripe_secret }}">
            @endif
        </div>

        <div class="form-group">
            <label for="">{{ __('Status') }}</label>
            <select name="stripe_status" id="stripe_status" class="form-control">
                <option {{ $basic_payment->stripe_status == 'active' ? 'selected' : '' }} value="active">
                    {{ __('Enable') }}</option>
                <option {{ $basic_payment->stripe_status == 'inactive' ? 'selected' : '' }} value="inactive">
                    {{ __('Disable') }}</option>
            </select>
        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Image') }}</label>
            <div>
                <img src="{{ asset($basic_payment->stripe_image) }}" alt="" class="w_200"
                    id="stripe_img_preview">
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('New Image') }}</label>
            <input id="stripe_img_input" type="file" name="stripe_image" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
