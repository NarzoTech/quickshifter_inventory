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
                                                @php
                                                    // Return reduces due (goods returned = less owed by customer)
                                                    $returnAmount = $due->sale ? $due->sale->saleReturns->sum('return_amount') : 0;
                                                    $returnPaidBack = $due->sale ? $due->sale->saleReturns->sum(function($r) { return $r->return_amount - $r->return_due; }) : 0;
                                                    $remainingDue = $due->due_amount - $returnAmount + $returnPaidBack;
                                                    if ($remainingDue <= 0) continue;
                                                    $totalDue += $remainingDue;
                                                @endphp
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
                                        <label class="mb-2"><b>{{ __('Payment Methods') }}</b></label>
                                        <div id="payment-rows-container">
                                            @include('customer::due-receive-payment-row')
                                        </div>
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
        const accountsList = @json($accounts);

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
                distributeToInvoices(value);

                // Auto-fill first payment row when only one row
                if ($('.payment-row').length === 1) {
                    $('.paid-amount-input').first().val(value.toFixed(2));
                }
            });

            // Add payment row
            $(document).on('click', '.add-payment-row', function() {
                const newRow = `@include('customer::due-receive-payment-row', ['add' => true])`;
                $('#payment-rows-container').append(newRow);
            });

            // Remove payment row
            $(document).on('click', '.remove-payment-row', function() {
                $(this).closest('.payment-row').remove();
                updateReceivingFromPayments();
            });

            // Update receiving amount when payment amounts change
            $(document).on('input', '.paid-amount-input', function() {
                updateReceivingFromPayments();
            });

            // Payment type change — update account dropdown
            $(document).on('change', '.payment-type-select', function() {
                const selectedType = $(this).val();
                const accountCol = $(this).closest('.payment-row').find('.account-col');

                if (selectedType === 'cash' || selectedType === 'advance') {
                    const displayName = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
                    accountCol.html(`<input type="hidden" name="account_id[]" value="${selectedType}"><input type="text" class="form-control account-display" value="${displayName}" readonly>`);
                    return;
                }

                const filtered = accountsList.filter(a => a.account_type === selectedType);
                let html = '<select name="account_id[]" class="form-control">';
                filtered.forEach(function(account) {
                    switch (selectedType) {
                        case 'bank':
                            html += `<option value="${account.id}">${account.bank_account_number} (${account.bank?.name || ''})</option>`;
                            break;
                        case 'mobile_banking':
                            html += `<option value="${account.id}">${account.mobile_number} (${account.mobile_bank_name})</option>`;
                            break;
                        case 'card':
                            html += `<option value="${account.id}">${account.card_number} (${account.bank?.name || ''})</option>`;
                            break;
                    }
                });
                html += '</select>';
                accountCol.html(html);
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

            // Auto-fill first payment row amount with total
            const firstPaidInput = $('.paid-amount-input').first();
            if ($('.payment-row').length === 1) {
                firstPaidInput.val(total.toFixed(2));
            }
        }

        function updateReceivingFromPayments() {
            let total = 0;
            $('.paid-amount-input').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('input[name="receiving_amount"]').val(total.toFixed(2));
            distributeToInvoices(total);
        }

        function distributeToInvoices(value) {
            // Reset all invoice amounts and checkboxes
            $('input[name="amount[]"]').val('');
            $('#direct_balance_amount').val('');
            $('#checkbox-all').prop('checked', false);
            $('input[name="select"]').prop('checked', false);
            $('#checkbox-direct-balance').prop('checked', false);

            const directBalanceMax = {{ $directBalance ?? 0 }};

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
        }
    </script>
@endpush
