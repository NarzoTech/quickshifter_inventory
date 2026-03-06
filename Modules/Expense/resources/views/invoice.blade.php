@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Expense Invoice') }}</title>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
@endpush

@section('content')
    <div class="main-content">
        <section class="page">
            {{-- Header --}}
            <div class="row justify-content-between">
                <div class="col-5">
                    <p class="title">{{ ucfirst($setting->app_name) }}</p>
                    <div class="property">
                        <span class="value">{{ $setting->address }}</span>
                    </div>
                    <div class="property">
                        <span class="key">Mobile:</span>
                        <span class="value">{{ $setting->mobile }}</span>
                    </div>
                    <div class="property">
                        <span class="key">Email:</span>
                        <span class="value">{{ $setting->email }}</span>
                    </div>
                </div>
                <div class="col-5">
                    <p class="title">Expense Invoice</p>
                    <div class="property">
                        <span class="key">Invoice No:</span>
                        <span class="value">{{ $expense->invoice }}</span>
                    </div>
                    <div class="property">
                        <span class="key">Date:</span>
                        <span class="value">{{ formatDate($expense->date) }}</span>
                    </div>
                    @if($expense->memo)
                        <div class="property">
                            <span class="key">Memo:</span>
                            <span class="value">{{ $expense->memo }}</span>
                        </div>
                    @endif

                    @if($expense->expenseSupplier->id)
                        <p class="subtitle">Supplier</p>
                        <div class="property">
                            <span class="key">Name:</span>
                            <span class="value">{{ $expense->expenseSupplier->name }}</span>
                        </div>
                        @if($expense->expenseSupplier->phone)
                            <div class="property">
                                <span class="key">Mobile:</span>
                                <span class="value">{{ $expense->expenseSupplier->phone }}</span>
                            </div>
                        @endif
                        @if($expense->expenseSupplier->email)
                            <div class="property">
                                <span class="key">Email:</span>
                                <span class="value">{{ $expense->expenseSupplier->email }}</span>
                            </div>
                        @endif
                        @if($expense->expenseSupplier->address)
                            <div class="property">
                                <span class="key">Address:</span>
                                <span class="value">{{ $expense->expenseSupplier->address }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Details Table --}}
            <div class="mt-5">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 5%;"><b>SL</b></th>
                            <th style="width: 30%;"><b>Description</b></th>
                            <th style="width: 25%;"><b>Details</b></th>
                            <th style="width: 20%; text-align: right;"><b>Amount</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                {{ $expense->expenseType->name ?? '-' }}
                                @if($expense->subExpenseType->name)
                                    <br><small style="color: #666;">{{ $expense->subExpenseType->name }}</small>
                                @endif
                            </td>
                            <td>
                                @if($expense->expenseSupplier->id)
                                    Supplier: {{ $expense->expenseSupplier->name }}
                                @else
                                    Direct Expense
                                @endif
                            </td>
                            <td style="text-align: right;">TK {{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Summary --}}
                @php
                    $advanceUsed = $expense->payments->where('payment_type', 'advance_deduct')->sum('amount');
                    $cashPaid = $expense->paid_amount - $advanceUsed;
                @endphp
                <table class="summary-table invoice-summary-table">
                    <tbody>
                        <tr>
                            <td><b>Total:</b></td>
                            <td><b>TK {{ number_format($expense->amount, 2) }}</b></td>
                        </tr>
                        <tr>
                            <td>Paid:</td>
                            <td>TK {{ number_format($cashPaid, 2) }}</td>
                        </tr>
                        @if($advanceUsed > 0)
                            <tr>
                                <td>Paid from Advance:</td>
                                <td>TK {{ number_format($advanceUsed, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Due:</td>
                            <td>TK {{ number_format($expense->due_amount, 2) }}</td>
                        </tr>
                        @if($expense->payments->where('payment_type', '!=', 'advance_deduct')->count())
                            <tr>
                                <td colspan="2" style="padding-top: 8px;">
                                    <b>Payment Methods:</b>
                                    <ul style="margin: 4px 0 0 16px; padding: 0; list-style: none;">
                                        @foreach ($expense->payments->where('payment_type', '!=', 'advance_deduct') as $payment)
                                            <li>{{ ucfirst($payment->account->account_type ?? '-') }} - TK {{ number_format($payment->amount, 2) }}</li>
                                        @endforeach
                                        @if($advanceUsed > 0)
                                            <li>Advance - TK {{ number_format($advanceUsed, 2) }}</li>
                                        @endif
                                    </ul>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @if($expense->paid_amount > 0)
                    <div class="mt-3 payment-details">
                        <span style="font-weight: bold; font-size: 13px;">
                            In Words: {{ numberToWord($expense->paid_amount) }} TK Only
                        </span>
                    </div>
                @endif

                @if($expense->note)
                    <div class="mt-3" style="clear: both;">
                        <b>Note:</b> {{ $expense->note }}
                    </div>
                @endif

                <div class="d-flex justify-content-between" style="margin-top: 250px; clear: both;">
                    <div>
                        <p class="signature">Prepared By</p>
                    </div>
                    <div>
                        <p class="signature">Authorised By</p>
                    </div>
                </div>
            </div>

            <div class="print-btn pos-share-btns d-print-none">
                <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light">
                    <i class="fa fa-print me-2"></i> Print
                </a>
            </div>
        </section>
    </div>
@endsection
