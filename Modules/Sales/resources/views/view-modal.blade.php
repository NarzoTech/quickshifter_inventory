<div class="modal-body">

    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            <span style="display: none;">{{ __('Business') }}:</span>
            <address>
                <strong>{{ $setting->app_name }}</strong><br>

            </address>
        </div>

        <div class="col-sm-4 invoice-col">
            {{ __('Customer') }}:
            <address>
                <strong></strong>
                <b>{{ $sale?->customer?->name ?? 'Guest' }}</b><br>
                {{ $sale?->customer?->phone ? 'Mobile: ' . $sale->customer->phone : '' }}
                <br>
                {{ $sale?->customer?->sale_note ? 'Remark: ' . $sale->customer->sale_note : '' }}
            </address>
        </div>

        <div class="col-sm-4 invoice-col">
            <b>{{ __('Invoice No') }}:</b> {{ $sale->invoice }}<br>


            <b>{{ __('Date') }}:</b>{{ $sale->order_date }}<br>
            <b>{{ __('Created By') }}</b>: {{ $sale->createdBy->name }} <br>
            <b>{{ __('Created At') }}</b>{{ __(':') }}<br> {{ $sale->created_at->format('d-m-Y h:i A') }}
        </div>
    </div>
    <hr style="margin-top: -6px;"><br>

    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="table-responsive text-center">
                <table class="table text-center bg-secondary text-white">


                    <thead class="">
                        <tr style=" background: #2dce89;">
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Unit Price') }}</th>
                            <th>{{ __('Total') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $subTotal = 0;
                        @endphp
                        @foreach ($sale->details as $index => $details)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if ($details->product_id)
                                        <a href="{{ asset($details->product->single_image) }}" target="_blank">
                                            <img style="height: 40px; width: 70px;"
                                                src='{{ asset($details->product->single_image) }}' alt="Image">
                                        </a>
                                    @else
                                        <a href="{{ asset($details->service->single_image) }}" target="_blank">
                                            <img style="height: 40px; width: 70px;"
                                                src='{{ asset($details->service->single_image) }}' alt="Image">
                                    @endif
                                    </a>
                                </td>
                                <td>
                                    @if ($details->product_id)
                                        @if ($details->source == 2)
                                            Other Income (Parts-Local market)
                                        @else
                                            {{ $details->product->name }}
                                        @endif
                                    @else
                                        {{ $details->service->name }}
                                    @endif
                                </td>
                                <td>
                                    {{ $details->quantity }}
                                    @if ($details->product_id)
                                        {{ $details->product?->unit?->name }}
                                    @endif
                                </td>
                                <td>{{ $details->price }}</td>
                                @php
                                    $subTotal += $details->sub_total;
                                @endphp
                                <td>{{ $details->sub_total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12 col-xs-12">
        </div>

        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>{{ __('Sub Total') }}: </th>
                            <td></td>
                            <td><span class="display_currency pull-right"
                                    data-currency_symbol="true">{{ currency($subTotal) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Discount') }}: </th>
                            <td></td>
                            <td><span class="display_currency pull-right"
                                    data-currency_symbol="true">{{ currency($sale->order_discount) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Total Amount') }}: </th>
                            <td></td>
                            <td><span class="display_currency pull-right"
                                    data-currency_symbol="true">{{ currency($sale->total_price) }}
                                </span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="ml-auto">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>{{ __('Total Pay') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ currency($sale->paid_amount) }}</span></td>
                            </tr>

                            <tr>
                                <th>{{ __('Final Due') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ currency($sale->due_amount) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger no-print" data-bs-dismiss="modal">{{ __('Close') }}</button>
    </div>
</div>
