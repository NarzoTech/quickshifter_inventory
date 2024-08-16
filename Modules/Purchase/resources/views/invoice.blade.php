@extends('admin.master_layout')
@section('title')
    <title>{{ __('Purchase List') }}</title>
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
                                <p class="title">Quick Shifter</p>
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
                        </div>
                    </div>
                    <div class="col-5">
                        <div>
                            <p class="title">Purchase</p>
                            <div class="property">
                                <span class="key">Invoice No:</span>
                                <span class="value">IN-12171722259126</span>
                            </div>
                            <div class="property">
                                <span class="key">Date:</span>
                                <span class="value">27 - Jul - 2024</span>
                            </div>
                            <p class="subtitle">Billing To</p>

                            <div class="property">
                                <span class="key">Name:</span>
                                <span class="value">Bajaj Corner</span>
                            </div>
                            <div class="property">
                                <span class="key">Address:</span>
                                <span class="value"></span>
                            </div>
                            <div class="property">
                                <span class="key">Mobile:</span>
                                <span class="value">01858444443</span>
                            </div>
                            <div class="property">
                                <span class="key">Email:</span>
                                <span class="value"></span>
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

                        <tbody>
                            <tr>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    1
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    Yamalube Semi Synthetic 10w40 indian
                                    (89458139)


                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center qty" id="qty1" data-qty="">

                                    2 1 Litre
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    635.00
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-right pr-2" id="totalPriceInvoice1">
                                    1,270.00
                                </td>
                            </tr>
                            <tr>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    2
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    Battery-5AH-Short-Hamko
                                    (79996966)


                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center qty" id="qty2" data-qty="">

                                    3 Piece
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    1,200.00
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-right pr-2" id="totalPriceInvoice2">
                                    3,600.00
                                </td>
                            </tr>
                            <tr>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    3
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    Engine Oil - Yamalube - Optima - Mineral - 10W-40 - India
                                    (21765314)


                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center qty" id="qty3" data-qty="">

                                    20 1 Litre
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-center">
                                    520.00
                                </td>
                                <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                    class="text-right pr-2" id="totalPriceInvoice3">
                                    10,400.00
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <td></td>
                            <td></td>
                            <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                class="text-center qty">
                                22 1 Litre
                                3 Piece
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
                                    15,270.00
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
                                    TK 15,270.00
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="border: none !important"></td>
                                <td class="text-right pr-5"
                                    style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important">
                                    Paid:</td>
                                <td class="text-right pr-2"
                                    style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important;">
                                    TK 0.00</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="border: none !important"></td>
                                <td class="text-right pr-5"
                                    style="border:none !important; border-bottom: 1px solid #fff !important">
                                    Due:
                                </td>
                                <td class="text-right pr-2"
                                    style="border:none !important; border-bottom: 1px solid #fff !important;">
                                    TK 15,270.00</td>
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
                                    <tr>
                                        <td style="border-left: none !important; border-right: none !important"
                                            class="text-center">1</td>
                                        <td style="border-left: none !important; border-right: none !important"
                                            class="text-center">Cash</td>
                                        <td style="border-left: none !important; border-right: none !important"
                                            class="text-center">


                                            -
                                        </td>
                                        <td style="border-left: none !important; border-right: none !important"
                                            class="text-center">0.00</td>
                                    </tr>
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
            </section>
        </div>
    </div>
@endsection
