@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Customer Due Receive') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <form method="POST" action="{{ route('admin.customer.due-receive.store') }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <div class="card">
                    <div class="card-header">
                        <h4 class="section_title">{{ __('Customer Due Receive') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-2">
                                    {{ __('Name') }}: {{ $customer->name }}
                                </h6>
                                <h6 class="mb-2">
                                    {{ __('Phone') }}: {{ $customer->phone }}
                                </h6>
                                <h6 class="mb-2">
                                    {{ __('Address') }}: {{ $customer->address }}
                                </h6>
                            </div>
                        </div>
                        @php
                            $totalDue = 0;
                            $directBalance = $customer->wallet_balance ?? 0;
                        @endphp

                        {{-- Direct Balance Due Section --}}
                        @if($hasDirectBalance ?? false)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-2"><i class="fas fa-wallet"></i> {{ __('Direct Balance Due') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>
                                                    <div class="custom-checkbox custom-control">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="checkbox-direct" name="select_direct">
                                                        <label for="checkbox-direct"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </th>
                                                <th>{{ __('Description') }}</th>
                                                <th>{{ __('Due Amount') }}</th>
                                                <th>{{ __('Receiving Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="custom-checkbox custom-control">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="checkbox-direct-balance" name="select_direct_balance">
                                                        <label for="checkbox-direct-balance"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ __('Opening/Direct Balance') }}</span>
                                                </td>
                                                <td class="direct-due-amount">
                                                    {{ currency($directBalance) }}
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="direct_balance_amount"
                                                        id="direct_balance_amount" value="" step="0.01" min="0" max="{{ $directBalance }}">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Invoice-Based Due Section --}}
                        @if($hasInvoiceDues ?? false)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-2"><i class="fas fa-file-invoice"></i> {{ __('Invoice Due') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>
                                                    <div class="custom-checkbox custom-control">
                                                        <input type="checkbox" data-checkboxes="checkgroup"
                                                            data-checkbox-role="dad" class="custom-control-input"
                                                            id="checkbox-all">
                                                        <label for="checkbox-all"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </th>
                                                <th>{{ __('Invoice No') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Invoice Amount') }}</th>
                                                <th>{{ __('Due Amount') }}</th>
                                                <th>{{ __('Receiving Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="purchase_table">
                                            @foreach ($customer->due as $due)
                                                <tr>
                                                    <td>
                                                        <div class="custom-checkbox custom-control">
                                                            <input type="checkbox" data-checkboxes="checkgroup"
                                                                class="custom-control-input"
                                                                id="checkbox-{{ $due->id }}" name="select">
                                                            <label for="checkbox-{{ $due->id }}"
                                                                class="custom-control-label">&nbsp;</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="sale_id[]"
                                                            value="{{ $due->sale->id ?? '' }}">
                                                        <input type="text" class="form-control" name="invoice_no[]"
                                                            value="{{ $due->invoice }}" readonly>
                                                    </td>
                                                    <td>
                                                        {{ formatDate($due->due_date ?? $due->sale?->order_date) }}
                                                    </td>
                                                    <td>
                                                        {{ currency($due->sale->grand_total ?? 0) }}
                                                    </td>
                                                    @php
                                                        $remainingDue = $due->due_amount + $due->paid_amount;
                                                        $totalDue += $remainingDue;
                                                    @endphp
                                                    <td>
                                                        {{ currency($remainingDue) }}
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="amount[]"
                                                            value="" step="0.01" min="0" max="{{ $remainingDue }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- No dues message --}}
                        @if(!($hasInvoiceDues ?? false) && !($hasDirectBalance ?? false))
                        <div class="alert alert-warning mt-3">
                            {{ __('This customer has no due amount to receive.') }}
                        </div>
                        @endif
                        {{-- summery --}}
                        <div class="row mt-5 justify-content-end">
                            <div class="col-lg-5">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Total Receivable') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-text" id="basic-addon11"><i
                                                        class="fas fa-money-check-alt"></i></div>
                                                <input type="number" class="form-control" placeholder="Total"
                                                    aria-label="Total" aria-describedby="basic-addon11"
                                                    id="total_payable" name="total_payable"
                                                    value="{{ $totalDue + $directBalance }}" step="0.01" readonly/>
                                            </div>
                                            <small class="text-muted">
                                                {{ __('Invoice Due') }}: {{ currency($totalDue) }} |
                                                {{ __('Direct Balance') }}: {{ currency($directBalance) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Receiving Amount') }}</label>
                                            <input type="number" class="form-control" name="receiving_amount" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('Receiving Date') }}</label>
                                            <input type="text" class="form-control datepicker" name="payment_date"
                                                value="{{ formatDate(now()) }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @include('components.account-type', ['text' => 'Receive'])
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-action d-flex justify-content-end">
                            <a href="{{ route('admin.customers.index') }}"
                                class="btn btn-danger me-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-success ">{{ __('Submit') }}</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            'use strict';

            const directBalanceMax = {{ $directBalance ?? 0 }};

            // Direct balance checkbox
            $('#checkbox-direct-balance').on('click', function() {
                const isChecked = $(this).prop('checked');
                if (isChecked) {
                    $('#direct_balance_amount').val(directBalanceMax);
                } else {
                    $('#direct_balance_amount').val('');
                }
                totalAmount();
            });

            // Direct balance amount input
            $('#direct_balance_amount').on('input', function() {
                let value = parseFloat($(this).val()) || 0;

                // Cap at max
                if (value > directBalanceMax) {
                    value = directBalanceMax;
                    $(this).val(value);
                }

                // Update checkbox
                if (value > 0) {
                    $('#checkbox-direct-balance').prop('checked', true);
                } else {
                    $('#checkbox-direct-balance').prop('checked', false);
                }

                totalAmount();
            });

            //check all checkboxes for invoice dues
            $('#checkbox-all').on('click', function() {
                var $this = $(this);
                var check = $this.prop('checked');
                $('input[name="select"]').each(function() {
                    $(this).prop('checked', check);

                    if (check) {
                        // get the due amount of each row and set it
                        let due = $(this).closest('tr').find('td:eq(4)').text();
                        due = parseFloat(due.replace(/[^0-9.]/g, '')) || 0;
                        $(this).closest('tr').find('input[name="amount[]"]').val(due);
                    } else {
                        $(this).closest('tr').find('input[name="amount[]"]').val('');
                    }
                });

                totalAmount();
            });

            $('input[name="select"]').on('click', function() {
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;

                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }

                // Set amount for this row
                const isChecked = $(this).prop('checked');
                if (isChecked) {
                    let due = $(this).closest('tr').find('td:eq(4)').text();
                    due = parseFloat(due.replace(/[^0-9.]/g, '')) || 0;
                    $(this).closest('tr').find('input[name="amount[]"]').val(due);
                } else {
                    $(this).closest('tr').find('input[name="amount[]"]').val('');
                }

                totalAmount();
            });

            $('[name="amount[]"]').on('input', function() {
                const value = parseFloat($(this).val()) || 0;

                if (value > 0) {
                    $(this).closest('tr').find('input[name="select"]').prop('checked', true);
                } else {
                    $(this).closest('tr').find('input[name="select"]').prop('checked', false);
                }

                // check checkbox-all if all are checked
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number && total > 0) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }

                totalAmount();
            });

            $('input[name="receiving_amount"]').on('input', function() {
                let value = parseFloat($(this).val()) || 0;

                // Reset all amounts
                $('input[name="amount[]"]').val('');
                $('#direct_balance_amount').val('');
                $('#checkbox-all').prop('checked', false);
                $('input[name="select"]').prop('checked', false);
                $('#checkbox-direct-balance').prop('checked', false);

                // First, allocate to direct balance if exists
                if (directBalanceMax > 0 && value > 0) {
                    const directAmount = Math.min(value, directBalanceMax);
                    $('#direct_balance_amount').val(directAmount);
                    $('#checkbox-direct-balance').prop('checked', true);
                    value -= directAmount;
                }

                // Then allocate to invoices
                $('input[name="amount[]"]').each(function() {
                    if (value <= 0) return;

                    let due = $(this).closest('tr').find('td:eq(4)').text();
                    due = parseFloat(due.replace(/[^0-9.]/g, '')) || 0;

                    if (due > 0) {
                        const allocate = Math.min(value, due);
                        $(this).val(allocate);
                        $(this).closest('tr').find('input[name="select"]').prop('checked', true);
                        value -= allocate;
                    }
                });

                // Update checkbox-all state
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number && total > 0) {
                    $('#checkbox-all').prop('checked', true);
                }
            });
        });


        function totalAmount() {
            let total = 0;

            // Sum invoice amounts
            $('input[name="amount[]"]').each(function() {
                const val = parseFloat($(this).val()) || 0;
                total += val;
            });

            // Add direct balance amount
            const directVal = parseFloat($('#direct_balance_amount').val()) || 0;
            total += directVal;

            $('input[name="receiving_amount"]').val(total.toFixed(2));
        }
    </script>
@endpush
