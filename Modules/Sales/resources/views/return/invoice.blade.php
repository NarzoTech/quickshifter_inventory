@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Sales Return Invoice') }}</title>
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
                                <p class="title">{{ $setting->app_name }}</p>
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
                            <div class="property">
                                <span class="key">Note:</span>
                                <span class="value">{{ $return->note }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div>
                            <p class="title" style="font-weight: 600; color: #dc3545;">Sales Return Invoice</p>
                            <div class="property">
                                <span class="value">
                                    Return Invoice No:
                                </span>
                                <span class="value" style="font-weight: bold">
                                    {{ $return->invoice }}
                                </span>
                            </div>
                            <div class="property">
                                <span class="value">
                                    Original Sale Invoice:
                                </span>
                                <span class="value">
                                    {{ $return->sale->invoice ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="property">
                                <span class="value">
                                    Return Date:
                                </span>
                                <span class="value">
                                    {{ formatDate($return->return_date) }}
                                </span>
                            </div>
                            <div class="property">
                                <span class="value">
                                    Time:
                                </span>
                                <span class="value">
                                    {{ formatDate($return->created_at, 'h:i A') }}
                                </span>
                            </div>

                            <p class="billing-badge">Customer Details</p>
                            <div class="property">
                                <span class="key">
                                    Name:
                                </span>
                                <span class="value">
                                    {{ $return->customer->name ?? 'Guest' }}
                                </span>
                            </div>

                            <div class="property">
                                <span class="key">
                                    Mobile:
                                </span>
                                <span class="value">
                                    {{ $return->customer->phone ?? '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 5%; border-left: none !important; border-right: none !important;"
                                    class="text-center">
                                    SL.
                                </th>
                                <th style="width: 45%; border-left: none !important; border-right: none !important; padding-left: 3px;"
                                    class="text-left">
                                    Item
                                </th>
                                <th style="width: 15%; border-left: none !important; border-right: none !important;"
                                    class="text-center">
                                    Price
                                </th>
                                <th style="width: 15%; border-left: none !important; border-right: none !important;"
                                    class="text-center">
                                    Return Qty
                                </th>
                                <th style="width: 20%; border-left: none !important; border-right: none !important;"
                                    class="text-right">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalQty = 0; @endphp
                            @foreach ($return->details as $index => $detail)
                                @php $totalQty += $detail->quantity; @endphp
                                <tr>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-left">
                                        {{ $detail->product->name ?? 'N/A' }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ currency($detail->price) }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $detail->quantity }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-right">
                                        {{ currency($detail->sub_total) }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3"
                                    style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-right">
                                    Total Qty:
                                </td>
                                <td
                                    style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    {{ $totalQty }}
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important">
                                </td>
                            </tr>
                        </tbody>
                    </table>


                    <div class="row">
                        <div class="col-6">
                            <div class="invoice-watermark">
                            </div>
                        </div>
                        <div class="col-6">
                            <table class="summary-table" style="margin-bottom: 10px">
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="border: none !important">
                                        </td>
                                        <td class="text-right ps-0 pb-0"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            <b>Return Amount :</b>
                                        </td>
                                        <td class="text-right pb-0"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            <b>{{ currency($return->return_amount) }}</b>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right ps-0"
                                            style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                            Paid to Customer:</td>
                                        <td class="text-right"
                                            style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                            {{ currency($return->return_amount - $return->return_due) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" style="border: none !important">
                                        </td>
                                        <td class="text-right ps-0">
                                            Due:
                                        </td>
                                        <td class="text-right">
                                            {{ currency($return->return_due) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @php
                        $returnPaid = $return->return_amount - $return->return_due;
                    @endphp
                    @if($returnPaid > 0)
                    <div class="mt-3 payment-details">
                        <span class="block bold" style="font-size: 12px">
                            <b>
                                <span style="font-weight: bold; letter-spacing: 0.1px; font-size: 13px;">
                                    In Words:
                                </span>
                                {{ numberToWord($returnPaid) }} TK Only
                            </b>
                        </span>
                    </div>
                    @endif

                    @if($return->payments && $return->payments->count() > 0)
                    <div class="mt-3 payment-details">
                        <div style="width: 100%">
                            <h6 class="mb-2"><b>Payment Details</b></h6>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;"><b>Sl</b></th>
                                        <th style="width: 30%;"><b>Payment Method</b></th>
                                        <th style="width: 30%;"><b>Account</b></th>
                                        <th style="width: 30%;"><b>Amount</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($return->payments as $index => $payment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ ucfirst($payment->account->account_type ?? '-') }}</td>
                                            <td>{{ $payment->account->bank->name ?? '-' }}</td>
                                            <td>{{ currency($payment->amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>Total Paid:</b></td>
                                        <td><b>{{ currency($return->payments->sum('amount')) }}</b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between" style="margin-top: 80px">
                        <div>
                            <p class="signature">
                                Customer Signature
                            </p>
                        </div>
                        <div>
                        </div>
                        <div>
                            <p class="signature">
                                Authorised By
                            </p>
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

@if (request()->print)
    <script>
        window.print();
    </script>
@endif
