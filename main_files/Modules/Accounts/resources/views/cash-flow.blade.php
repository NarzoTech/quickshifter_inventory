@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Cash Flow') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-1">
                    <form class="search_form" action="" method="GET">
                        <div class="row">

                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="From Date" name="from_date"
                                        value="{{ request()->get('from_date') }}" class="form-control datepicker"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="To Date" name="to_date"
                                        value="{{ request()->get('to_date') }}" class="form-control datepicker"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-1 col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn bg-label-danger reset-form"><i
                                            class='bx bx-rotate-right'></i></button>

                                    <button type="submit" class="btn bg-label-primary"><i
                                            class='bx bx-search'></i></button>
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
                <h4 class="section_title"><i class="fas fa-list"></i> Cash Flow</h4>
            </div>

        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3" id="cash-summary-table">
                    <thead>
                        <tr>
                            <td colspan="2" class="text-center">
                                <h5>Cash In</h5>
                            </td>
                            <td colspan="2" class="text-center">
                                <h5>Cash Out</h5>
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
                            <td class="border-none"></td>
                            <td colspan="" class="text-left">
                                <h4 class="m-0">
                                    Opening Balance =
                                </h4>
                            </td>
                            <td colspan="" class="text-left border-none">
                                <h4 class="m-0">
                                    {{ $openingBalance }}
                                </h4>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="border-none"></td>
                            <td class="text-left">
                                <h4 class="m-0">
                                    Current Balance =
                                </h4>
                            </td>
                            <td class="text-left border-none">
                                <h4 class="m-0">
                                    {{ $currentBalance }}
                                </h4>
                                (Opening Balance + Cash In - Cash Out)
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
