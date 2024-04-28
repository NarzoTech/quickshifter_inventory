<div class="tab-pane fade" id="direct_bank_tab" role="tabpanel">
    <form action="{{ route('admin.update-bank-payment') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="">{{ __('Currency Name') }}</label>
            <select name="bank_currency_id" id="" class="form-control">
                <option value="">{{ __('Select Currency') }}</option>
                @foreach ($currencies as $currency)
                    <option {{ $basic_payment->bank_currency_id == $currency->id ? 'selected' : '' }}
                        value="{{ $currency->id }}">{{ $currency->currency_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="">{{ __('Gateway charge (%)') }}</label>
            <input type="text" class="form-control" name="bank_charge" value="{{ $basic_payment->bank_charge }}">
        </div>

        <div class="form-group">
            <label for="">{{ __('Status') }}</label>
            <select name="bank_status" id="bank_status" class="form-control">
                <option {{ $basic_payment->bank_status == 'active' ? 'selected' : '' }} value="active">
                    {{ __('Enable') }}</option>
                <option {{ $basic_payment->bank_status == 'inactive' ? 'selected' : '' }} value="inactive">
                    {{ __('Disable') }}</option>
            </select>
        </div>

        <div class="form-group">
            <label for="">{{ __('Account Information') }}</label>
            <textarea name="bank_information" id="" cols="30" rows="10" class="text-area-5 form-control">{{ $basic_payment->bank_information }}</textarea>
        </div>

        <div class="form-group">
            <label for="">{{ __('Existing Image') }}</label>
            <div>
                <img src="{{ asset($basic_payment->bank_image) }}" alt="" class="w_200" id="bank_img_preview">
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('New Image') }}</label>
            <input id="bank_img_input" type="file" name="bank_image" class="form-control-file">
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>
    </form>
</div>
