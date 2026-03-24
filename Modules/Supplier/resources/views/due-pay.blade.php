@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Due Pay') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.suppliers.due-pay-store', $supplier->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-header-title">
                                        <h4 class="section_title">{{ __('Supplier Due Pay') }}</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-2">
                                                {{ __('Name') }}: {{ $supplier->name }}
                                            </h6>
                                            <h6 class="mb-2">
                                                {{ __('Phone') }}: {{ $supplier->phone }}
                                            </h6>
                                            <h6 class="mb-2">
                                                {{ __('Address') }}: {{ $supplier->address }}
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <div class="custom-checkbox custom-control">
                                                                    <input type="checkbox" data-checkboxes="checkgroup"
                                                                        data-checkbox-role="dad"
                                                                        class="custom-control-input" id="checkbox-all">
                                                                    <label for="checkbox-all"
                                                                        class="custom-control-label">&nbsp;</label>
                                                                </div>
                                                            </th>
                                                            <th>{{ __('Invoice No') }}</th>
                                                            <th>{{ __('Purchase Date') }}</th>
                                                            <th>{{ __('Invoice Amount') }}</th>
                                                            <th>{{ __('Due Amount') }}</th>
                                                            <th>{{ __('Paying Amount') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="purchase_table">
                                                        @foreach ($supplier->duePurchase as $purchase)
                                                            @php
                                                                // Return reduces due (goods returned = less owed)
                                                                $returnAmount = $purchase->purchaseReturn->sum('return_amount');
                                                                $returnReceived = $purchase->purchaseReturn->sum('received_amount');
                                                                $effectiveDue = $purchase->due_amount - $returnAmount + $returnReceived;
                                                                if ($effectiveDue <= 0) continue;
                                                            @endphp
                                                            <tr data-due="{{ $effectiveDue }}">
                                                                <td>
                                                                    <div class="custom-checkbox custom-control">
                                                                        <input type="checkbox" data-checkboxes="checkgroup"
                                                                            class="custom-control-input"
                                                                            id="checkbox-{{ $purchase->id }}"
                                                                            name="select">
                                                                        <label for="checkbox-{{ $purchase->id }}"
                                                                            class="custom-control-label">&nbsp;</label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="hidden" name="purchase_id[]"
                                                                        value="{{ $purchase->id }}">
                                                                    <input type="text" class="form-control"
                                                                        name="invoice_no[]"
                                                                        value="{{ $purchase->invoice_number }}" readonly>
                                                                </td>
                                                                <td>
                                                                    {{ formatDate($purchase->purchase_date) }}
                                                                </td>
                                                                <td>
                                                                    {{ currency($purchase->total_amount) }}
                                                                </td>
                                                                <td>
                                                                    {{ currency($effectiveDue) }}
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="amount[]" value="0"
                                                                        min="0" max="{{ $effectiveDue }}" step="0.01">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5 justify-content-end">
                                        <div class="col-md-5">
                                            {{-- summery --}}
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Total Payable') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <i class="fas fa-money-check-alt"></i>
                                                            </div>
                                                            @php
                                                                $totalPayable = 0;
                                                                foreach ($supplier->duePurchase as $p) {
                                                                    $rAmt = $p->purchaseReturn->sum('return_amount');
                                                                    $rRcv = $p->purchaseReturn->sum('received_amount');
                                                                    $eDue = $p->due_amount - $rAmt + $rRcv;
                                                                    if ($eDue > 0) $totalPayable += $eDue;
                                                                }
                                                            @endphp
                                                            <input type="number" class="form-control" name="total_payable"
                                                                value="{{ $totalPayable }}"
                                                                readonly step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Paying Amount') }}</label>
                                                        <input type="number" class="form-control" name="paying_amount" step="0.01">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Paying Date') }}</label>
                                                        <input type="text" class="form-control datepicker"
                                                            name="payment_date" value="{{ formatDate(now()) }}"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    @include('components.account-type')
                                                </div>
                                            </div>

                                            <div class="card-action d-flex justify-content-end">
                                                <a href="{{ route('admin.purchase.index') }}"
                                                    class="btn btn-danger me-2">{{ __('Cancel') }}</a>
                                                <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            'use strict';

            /**
             * Get the due amount for a row from the data-due attribute (reliable, not DOM text).
             */
            function getRowDue(row) {
                return parseFloat($(row).data('due')) || 0;
            }

            /**
             * Enforce max limit: amount cannot exceed the row's due.
             */
            function clampAmount(input) {
                let val = parseFloat($(input).val()) || 0;
                let max = getRowDue($(input).closest('tr'));
                if (val < 0) val = 0;
                if (val > max) val = max;
                $(input).val(val);
            }

            /**
             * Update checkbox-all state based on individual checkboxes.
             */
            function updateCheckAllState() {
                var total = $('input[name="select"]').length;
                var checked = $('input[name="select"]:checked').length;
                $('#checkbox-all').prop('checked', total > 0 && total === checked);
            }

            /**
             * Recalculate total paying amount from individual amounts.
             */
            function totalAmount() {
                let total = 0;
                $('input[name="amount[]"]').each(function() {
                    let val = parseFloat($(this).val()) || 0;
                    if (val > 0) total += val;
                });
                $('input[name="paying_amount"]').val(Math.round(total * 100) / 100);
            }

            // Check-all: set each row's amount to its OWN due (not the total)
            $('#checkbox-all').on('click', function() {
                var check = $(this).prop('checked');

                $('input[name="select"]').each(function() {
                    $(this).prop('checked', check);
                    let row = $(this).closest('tr');
                    let amountInput = row.find('input[name="amount[]"]');

                    if (check) {
                        amountInput.val(getRowDue(row));
                    } else {
                        amountInput.val(0);
                    }
                });

                totalAmount();
            });

            // Individual checkbox: set amount to due when checked, 0 when unchecked
            $('input[name="select"]').on('click', function() {
                let row = $(this).closest('tr');
                let amountInput = row.find('input[name="amount[]"]');

                if ($(this).prop('checked')) {
                    amountInput.val(getRowDue(row));
                } else {
                    amountInput.val(0);
                }

                updateCheckAllState();
                totalAmount();
            });

            // Manual amount entry: clamp to max, sync checkbox state
            $('[name="amount[]"]').on('input', function() {
                clampAmount(this);

                let val = parseFloat($(this).val()) || 0;
                $(this).closest('tr').find('input[name="select"]').prop('checked', val > 0);

                updateCheckAllState();
                totalAmount();
            });

            // Paying amount: distribute across invoices (oldest first)
            $('input[name="paying_amount"]').on('input', function() {
                let remaining = parseFloat($(this).val()) || 0;
                if (remaining < 0) remaining = 0;

                // Cap at total payable
                let totalPayable = parseFloat($('input[name="total_payable"]').val()) || 0;
                if (remaining > totalPayable) {
                    remaining = totalPayable;
                    $(this).val(remaining);
                }

                // Reset all amounts and checkboxes
                $('input[name="amount[]"]').val(0);
                $('input[name="select"]').prop('checked', false);

                // Distribute across rows
                $('input[name="amount[]"]').each(function() {
                    if (remaining <= 0) return;

                    let due = getRowDue($(this).closest('tr'));
                    if (due <= 0) return;

                    let allocate = Math.min(remaining, due);
                    // Round to 2 decimals to avoid floating point drift
                    allocate = Math.round(allocate * 100) / 100;

                    $(this).val(allocate);
                    $(this).closest('tr').find('input[name="select"]').prop('checked', true);
                    remaining = Math.round((remaining - allocate) * 100) / 100;
                });

                updateCheckAllState();
            });
        });
    </script>
@endpush
