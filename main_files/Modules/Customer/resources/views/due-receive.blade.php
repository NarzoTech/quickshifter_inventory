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
                        <div class="">{{ __('Customer Due Receive') }}</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>
                                    {{ __('Name') }}: {{ $customer->name }}
                                </h6>
                                <h6>
                                    {{ __('Phone') }}: {{ $customer->phone }}
                                </h6>
                                <h6>
                                    {{ __('Address') }}: {{ $customer->address }}
                                </h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="custom-checkbox custom-control">
                                                    <input type="checkbox" data-checkboxes="checkgroup"
                                                        data-checkbox-role="dad" class="custom-control-input"
                                                        id="checkbox-all">
                                                    <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
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
                                        @php
                                            $totalDue = 0;
                                        @endphp
                                        @foreach ($customer->due as $due)
                                            <tr>
                                                <td>
                                                    <div class="custom-checkbox custom-control">
                                                        <input type="checkbox" data-checkboxes="checkgroup"
                                                            class="custom-control-input" id="checkbox-{{ $due->id }}"
                                                            name="select">
                                                        <label for="checkbox-{{ $due->id }}"
                                                            class="custom-control-label">&nbsp;</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="invoice_no[]"
                                                        value="{{ $due->invoice }}" readonly>
                                                </td>
                                                <td>
                                                    {{ $due->due_date }}
                                                </td>
                                                <td>
                                                    {{ currency($due->sale->grand_total) }}
                                                </td>
                                                <td>
                                                    @php
                                                        $totalDue += $due->due_amount;
                                                    @endphp
                                                    {{ currency($due->due_amount) }}
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="amount[]"
                                                        value="">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        {{-- summery --}}
                        <div class="row">
                            <div class="col-7"></div>
                            <div class="col-5 row">
                                <div class="col-12">
                                    <div class="form-group d-flex">
                                        <div class="col-4">
                                            <label>{{ __('Total Receivable') }}</label>
                                        </div>
                                        <div class="col-8">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon11"><i
                                                        class="fas fa-money-check-alt"></i></span>
                                                <input type="number" class="form-control" placeholder="Username"
                                                    aria-label="Username" aria-describedby="basic-addon11"
                                                    id="total_payable" name="total_payable"
                                                    value="{{ $customer->total_due }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-4">
                                            <label>{{ __('Receiving Amount') }}</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="number" class="form-control" name="receiving_amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-4">
                                            <label>{{ __('Receiving Date') }}</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control datepicker" name="payment_date"
                                                value="{{ now()->format('d-m-Y') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    @include('components.account-type', ['text' => 'Receive'])
                                </div>
                            </div>
                        </div>
                        <div class="card-action d-flex justify-content-end">
                            <button type="submit" class="btn btn-success me-2">{{ __('Submit') }}</button>
                            <a href="{{ route('admin.purchase.index') }}" class="btn btn-danger">{{ __('Cancel') }}</a>
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

            //check all checkboxes
            $('#checkbox-all').on('click', function() {
                var $this = $(this);
                var check = $this.prop('checked');
                $('input[name="select"]').each(function() {
                    $(this).prop('checked', check);

                    // change the count number
                    if (check) {
                        $('.number').text($('input[name="select"]').length);
                        $('.delete-section').removeClass('d-none');
                        $('.delete-section').addClass('d-flex');


                        // get the due amount of selected items
                        let total_due = 0;
                        $('input[name="select"]:checked').each(function() {
                            let due = $(this).closest('tr').find('td:eq(4)').text();
                            // remove icon
                            due = due.replace(/[^0-9.]/g, '');
                            total_due += parseFloat(due);
                        });

                        // set the total due amount to nearest input field
                        $('input[name="amount[]"]').val(total_due);

                    } else {
                        $('.number').text(0);
                        $('.delete-section').addClass('d-none');
                        $('.delete-section').removeClass('d-flex');

                        $('input[name="amount[]"]').val(0);
                    }

                    totalAmount()
                });
            });

            $('input[name="select"]').on('click', function() {
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }
                $('.number').text(number);

                if (number > 0) {
                    $('.delete-section').removeClass('d-none');
                    $('.delete-section').addClass('d-flex');
                } else {
                    $('.delete-section').addClass('d-none');
                    $('.delete-section').removeClass('d-flex');
                }


                // get the due amount of selected items
                let total_due = 0;
                $('input[name="select"]:checked').each(function() {
                    let due = $(this).closest('tr').find('td:eq(4)').text();
                    // remove icon
                    due = due.replace(/[^0-9.]/g, '');
                    total_due += parseFloat(due);
                });
                $('input[name="amount[]"]').val(total_due);

                if (number == 0) {
                    $('input[name="receiving_amount"]').val(0);
                }

                totalAmount()
            });

            $('[name="amount[]"]').on('input', function() {
                const value = $(this).val();

                if (value > 0) {
                    $(this).closest('tr').find('input[name="select"]').prop('checked', true);
                } else {
                    $(this).closest('tr').find('input[name="select"]').prop('checked', false);
                }

                // check checkbox-all if all are checked
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }

                totalAmount()
            })

            $('input[name="receiving_amount"]').on('input', function() {
                let value = parseFloat($(this).val());


                // reset all the amount
                $('input[name="amount[]"]').val(0);

                // uncheck checkbox-all
                $('#checkbox-all').prop('checked', false);

                // uncheck all the checkbox
                $('input[name="select"]').prop('checked', false);
                $('.number').text(0);
                $('.delete-section').addClass('d-none');
                $('.delete-section').removeClass('d-flex');


                // get all the row
                $('input[name="amount[]"]').each(function() {
                    // due amount the previous sibling
                    let due = $(this).closest('tr').find('td:eq(4)').text();
                    // remove icon
                    due = parseFloat(due.replace(/[^0-9.]/g, ''));

                    // calculate the due amount
                    if (value <= due) {
                        if (value > 0) {
                            $(this).val(value);
                            $(this).closest('tr').find('input[name="select"]').prop('checked',
                                true);
                            value = value - due;
                        }

                    } else {
                        if (due > 0) {
                            $(this).val(due);
                            value = value - due;
                            $(this).closest('tr').find('input[name="select"]').prop('checked',
                                true);
                        }
                    }
                });

                // check checkbox-all if all are checked
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }

            })
        });


        function totalAmount() {
            let total = 0;
            $('input[name="amount[]"]').each(function() {
                total += parseFloat($(this).val());
            });
            $('input[name="receiving_amount"]').val(total);
        }
    </script>
@endpush
