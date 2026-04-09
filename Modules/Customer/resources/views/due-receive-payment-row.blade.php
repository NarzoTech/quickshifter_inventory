<div class="payment-row mb-3">
    <div class="input-group">
        <select name="payment_type[]" class="form-control payment-type-select">
            @foreach (accountList() as $key => $list)
                <option value="{{ $key }}" @if ($key == 'cash') selected @endif>{{ $list }}</option>
            @endforeach
        </select>
        <div class="input-group-append">
            @if (isset($add))
                <button class="btn btn-danger remove-payment-row" type="button"><i class="fas fa-trash"></i></button>
            @else
                <button class="btn btn-success add-payment-row" type="button"><i class="fa fa-plus"></i></button>
            @endif
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-6 account-col">
            <input type="hidden" name="account_id[]" value="cash">
            <input type="text" class="form-control account-display" value="Cash" readonly>
        </div>
        <div class="col-6">
            <input type="number" name="paid_amount[]" class="form-control paid-amount-input" placeholder="Amount" step="0.01" min="0">
        </div>
    </div>
</div>
