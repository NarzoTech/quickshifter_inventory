<tr data-counter="1">
    <td style="text-align: center; vertical-align: middle;">
        <select name="payment_type[]" class="form-control form-control-sm pay_by" required>
            @foreach (accountList() as $key => $list)
                <option value="{{ $key }}" @if ($key == 'cash') selected @endif
                    data-name="{{ $list }}">{{ $list }}
                </option>
            @endforeach
        </select>
    </td>
    <td style="text-align: center; vertical-align: middle;" class="account_info">
        <input type="text" name="account_id[]" value="Cash" class="form-control form-control-sm" readonly>
    </td>
    <td style="text-align: center; vertical-align: middle;">
        <input type="text" name="paying_amount[]" class="form-control form-control-sm text-center paying_amount"
            id="payingAmount" placeholder="Amount" required autocomplete="off">
    </td>
    <td style="text-align: center; vertical-align: middle;">
        <div class="btn-group btn-group-sm">
            @if (isset($add))
                <a href="javascript:0" class="btn btn-sm btn-danger remove-payment">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
            <a href="javascript:0" class="btn btn-sm btn-primary add-payment">
                <i class="fa fa-plus"></i>
            </a>
        </div>
    </td>
</tr>
