@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Cash Flow') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xl-9 col-lg-6">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> Cash Flow</h4>
            </div>

        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3" id="cash-summary-table">
                    <thead>
                        <tr>
                            <td colspan="2" class="text-center bg-primary">
                                <h5 class="mb-0 text-white">Cash In</h5>
                            </td>
                            <td colspan="2" class="text-center bg-warning">
                                <h5 class="mb-0 text-white">Cash Out</h5>
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/1.png" class="icon-img" />
                                Product Sale
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['productSale']) }}
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/12.png" class="icon-img" />
                                Sale Return
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['sale_return']) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/4.png" class="icon-img" />
                                Balance Deposit
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['balance_deposit']) }}
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/13.png" class="icon-img" />
                                Balance Withdraw
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['balance_withdraw']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/3.png" class="icon-img" />
                                Customer Due
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['customer_due']) }}
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/3.png" class="icon-img" />
                                Customer Due Send
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/15.png" class="icon-img" />
                                Customer Advance
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['customer_advance']) }}
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/6.png" class="icon-img" />
                                Customer Advance Refund
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['customer_advance_refund']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/10.png" class="icon-img" />
                                Supplier Due Receive
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/10.png" class="icon-img" />
                                Supplier Due Pay
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['supplierDuePay']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/15.png" class="icon-img" />
                                Supplier Advance Refund
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['supplierAdvanceRefund']) }}
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/15.png" class="icon-img" />
                                Supplier Advance
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['supplierAdvancePay']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/6.png" class="icon-img" />
                                Purchase Return
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/6.png" class="icon-img" />
                                Purchase
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['purchase']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/7.png" class="icon-img" />
                                Service
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['serviceSale']) }}
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/11.png" class="icon-img" />
                                Expense
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['expenses']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/1.png" class="icon-img" />
                                Installment
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/14.png" class="icon-img" />
                                Salary
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['salary']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/8.png" class="icon-img" />
                                Balance Transfer
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                            <td>
                                <img src="https://amarsolution.com/backend/images/cash-flow-icon/17.png" class="icon-img" />
                                Balance Transfer
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td colspan="" class="text-left">
                                <b>
                                    Total : {{ currency($data['totalReceive']) }}
                                </b>
                            </td>
                            <td></td>
                            <td colspan="" class="text-left">
                                <b>
                                    Total : {{ currency($data['totalPay']) }}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">
                                <h5 class="m-0">
                                    <b>Opening Balance =</b>
                                </h5>
                            </td>
                            <td colspan="" class="text-left">
                                <h5 class="m-0">
                                    <b>{{ $openingBalance }}</b>
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">
                                <h5 class="m-0">
                                    <b>Current Balance =</b>
                                </h5>
                            </td>
                            <td class="text-left">
                                <h5 class="m-0">
                                    <b>{{ $currentBalance }}</b>
                                </h5>
                                (Opening Balance + Cash In - Cash Out)
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
