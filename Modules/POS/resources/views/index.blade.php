@extends('admin.master_layout')
@section('title')
    <title>
        {{ __('POS') }}</title>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/pos.css') }}">
    <style>
        .ui-autocomplete {
            z-index: 215000000 !important;
        }

        .theme-primary {
            background: #ad07b0 !important;
        }

        .product-table thead {
            position: sticky;
            top: -1px;
        }

        .w-21 {
            width: 21%;
        }

        .cursor-pointer {
            cursor: pointer !important;
        }

        .table:not(.table-sm):not(.table-md):not(.dataTable) td,
        .table:not(.table-sm):not(.table-md):not(.dataTable) th {
            padding: 0 5px !important;
        }

        .main-content {
            padding-left: 0px !important;
            padding-top: 0px !important;
        }

        .main-sidebar {
            display: none;
            width: 0 !important;
        }

        .pos-right-side .summary-table {
            width: calc(100% + 40px);
            margin-left: -20px;
        }

        .dis-form {
            display: none;
        }

        .dis-form input {
            width: 70px;
            text-align: center;
            border: 1px solid #E3E3E3;
            margin-left: -16px;
            outline: none;
        }

        .dis-form select {
            position: relative;
            width: 120px;
            color: #fff;
            background-color: #188ae2;
            border-radius: 4px 0 0 4px;
            padding: 5px;
            z-index: 10;
        }

        @media only screen and (max-width:767px) {
            .pos-right-side {
                padding-bottom: 10px !important;
            }
        }

        @media only screen and (max-width:480px) {
            .pos-right-side {
                padding-bottom: 5px !important;
            }

            #exchange-table {
                display: block;
            }
        }
    </style>
