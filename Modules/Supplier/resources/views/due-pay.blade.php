@extends('admin.master_layout')
@section('title')
    <title>{{ __('Supplier Due Pay') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Supplier Due Pay') }}</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.purchase.index') }}">{{ __('Supplier Due Pay') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Supplier Due Pay') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.purchase.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <div class="">{{ __('Supplier Due Pay') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>
                                                {{ __('Name') }}: {{ $supplier->name }}
                                            </h6>
                                            <h6>
                                                {{ __('Phone') }}: {{ $supplier->phone }}
                                            </h6>
                                            <h6>
                                                {{ __('Address') }}: {{ $supplier->address }}
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
                                                        <tr>
                                                            <td>
                                                                <div class="custom-checkbox custom-control">
                                                                    <input type="checkbox" data-checkboxes="checkgroup"
                                                                        class="custom-control-input"
                                                                        id="checkbox-{{ $purchase->id }}" name="select">
                                                                    <label for="checkbox-{{ $purchase->id }}"
                                                                        class="custom-control-label">&nbsp;</label>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="invoice_no[]"
                                                                    value="{{ $purchase->invoice_number }}" readonly>
                                                            </td>
                                                            <td>
                                                                {{ $purchase->purchase_date }}
                                                            </td>
                                                            <td>
                                                                {{ currency($purchase->total_amount) }}
                                                            </td>
                                                            <td>
                                                                {{ currency($purchase->due_amount) }}
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="paying_amount[]"
                                                                    value="{{ $purchase->due_amount }}">
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
                                                        <label>{{ __('Item Count') }}</label>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="number" class="form-control" name="items"
                                                            value="0" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group d-flex">
                                                    <div class="col-4">
                                                        <label>{{ __('Total Amount') }}</label>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="total_amount" class="form-control" name="total_amount"
                                                            value="0" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-4">
                                                        <label>{{ __('Payment Type') }}</label>
                                                    </div>
                                                    <div class="col-8">
                                                        <select name="payment_type" id="" class="form-control">
                                                            <option value="">{{ __('Select Payment Type') }}
                                                            </option>
                                                            @foreach (accountList() as $key => $list)
                                                                <option value="{{ $key }}"
                                                                    @if ($key == 'cash') selected @endif
                                                                    data-name="{{ $list }}">{{ $list }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control" name="payment_method"
                                                            value="cash" readonly>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="paid_amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-4">
                                                        <label>{{ __('Due') }}</label>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="due_amount"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-action d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success mr-2">{{ __('Submit') }}</button>
                                        <a href="{{ route('admin.purchase.index') }}"
                                            class="btn btn-danger">{{ __('Cancel') }}</a>
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

                    } else {
                        $('.number').text(0);
                        $('.delete-section').addClass('d-none');
                        $('.delete-section').removeClass('d-flex');
                    }
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
            });

        });
    </script>
@endpush
