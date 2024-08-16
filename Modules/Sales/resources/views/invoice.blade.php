@extends('admin.master_layout')
@section('title')
    <title>{{ __('Invoice') }}</title>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
@endpush

@section('admin-content')
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
                                        <p>Shop No - 01, Plot - 02, Road - 09, Sector -15/D, Uttara, Dhaka-1230</p>
                                    </span>
                                </div>

                                <div class="property">
                                    <span class="key">Mobile:</span>
                                    <span class="value">
                                        +880 1787871041
                                    </span>
                                </div>
                                <div class="property">
                                    <span class="key">Email:</span>
                                    <span class="value">quickshifter21@gmail.com</span>
                                </div>




                            </div>
                            <div class="property">
                                <span class="key">Sold By:</span>
                                <span class="value">{{ $sale->createdBy->name }}</span>
                            </div>
                            <div class="property">
                                <span class="value">
                                    <span class="key">Remark:</span>
                                    {{ $sale->notes }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div>
                            <p class="title" style="font-weight: 600;">Invoice</p>
                            <div class="property">
                                <span class="value">
                                    Invoice No:
                                </span>
                                <span class="value" style="font-weight: bold">
                                    {{ $sale->invoice }}
                                </span>
                            </div>
                            <div class="property">
                                <span class="value">
                                    Date:
                                </span>
                                <span class="value">
                                    {{ now()->parse($sale->order_date)->format('d - M - Y') }}
                                </span>
                            </div>
                            <div class="property">
                                <span class="value">
                                    Time:
                                </span>
                                <span class="value">
                                    {{ $sale->created_at->format('h:i A') }}
                                </span>
                            </div>

                            <p class="billing-badge">Billing To</p>
                            <div class="property">
                                <span class="key">
                                    Name:
                                </span>
                                <span class="value">
                                    {{ $sale->customer->name ?? 'Guest' }}
                                </span>
                            </div>


                            <div class="property">
                                <span class="key">
                                    Mobile:
                                </span>
                                <span class="value">
                                    {{ $sale->customer->phone ?? '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%; border-left: none !important; border-right: none !important;"
                                    class="text-center">
                                    SL.
                                </th>
                                <th style="width: 5%; border-left: none !important; border-right: none !important; padding-left: 3px;"
                                    class="text-left">
                                    Item
                                </th>
                                <th style="width: 40%; border-left: none !important; border-right: none !important;"
                                    class="text-left">

                                </th>
                                <th style="width: 10%; border-left: none !important; border-right: none !important; text-align:center"
                                    class="text-center">
                                    Warranty
                                </th>
                                <th style="width: 10%; border-left: none !important; border-right: none !important;"
                                    class="text-center">
                                    Price
                                </th>
                                <th style="width: 15%; border-left: none !important; border-right: none !important;"
                                    class="text-center">

                                    Quantity
                                </th>
                                <th style="width: 15%; border-left: none !important; border-right: none !important;"
                                    class="text-right pr-2">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->products as $index => $details)
                                <tr>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        style="border-left: none !important; border-right: none !important; border-top: none !important;">
                                        <img src="{{ asset($details->product->singleImage) }}"
                                            style="width: 30px; height: 30px;" />
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-left">
                                        {{ $details->product->name }}({{ $details->product->barcode }})
                                    </td>

                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $details->product->warranty ?? 'N/A' }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center pr-2">
                                        {{ $details->price }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center qty" id="qty1" data-qty="1">
                                        {{ $details->quantity }} {{ $details->product?->unit->name ?? '' }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-right pr-2" id="totalPriceInvoice1">
                                        {{ $details->sub_total }}
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($sale->services as $index => $details)
                                <tr>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    <td
                                        style="border-left: none !important; border-right: none !important; border-top: none !important;">
                                        <img src="{{ asset($details->service->singleImage) }}"
                                            style="width: 30px; height: 30px;" />
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-left">
                                        {{ $details->service->name }}
                                    </td>

                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        N/A
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center pr-2">
                                        {{ $details->price }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center qty" id="qty1" data-qty="1">
                                        {{ $details->quantity }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-right pr-2" id="totalPriceInvoice1">
                                        {{ $details->sub_total }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4"
                                    style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-right">
                                    Total Qty:
                                </td>
                                <td colspan="2"
                                    style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-left">
                                    {{ $sale->quantity }}
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
                                        <td class="text-right pr-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            Subtotal :
                                        </td>
                                        @php
                                            $subTotal = array_sum($sale->details->pluck('sub_total')->toArray());
                                        @endphp
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            TK
                                            {{ $subTotal }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right pr-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            Discount:</td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            TK
                                            {{ $sale->order_discount }}
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right pr-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            Previous Due:</td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            TK
                                            {{ $sale->customer->due->sum('due_amount') }}
                                        </td>
                                    </tr> --}}


                                    <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right pr-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            Total:
                                        </td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">

                                            TK
                                            {{ $subTotal - $sale->order_discount }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right pr-5" style="border:none !important; ">
                                            Paid:
                                        </td>
                                        <td class="text-right pr-2" style="border:none !important;">
                                            TK
                                            {{ $sale->paid_amount }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" style="border: none !important">
                                        </td>
                                        <td class="text-right pr-5"
                                            style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important">
                                        </td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="border: none !important">
                                        </td>
                                        <td class="text-right pr-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">

                                            Due:
                                        </td>

                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            TK {{ $sale->due_amount }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="border: none !important">
                                        </td>
                                        <td class="text-right pr-5"
                                            style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important">
                                        </td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important;">
                                        </td>
                                    </tr>
                                    @if ($sale->customer)
                                        <tr>
                                            <td colspan="5" style="border: none !important">
                                            </td>
                                            <td class="text-right pr-5"
                                                style="border:none !important; border-bottom: 1px solid #fff !important">
                                                Due Left/Due Remaining:
                                            </td>

                                            <td class="text-right pr-2"
                                                style="border:none !important; border-bottom: 1px solid #fff !important;">
                                                TK {{ $sale->customer->due->sum('due_amount') }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-3 payment-details">
                        <span class="block bold" style="font-size: 12px">
                            <b>
                                <span style="font-weight: bold; letter-spacing: 0.1px; font-size: 13px;">
                                    In Words:
                                </span>
                                {{ numberToWord($sale->grand_total) }} TK
                                Only


                            </b>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between" style="margin-top: 80px">
                        <div>
                            <p class="signature">
                                Received By
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
            </section>
        </div>
    </div>
@endsection
