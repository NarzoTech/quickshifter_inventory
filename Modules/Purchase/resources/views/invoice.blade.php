@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Invoice') }}</title>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <section class="page">
                <div class="row justify-content-between">
                    <div class="col-5">
                        <div>
                            <div>
                                <p class="title">{{ ucfirst($setting->app_name) }}</p>
                                <div class="property">

                                    <span class="value">
                                        <p>{{ $setting->address }}</p>
                                    </span>
                                </div>

                                <div class="property">
                                    <span class="key">Mobile:</span>
                                    <span class="value">
                                        {{ $setting->mobile }}
                                    </span>
                                </div>
                                <div class="property">
                                    <span class="key">Email:</span>
                                    <span class="value">{{ $setting->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div>
                            <p class="title">Purchase</p>
                            <div class="property">
                                <span class="key">Invoice No:</span>
                                <span class="value">{{ $purchase->invoice_number }}</span>
                            </div>
                            <div class="property">
                                <span class="key">Date:</span>
                                <span
                                    class="value">{{ now()->parse($purchase->purchase_date)->format('d - M - Y') }}</span>
                            </div>
                            <p class="subtitle">Billing To</p>

                            <div class="property">
                                <span class="key">Name:</span>
                                <span class="value">{{ $purchase->supplier->name }}</span>
                            </div>
                            <div class="property">
                                <span class="key">Address:</span>
                                <span class="value">{{ $purchase->supplier->address }}</span>
                            </div>
                            <div class="property">
                                <span class="key">Mobile:</span>
                                <span class="value">{{ $purchase->supplier->phone }}</span>
                            </div>
                            <div class="property">
                                <span class="key">Email:</span>
                                <span class="value">{{ $purchase->supplier->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 6%; border-left: none !important; border-right: none !important;"
                                    class="text-center">SL.</th>
                                <th style="width: 35%; border-left: none !important; border-right: none !important;"
                                    class="text-center">Item</th>
                                <th style="width: 23%; border-left: none !important; border-right: none !important;"
                                    class="text-center">Quantity</th>
                                <th style="width: 18%; border-left: none !important; border-right: none !important;"
                                    class="text-center">Rate</th>
                                <th style="width: 23%; border-left: none !important; border-right: none !important;"
                                    class="text-right pr-2">Total</th>
                            </tr>
                        </thead>

                        @php
                            $unit = [];
                            $subTotal = 0;
                        @endphp
                        <tbody>
                            @foreach ($purchase->purchaseDetails as $index => $details)
                                <tr>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $details->product->name }}({{ $details->product->barcode }})
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center qty" id="qty1" data-qty="">


                                        @php
                                            $unitName = $details->product->unit->name;
                                            $unitQty = isset($unit[$unitName]) ? $unit[$unitName] : 0;
                                            $newQty = $details->quantity + $unitQty;
                                            $unit[$unitName] = $newQty;

                                            $subTotal += $details->sub_total;
                                        @endphp
                                        {{ $details->quantity }} {{ $unitName }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $details->purchase_price }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-right pr-2" id="totalPriceInvoice1">
                                        {{ $details->sub_total }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <td></td>
                            <td></td>
                            <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                class="text-center qty">
                                {{ array_sum(array_values($unit)) }}
                                @foreach ($unit as $key => $value)
                                    {{ $key }} {{ $value }}
                                @endforeach
                            <td></td>
                            <td></td>
                        </tfoot>
                    </table>

                    <table class="summary-table">
                        <tbody>
                            <tr>
                                <td colspan="3" style="border: none !important"></td>
                                <td class="text-right pr-5"
                                    style="border:none !important; border-bottom: 1px solid #fff !important">
                                    Subtotal:
                                </td>
                                <td class="text-right pr-2"
                                    style="border:none !important; border-bottom: 1px solid #fff !important;">
                                    TK
                                    {{ $subTotal }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="border: none !important"></td>
                                <td class="text-right pr-5"
                                    style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important;">
                                    Other Cost:
                                </td>
                                <td class="text-right pr-2"
                                    style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important;">
                                    TK 0.00
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="border: none !important"></td>
                                <td class="text-right pr-5"
                                    style="border:none !important; border-bottom: 1px solid #fff !important">
                                    Total:
                                </td>
                                <td class="text-right pr-2"
                                    style="border:none !important; border-bottom: 1px solid #fff !important;">
                                    TK {{ $subTotal }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="border: none !important"></td>
                                <td class="text-right pr-5"
                                    style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important">
                                    Paid:</td>
                                <td class="text-right pr-2"
                                    style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important;">
                                    TK {{ $purchase->paid_amount }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="border: none !important"></td>
                                <td class="text-right pr-5"
                                    style="border:none !important; border-bottom: 1px solid #fff !important">
                                    Due:
                                </td>
                                <td class="text-right pr-2"
                                    style="border:none !important; border-bottom: 1px solid #fff !important;">
                                    TK {{ $purchase->due_amount }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-3 payment-details">
                        <div class="" style=" width: 50%">
                            <h5 class="small text-inverse font-600">Payment Details</h5>
                            <table class="table table-bordered" style="border-top: 1px solid #eee;">
                                <thead>
                                    <tr>
                                        <th style="border-left: none !important; border-right: none !important"
                                            class="text-center">Sl</th>
                                        <th style="border-left: none !important; border-right: none !important"
                                            class="text-center">Payment Method</th>
                                        <th style="border-left: none !important; border-right: none !important"
                                            class="text-center">Payment By</th>
                                        <th style="border-left: none !important; border-right: none !important"
                                            class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->payments as $index => $payment)
                                        <tr>
                                            <td style="border-left: none !important; border-right: none !important"
                                                class="text-center">{{ $index + 1 }}</td>
                                            <td style="border-left: none !important; border-right: none !important"
                                                class="text-center">{{ ucfirst($payment->account->account_type) }}</td>
                                            <td style="border-left: none !important; border-right: none !important"
                                                class="text-center">
                                                -
                                            </td>
                                            <td style="border-left: none !important; border-right: none !important"
                                                class="text-center">TK.{{ $payment->amount }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between" style="margin-top: 80px">
                        <div>
                            <p class="signature">Received By</p>
                        </div>
                        <div>
                        </div>
                        <div>
                            <p class="signature">Authorised By</p>
                        </div>
                    </div>
                </div>
                <div class="print-btn pos-share-btns d-print-none">
                    <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-print"></i> Print
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