@endpush
@section('admin-content')

    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('POS') }}</h1>
            </div>

            <div class="section-body">

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <form id="product_search_form" class="pos_pro_search_form w-100">
                                    <div class="row">
                                        <div class="col-md-5 d-flex align-items-center">
                                            <select name="category_id" id="category_id" class="form-control select2">
                                                <option value="">{{ __('Select Category') }}</option>
                                                @if (request()->has('category_id'))
                                                    @foreach ($categories as $category)
                                                        <option
                                                            {{ request()->get('category_id') == $category->id ? 'selected' : '' }}
                                                            value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                @else
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-5 d-flex align-items-center ">
                                            <select name="brand_id" id="brand_id" class="form-control select2">
                                                <option value="">{{ __('Select brand') }}</option>
                                                @if (request()->has('brand_id'))
                                                    @foreach ($brands as $brand)
                                                        <option
                                                            {{ request()->get('brand_id') == $brand->id ? 'selected' : '' }}
                                                            value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                @else
                                                    @foreach ($categories as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-12 d-flex align-items-center mt-2">
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="{{ __('Enter Product name / SKU / Scan bar code') }}"
                                                autocomplete="off" value="{{ request()->get('name') }}">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body product_body" style="overflow: auto">

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header pos_sidebar_button">
                                <div class="row w-100">
                                    <div class="col-md-9">
                                        <select name="customer_id" id="customer_id" class="form-control select2">
                                            @include('pos::customer-drop-down')
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button data-toggle="modal" data-target="#addCustomer" type="button"
                                            class="btn btn-primary"><i class="fa fa-plus"
                                                aria-hidden="true"></i>{{ __('New') }}</button>
                                    </div>
                                    <div class="col-md-12 mt-3 address-container">
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $cumalitive_sub_total = 0;
                                    @endphp
                                    <div class="col-md-12 product-table-container">
                                        @include('pos::ajax_cart')
                                    </div>
                                </div>
                                <table id="totalTable" class="summary-table">
                                    <thead>
                                        <th width="30%"></th>
                                        <th width="20%"></th>
                                        <th width="30%"></th>
                                        <th width="20%"></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 5px 10px;border-top: 1px solid #DDD;">Items</td>
                                            <td class="text-right"
                                                style="padding: 5px 10px;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
                                                <span id="titems">{{ count($cart_contents) }}</span>
                                            </td>
                                            <td style="padding: 5px 10px;border-top: 1px solid #DDD;">Total</td>
                                            <td class="text-right"
                                                style="padding: 5px 10px;font-size: 14px; font-weight:bold;border-top: 1px solid #DDD;">
                                                <span id="total">{{ currency($cumalitive_sub_total) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px 10px;">
                                                VAT <small class="text-info">(0%)</small>
                                            </td>
                                            <td class="text-right"
                                                style="padding: 5px 10px;font-size: 14px; font-weight:bold;">
                                                <span id="ttax2">0</span>
                                            </td>
                                            <td style="padding: 5px 10px;">{{ __('Discount') }}
                                                <i class="fa fa-edit dis-tgl" style="cursor: pointer;"></i>
                                                <div class="dis-form">
                                                    <select name="discount_type" id="discount_type"
                                                        onchange="discountExist()">
                                                        <option value="1" selected>{{ __('Amount') }} (TK )</option>
                                                        <option value="2">{{ __('Percentage') }} (%)</option>
                                                    </select>
                                                    <input type="text" onchange="discountExist()"
                                                        id="discount_total_amount" value="0" step="0.1"
                                                        name="discount_total_amount" autocomplete="off" autofocus>
                                                </div>
                                            </td>

                                            <td class="text-right" style="padding: 5px 10px;font-weight:bold;">
                                                <span id="tds">0</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;"
                                                colspan="2">
                                                After Discount Price
                                            </td>
                                            <td class="text-right"
                                                style="padding:5px 10px 5px 10px; font-size: 14px;border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;"
                                                colspan="2">
                                                <span id="gtotal">{{ currency($cumalitive_sub_total) }}</span>
                                                <input type="hidden" value="0" id="business_vat">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;"
                                                colspan="2">
                                                Total Vat
                                            </td>
                                            <td class="text-right"
                                                style="padding:5px 10px 5px 10px; font-size: 14px;border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;"
                                                colspan="2">
                                                <span id="totalVat">0</span>
                                                <input type="hidden" value="0" id="business_vat">
                                            </td>
                                        </tr>
                                        <tr class="pay-row">
                                            <td
                                                style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;">
                                                Total Payable
                                                <span id="payable_amount"></span>
                                            </td>
                                            <td class="text-right" id="totalAmountWithVat" colspan="3"
                                                style="padding: 5px 10px; border-top: 1px solid #666; border-bottom: 1px solid #333; font-weight:bold; background:#333; color:#FFF;">
                                                {{ currency($cumalitive_sub_total) }}
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="pos-footer" style="z-index: 9000">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-block back-btn">
                    <i class="fa fa-backward fa-lg mt-3"></i>
                </a>
            </div>
            <h3 class="final-text">
                Total : <span id="finalTotal"> {{ currency($cumalitive_sub_total) }} </span>
            </h3>
            <div class="btn-group lg-btns">
                <div class="btn-group">
                    <button type="button" class="btn hold-list-btn" title="Hold Sale List">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn hold-btn" title="Hold Current Sale">
                        Hold
                    </button>
                </div>
                <button type="button" class="btn cancel-btn" onclick="cancel()">
                    Clear
                </button>
                <button type="button" class="btn payment-btn" onclick="openPaymentModal()">
                    Payment
                </button>
            </div>
        </footer>
    </div>


    @include('components.admin.preloader')


    <!-- Product Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog mw-100 w-75" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid load_product_modal_response">

                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- Create new user modal -->
    @include('customer::customer-modal')


    {{-- item details modal --}}
    <div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content load_item_details_modal_response">

            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="payment-modal" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="" id="checkoutForm" onSubmit="paymentSubmit(event)">
                    @csrf
                    <input type="hidden" name="order_customer_id" id="order_customer_id" value="">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed">
                                    <tbody>
                                        <tr>
                                            <td class="text-center w-10"></td>
                                            <td class="w-70">Payment Details</td>
                                            <td class="text-right w-20"></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Subtotal
                                            </th>
                                            <input type="hidden" name="sub_total" value="" autocomplete="off">
                                            <td class="text-right w-40" id="sub_totalModal">0</td>
                                        </tr>
                                        <tr class="discount-row">
                                            <th class="text-right w-60" colspan="2">
                                                Discount
                                            </th>
                                            <input type="hidden" name="discount_amount" value="0"
                                                autocomplete="off">
                                            <td class="text-right w-40" id="discount_amountModal">0.00</td>
                                        </tr>
                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Total Amount <br><small class="ng-binding">(<span id="itemModal">0</span>
                                                    items)</small>
                                            </th>
                                            <input type="hidden" name="total_amount" value="0"
                                                id="total_amount_modal_input" autocomplete="off">
                                            <input type="hidden" name=exchange_amount" value="0"
                                                id="exchange_amount_modal_input" autocomplete="off">
                                            <td class="text-right w-40" id="total_amountModal">0.00</td>
                                        </tr>

                                        {{-- <tr class="vat-row">
                                            <th class="text-right w-60" colspan="2">
                                                VAT </th>
                                            <input type="hidden" name="vat" value="0" autocomplete="off">
                                            <td class="text-right w-40" id="vatModal">0.00</td>
                                        </tr> --}}
                                        {{-- <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Paid Amount</th>
                                            <td class="text-right w-40" id="paing_amountModal">0</td>
                                        </tr>
                                         --}}
                                        {{-- <tr class="due">
                                            <th class="text-right w-60" colspan="2">
                                                <label>Previous Due</label>
                                            </th>
                                            <td class="text-right w-40" id="previous_due" data-amount="0">0</td>
                                        </tr> --}}

                                        <tr class="due">
                                            <th class="text-right w-60" colspan="2">
                                                Total Due
                                            </th>
                                            <td class="text-right w-40" id="due_amountModal">0</td>
                                        </tr>
                                        {{-- <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Total Advance
                                            </th>
                                            <td class="text-center w-40">
                                                <span id="total_advance">0</span> TK
                                            </td>
                                        </tr> --}}
                                        <tr>
                                            <th class="text-right w-60" colspan="2">
                                                Sale Date
                                            </th>
                                            <td class="text-right w-40">

                                                <input type="text" class="form-control datepicker" name="sale_date"
                                                    value="{{ date('Y-m-d') }}" autocomplete="off">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-block"
                                        style="background:#7a8882;color: #fff;font-weight: 900;font-size: 18px;">
                                        Total Amount:
                                        <span style="color: #ffd400;font-size: 22px;" id="total_amountModal2">0</span>
                                        TK
                                    </button>
                                </div>
                            </div>

                            <div>
                                <table class="table payment mt-2">
                                    <thead>
                                        <tr>
                                            <td style="vertical-align: middle; width: 30%; text-transform: capitalize">
                                                Payment Type
                                            </td>
                                            <td style="vertical-align: middle; width: 30%; text-transform: capitalize">
                                                Payment Option
                                            </td>
                                            <td style="vertical-align: middle; width: 30%; text-transform: capitalize">
                                                Amount Received
                                            </td>
                                            <td style="vertical-align: middle; width: 10%; text-transform: capitalize">
                                                Action
                                            </td>
                                        </tr>
                                    </thead>

                                    <tbody id="paymentRow">
                                        @include('pos::payment-row')

                                        {{-- <tr class="bank_check_info" data-counter="0">
                                            <td style="text-align: center; vertical-align: middle;" class="account_info">
                                                <input type="hidden" name="ck_due_amount[]"
                                                    class="form-control ck_due_amount" readonly value=""
                                                    placeholder="Cheque Amount" autocomplete="off" required>
                                                <input type="hidden" name="ck_bank_name[]"
                                                    class="form-control ck_bank_name" placeholder="Bank Name"
                                                    autocomplete="off" value="">
                                                <input type="hidden" name="ck_number[]" class="form-control ck_number"
                                                    value="" placeholder="Cheque Number" autocomplete="off"
                                                    required>
                                                <input type="hidden" name="ck_issue_date[]"
                                                    class="form-control ck_issue_date" value=""
                                                    placeholder="Issue Date" autocomplete="off" required>
                                                <input type="hidden" name="ck_active_date[]"
                                                    class="form-control ck_active_date" value=""
                                                    placeholder="Active Date" autocomplete="off" required>
                                                <input type="hidden" name="ck_issue_name[]"
                                                    class="form-control ck_issue_name" value=""
                                                    placeholder="Issue to Name" autocomplete="off" required>
                                            </td>
                                        </tr> --}}
                                    </tbody>

                                    <tfoot id="normalPayment">
                                        <tr class="due d-none">
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Due</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="total_due" readonly>
                                            </td>
                                        </tr>
                                        <tr class="due-date d-none">
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Due Date</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="date" class="form-control form-control-sm"
                                                    name="due_date">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Receive Cash</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="number"
                                                    class="form-control form-control-sm receive_cash removeZero"
                                                    name="receive_amount" value="0" step="0.01">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Change</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="text"
                                                    class="form-control form-control-sm change_amount removeZero"
                                                    name="return_amount" value="0" readonly>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Remark</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="text" class="form-control form-control-sm" name="remark"
                                                    value="" autocomplete="off" placeholder="Remark">
                                            </td>
                                        </tr>
                                    </tfoot>

                                    <tfoot id="instalmentPayment" hidden>
                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Due</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="total_due" readonly>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Tenor (in Months)</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="text"
                                                    class="form-control form-control-sm installment_month"
                                                    name="installment_month" autocomplete="off">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Rate of Interest (%)</label>
                                            </td>
                                            <td colspan="3">

                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control form-control-sm interest_rate"
                                                        name="interest_rate" max="100" id="interest_rate"
                                                        autocomplete="off" disabled>
                                                    <div class="input-group-prepend"
                                                        style="padding: 7px;background: #7a888240;">
                                                        <div class="input-group-text">
                                                            <input type="checkbox" id="no_interest" value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="pl-2" style="vertical-align: middle">
                                                <label>Monthly Installment</label>
                                            </td>
                                            <td colspan="3">
                                                <input type="text"
                                                    class="form-control form-control-sm installment_amount"
                                                    name="installment_amount" readonly>
                                            </td>
                                        </tr>

                                    </tfoot>
                                </table>
                            </div>
                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-md-6 mt-4">
                                        <button type="button"
                                            style="background: #f31250;font-size: 20px;font-weight: 600;color: #fff"
                                            class="btn btn-block" onclick="modalHide('#payment-modal')">Cancel <span
                                                style="font-size: 14px;color: #f7e5e5;">[Esc]</span></button>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <button type="submit" id="checkout" class="btn btn-block"
                                            style="background: #00a65a;font-size: 20px;font-weight: 600;color: #fff">
                                            Checkout
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- create payment --}}
    {{-- <div class="modal fade" id="createPayment" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog mw-100 w-75" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create Payment') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="picker3">{{ __('Payment Choice') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="payment_method">
                                        @foreach (allPaymentMethods() as $key => $method)
                                            <option value="{{ $key }}">
                                                {{ $method }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="picker3">{{ __('Payment Details') }} <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control h-80px" placeholder="{{ __('Payment Details') }}" name="payment_details"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="picker3">{{ __('Payment Notes') }}</label>
                                    <textarea class="form-control h-80px" placeholder="{{ __('Payment Notes') }}" name="payment_note"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="picker3">{{ __('Order Notes') }}</label>
                                    <textarea class="form-control h-80px" placeholder="{{ __('Order Notes') }}" name="order_note"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="placeOrderBtn">{{ __('Place Order') }}</button>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('js')
    <script>
        // load products
        (function($) {
            "use strict";
            $(document).ready(function() {
                totalSummery();
                loadProudcts();

                // update pos quantity
                $(document).on("input", ".pos_input_qty", function(e) {
                    let quantity = $(this).val();
                    if (quantity < 1) {
                        return;
                    }
                    $('.preloader_area').removeClass('d-none');
                    let parernt_td = $(this).parents('td');
                    let rowid = parernt_td.data('rowid')

                    $.ajax({
                        type: 'get',
                        data: {
                            rowid,
                            quantity
                        },
                        url: "{{ route('admin.cart-quantity-update') }}",
                        success: function(response) {
                            $(".product-table-container").html(response)
                            totalSummery();
                            $('.preloader_area').addClass('d-none');
                        },
                        error: function(response) {
                            if (response.status == 500) {
                                toastr.error("{{ __('Server error occurred') }}")
                            }

                            if (response.status == 403) {
                                toastr.error("{{ __('Server error occurred') }}")
                            }
                            $('.preloader_area').addClass('d-none');
                        }
                    });

                });

                // load customer address
                $("#customer_id").on("change", function() {
                    let customer_id = $(this).val();
                    $("#order_customer_id").val(customer_id ? customer_id : 'walk-in-customer');
                })

                // add new customer modal

                $("#add-customer-form").on("submit", function(e) {
                    e.preventDefault();
                    const from = $('#add-customer-form')
                    $.ajax({
                        type: 'POST',
                        data: $('#add-customer-form').serialize(),
                        url: $('#add-customer-form').attr('action'),
                        success: function(response) {
                            toastr.success(response.message)
                            $("#addCustomer").modal('hide');
                            $('#add-customer-form')[0].reset();
                            $("#customer_id").html(response.view)
                        },
                        error: function(response) {

                            if (response.status == 500) {
                                toastr.error("{{ __('Server error occurred') }}")
                            }
                            console.log(response);

                        }
                    });
                })

                // product search modal
                $("#product_search_form").on("submit", function(e) {
                    e.preventDefault();

                    $("#search_btn_text").html(`<div class="spinner-border" role="status">
                                            <span class="sr-only">Loading...</span></div>`)

                    $.ajax({
                        type: 'get',
                        data: $('#product_search_form').serialize(),
                        url: "{{ route('admin.load-products') }}",
                        success: function(response) {
                            $("#search_btn_text").html(
                                `<i class="fas fa-search fa-2x fs-25"></i>`)
                            $(".product_body").html(response)
                        },
                        error: function(response) {
                            $("#search_btn_text").html(
                                `<i class="fas fa-search fa-3x fs-25"></i>`)

                            if (response.status == 500) {
                                toastr.error("{{ __('Server error occurred') }}")
                            }

                            if (response.status == 403) {
                                toastr.error(response.responseJSON.message);
                            }

                        }
                    });
                })

                // palce order modal
                // $('#placeOrderBtn').on('click', function() {

                //     const paymentMethod = $('[name="payment_method"]').val();

                //     if (!paymentMethod) {
                //         toastr.error("{{ __('Please select a Payment Method') }}")
                //         return;
                //     }
                //     const paymentDetails = $('[name="payment_details"]').val();
                //     if (!paymentMethod) {
                //         toastr.error("{{ __('Please select a Fill Payment Details') }}")
                //         return;
                //     }
                //     $('[name="order_delivery_date"]').val($('[name="delivery_date"]').val())
                //     $('[name="order_payment_method"]').val($('[name="payment_method"]').val());
                //     $('[name="order_payment_details"]').val($('[name="payment_details"]').val());
                //     $('[name="order_payment_notes"]').val($('[name="_payment_notes"]').val())
                //     $('[name="order_order_note"]').val($('[name="order_note"]').val())

                //     $("#placeOrderForm").submit();
                // })


                $('.modal-reset-button').on('click', function() {
                    const productId = $(this).data('product-id');
                    resetCart();
                    load_product_model(productId);
                })

                $('[name="discount_type"]').on('change', function() {
                    const type = $(this).val();
                    const symbol = type == 'percent' ? '%' : "{{ currency_icon() }}"
                    $('.discount_icon').html(symbol)
                })



                $("#category_id,#brand_id,#name").on('input', function() {
                    const category_id = $('#category_id').val();
                    const brand = $('#brand_id').val();
                    const name = $('#name').val();

                    loadProudcts({
                        category_id,
                        brand,
                        name
                    })
                })


                // extra

                $(".dis-tgl").click(function() {
                    $(".dis-form").slideToggle("fast")
                })


                // add payment row
                $('.add-payment').on('click', function() {
                    const row = `@include('pos::payment-row', ['add' => true])`;
                    $('#paymentRow').append(row)
                })
                $(document).on('click', '.remove-payment', function() {
                    $(this).parents('tr').remove()
                })
            });
        })(jQuery);


        function load_product_model(product_id) {
            $('.preloader_area').removeClass('d-none');
            // check if cart has item from different restaurant using ajax request
            $.ajax({
                type: 'get',
                url: "{{ route('admin.check-cart-restaurant', '') }}" + "/" + product_id,
                success: function(response) {
                    if (response.status == true) {
                        // add product id to reset button of modal
                        $(".modal-reset-button").attr('data-product-id', product_id);
                        $("#resetCartModal").modal('show');
                        $('.preloader_area').addClass('d-none');
                    } else {
                        loadProductModal(product_id)
                    }
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function loadProductModal(product_id) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                url: "{{ url('admin/pos/load-product-modal') }}" + "/" + product_id,
                success: function(response) {
                    $(".load_product_modal_response").html(response)
                    $("#cartModal").modal('show');
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function removeCartItem(rowId) {

            $.ajax({
                type: 'get',
                url: "{{ url('admin/pos/remove-cart-item') }}" + "/" + rowId,
                success: function(response) {
                    $(".product-table-container").html(response)
                    totalSummery();
                    toastr.success("{{ __('Remove successfully') }}")
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                }
            });
        }

        function calculateTotalFee() {

            let subTotal = $('#sub_total').val() || '0.00';

            // remove , if exists
            if (subTotal.includes(',')) {
                subTotal = subTotal.replace(/,/g, '');
            }
            subTotal = parseFloat(subTotal);
            let deliveryFee = parseFloat($('#delivery_fee').val()) || 0;

            let tax = parseFloat($('#tax_fee').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;
            let total = parseFloat($('#total_fee').val()) || 0;

            let discountType = $('[name="discount_type"]').val();

            if (discountType === 'percent') {
                discount = subTotal * (discount / 100);
            }

            // Calculate the total
            total = subTotal + deliveryFee + tax - discount;

            // Update the total field with the calculated value
            $('#total_fee').val(total.toFixed(2));

            $('[name="order_sub_total"]').val(subTotal);
            $('[name="order_delivery_fee"]').val(deliveryFee);
            $('[name="order_tax"]').val(tax);
            $('[name="order_discount"]').val(discount.toFixed(2));
            $('[name="order_total_fee"]').val(total.toFixed(2));
        }



        function loadProudcts(data = null) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                url: "{{ route('admin.load-products') }}",
                data: data,
                success: function(response) {
                    $(".product_body").html(response)
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                    //location.reload();
                }
            });
        }

        function loadPagination(url) {
            $.ajax({
                type: 'get',
                url: url,
                success: function(response) {
                    $(".product_body").html(response)
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                }
            });
        }

        function openPaymentModal() {

            const finalTotal = $('#finalTotal').text().replace(/[^0-9.]/g, '');
            const discountAmount = $('#tds').text();
            const subTotal = $('#total').text().replace(/[^0-9.]/g, '');
            const item = $('#titems').text();


            $('[name="sub_total"]').val(subTotal);
            $('#sub_totalModal').text(subTotal);

            $('#discount_amountModal').text(discountAmount);
            $('[name="discount_amount"]').val(discountAmount);

            let grandTotal = parseFloat(finalTotal);
            $('#total_amountModal').text(grandTotal);
            $('#total_amountModal2').text(grandTotal);

            // total items

            $('#itemModal').text(item);
            $('#payment-modal').modal('show')


            $('.paying_amount').val(grandTotal);

            // hide rows
            if (!discountAmount) {
                $('.discount-row').addClass('d-none');
            } else {
                $('.discount-row').removeClass('d-none');
            }
        }

        function resetCart() {
            $.ajax({
                type: 'get',
                url: "{{ route('admin.modal-cart-clear') }}",
                success: function(response) {
                    $(".shopping-card-body").html(response)
                    calculateTotalFee();
                    toastr.success("{{ __('Cart reset successfully') }}")
                    $("#resetCartModal").modal('hide');
                },
                error: function(response) {
                    toastr.error("{{ __('Server error occurred') }}")
                }
            });
        }

        function singleAddToCart(id) {
            $('.preloader_area').removeClass('d-none');
            $.ajax({
                type: 'get',
                data: {
                    product_id: id,
                    type: 'single'
                },
                url: "{{ url('/admin/pos/add-to-cart') }}",
                success: function(response) {
                    $(".product-table-container").html(response)

                    console.log(response);
                    toastr.success("{{ __('Item added successfully') }}")
                    calculateTotalFee();
                    $('.preloader_area').addClass('d-none');
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}")
                    }

                    if (response.status == 403) {
                        toastr.error(response.responseJSON.message)
                    }
                    $('.preloader_area').addClass('d-none');
                }
            });
        }

        function numberFormat(n) {
            return Number(n).toFixed(2)
        }

        function showDeliveryInfo(show = false) {
            if (show) {
                $('.add_delivery_info').removeClass('d-none');
            } else {
                $('.add_delivery_info').addClass('d-none');
            }
        }

        function discountExist() {
            let discount_total_amount = $('#discount_total_amount').val()
            let discount_type = $('#discount_type').val()
            let total_amount_get_text = Number($('#total').text().replace(/[^0-9.]/g, ''))
            let vat_amount = Number($('#ttax2').text())
            let totalAmount = 0
            let percentage = null

            if (discount_type == 1) {
                if (discount_total_amount > total_amount_get_text) {
                    discount_total_amount = total_amount_get_text
                }
                totalAmount = numberFormat(
                    Number(total_amount_get_text - discount_total_amount).toFixed(6)
                )
            } else {
                if (discount_total_amount > 100) {
                    discount_total_amount = 100
                }
                percentage = (discount_total_amount * total_amount_get_text) / 100
                totalAmount = total_amount_get_text - percentage
            }


            $('#tds').text(percentage ? percentage : discount_total_amount)
            $('input[name=discount_amount]').val(
                percentage ? percentage : discount_total_amount
            )
            $('#discount_amountModal').text(
                percentage ? percentage : discount_total_amount
            )
            vat_amount = 0
            let grand_total = numberFormat(
                // Number(exchange_total)
                Number(totalAmount) + Number(vat_amount) - 0
            )
            $('#ttax2').text(vat_amount)
            $('#gtotal').text(totalAmount)
            $('#finalTotal').text(grand_total)
            $('#discount_total_amount').val(discount_total_amount)
            $('#discountModal').modal('hide')
            $('input[name=total_amount]').val(grand_total)
            $('#total_amountModal').text(grand_total)
            $('input[name=paying_amount]').val(grand_total)
            $('#paing_amountModal').text(grand_total)
            $('#total_amountModal2').text(grand_total)
            totalSummery()
        }

        const accountsList = @json($accounts);

        $(document).on('change', 'select[name="payment_type[]"]', function() {
            const accounts = accountsList.filter(account => account.account_type == $(this).val());

            if (accounts) {
                let html = '<select name="account_id[]" id="" class="form-control">';
                accounts.forEach(account => {
                    switch ($(this).val()) {
                        case 'bank':
                            html +=
                                `<option value="${account.id}">${account.bank_account_number} (${account.bank?.name})</option>`;
                            break;
                        case "mobile_banking":
                            html +=
                                `<option value="${account.id}">${account.mobile_number}(${account.mobile_bank_name})</option>`;
                            break;
                        case 'card':
                            html +=
                                `<option value="${account.id}">${account.card_number} (${account.bank?.name})</option>`;
                            break;
                        default:
                            break;
                    }

                });
                html += '</select>';

                $(this).parents('td').siblings('.account_info').html(html);
            }

            console.log($(this).parents('td').siblings('.account_info'));

            if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                $(this).parents('td').siblings('.account_info').html('');
                $cash =
                    `<input type="hidden" name="account_id[]" class="form-control" value="${$(this).val()}" readonly>`;

                $(this).parents('td').siblings('.account_info').html($cash);
            }
        });

        $('.receive_cash').on('input', function() {
            const cash = $(this).val();
            const total = $('#finalTotal').text();
            let change_amount = 0;

            if (numberOnly(total) < numberOnly(cash)) {
                change_amount = numberOnly(cash) - numberOnly(total);
            }

            if (change_amount < 0 || !change_amount) {
                $('.change_amount').val(0)
            } else {
                $('.change_amount').val(change_amount)
            }
        })


        $(document).on('input', '[name="paying_amount[]"]', function() {
            const amount = [];
            const allAmount = $('[name="paying_amount[]"]').each(function() {
                amount.push($(this).val());
            })

            const amountVal = amount.reduce((a, b) => Number(a) + Number(b), 0);
            console.log(amountVal);
        })
    </script>


    <script>
        function modalHide(id) {
            $(id).modal('hide')
        }

        $(document).on('keydown', function(event) {
            if (event.key === 'Escape' || event.keyCode === 27) {
                modalHide('#payment-modal')
            }
        });

        function paymentSubmit(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                data: formData,
                url: "{{ route('admin.place-order') }}",
                success: function(response) {
                    console.log(response);
                    if (response['alert-type'] == 'success') {

                        toastr.success(response.message)
                        $("#payment-modal").modal('hide');
                        $("#checkoutForm")[0].reset();
                    } else {
                        toastr.error(response.message)
                    }
                },
                error: function(response) {
                    if (response.status == 500) {
                        toastr.error("{{ __('Server error occurred') }}")
                    }
                    console.log(response);
                }
            });
        }


        function totalSummery() {
            const products = $('.product-table tbody > tr > .row_total');

            let total = 0;

            products.each(function() {
                total += numberOnly($(this).text())
            })

            $('#total').text(`{{ currency_icon() }}${total}`)


            // discount
            const discount = $('#discount_total_amount').val();
            const discountType = $('#discount_type').val();
            let discountAmount = 0;

            if (discountType == 2) {
                discountAmount = total * parseFloat(discount) / 100
            } else {
                discountAmount = parseFloat(discount)
            }

            // total after discount = total - discount

            $('#gtotal').text(`{{ currency_icon() }}${total - discountAmount}`)

            // vat

            const vat = $('#ttax2').text();

            let vatAmount = 0;

            if (vat) {
                vatAmount = total * parseFloat(vat) / 100
            }

            $('#totalVat').text(`{{ currency_icon() }}${vatAmount}`)

            // totalAmountWithVat
            const grandTotal = total - discountAmount + vatAmount
            $('#totalAmountWithVat').text(`{{ currency_icon() }}${grandTotal}`)
            $('#finalTotal').text(`{{ currency_icon() }}${grandTotal}`)
        }
    </script>
@endpush
