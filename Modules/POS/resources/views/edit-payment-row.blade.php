@foreach ($sale->payment as $index => $payment)
    <tr data-counter="1">
        <td style="text-align: center; vertical-align: middle;">
            <select name="payment_type[]" class="form-control form-control-sm pay_by" required>
                @foreach (accountList() as $key => $list)
                    <option value="{{ $key }}" @if ($key == $payment->account->account_type) selected @endif
                        data-name="{{ $list }}">{{ $list }}
                    </option>
                @endforeach
            </select>
        </td>
        <td style="text-align: center; vertical-align: middle;" class="account_info">

            @php
                $account = $accounts->where('account_type', $payment->account->account_type);
                $html = '';
            @endphp
            @if ($account)
                @if ($payment->account->account_type != 'cash')
                    <select name="account_id[]" class="form-control form-control-sm" required>
                @endif

                @foreach ($account as $key => $list)
                    @include('accounts::payment', [
                        'html' => $html,
                        'account' => $list,
                        'value' => $payment->account->account_type,
                    ])
                @endforeach
                @if ($payment->account->account_type != 'cash' && $html)
                    </select>
                @endif
            @endif
        </td>
        <td style="text-align: center; vertical-align: middle;">
            <input type="text" name="paying_amount[]" class="form-control form-control-sm text-center paying_amount"
                id="payingAmount" placeholder="Amount" required autocomplete="off" value="{{ $payment->amount }}">
        </td>
        <td style="text-align: center; vertical-align: middle;">
            <div class="btn-group btn-group-sm">
                @if ($index > 0)
                    <a href="javascript:0" class="btn btn-sm btn-danger remove-payment">
                        <i class="fa fa-trash"></i>
                    </a>
                @endif
                @if ($index == 0)
                    <a href="javascript:0" class="btn btn-sm btn-primary add-payment">
                        <i class="fa fa-plus"></i>
                    </a>
                @endif

            </div>
        </td>
    </tr>
@endforeach
