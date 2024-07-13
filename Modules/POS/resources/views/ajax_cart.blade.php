<table class="table table-bordered product-table">
    <thead class="text-center" style="background: #00a65a">
        <tr style="height: 25px; color: #fff;">
            <th style="padding:4px 0px; margin:0px; width: 30%;">Name</th>
            <th style="padding:4px 0px; margin:0px; width: 5%;">Qty</th>
            <th style="padding:4px 0px; margin:0px; width: 7%;">Price</th>
            <th style="padding:4px 0px; margin:0px; width: 10%;">Total</th>
            <th style="padding:4px 0px; margin:0px; width: 5%;">
                <i class="fa fa-trash"></i>
            </th>
        </tr>
    </thead>
    <tbody>
        @php
            $cumalitive_sub_total = $cumalitive_sub_total ?? 0;
        @endphp
        @foreach ($cart_contents as $cart_index => $cart_content)
            <tr>
                <td>
                    <p>{{ $cart_content['name'] }}</p>
                    @if (isset($cart_content['variant']))
                        <span>
                            {{ $cart_content['variant']['attribute'] }}
                        </span>
                    @endif
                </td>
                <td data-rowid="{{ $cart_content['rowid'] }}" class="px-3">
                    <input min="1" type="number" value="{{ $cart_content['qty'] }}"
                        class="pos_input_qty form-control">
                </td>

                <td>{{ currency($cart_content['price']) }}</td>
                @php
                    $sub_total = $cart_content['sub_total'];
                    $cumalitive_sub_total += $sub_total;
                @endphp

                <td class="row_total">{{ currency($sub_total) }}</td>
                <td>
                    <a href="javascript:;" onclick="removeCartItem('{{ $cart_content['rowid'] }}')"
                        class="d-block p-2 "><i class="fa fa-trash text-danger" aria-hidden="true"></i></a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
