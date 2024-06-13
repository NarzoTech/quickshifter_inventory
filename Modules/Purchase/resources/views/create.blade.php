@extends('admin.master_layout')
@section('title')
    <title>{{ __('Purchase List') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Create Purchase') }}</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.purchase.index') }}">{{ __('Purchase List') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Add Purchase') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.purchase.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __('Create Purchase') }}</div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Supplier') }}</label>
                                                <select class="form-control select2" name="supplier_id">
                                                    <option value="">{{ __('Select Supplier') }}</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('supplier_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Invoice Number') }}</label>
                                                <input type="text" class="form-control datepicker" name="invoice_number"
                                                    value="{{ old('invoice_number', $invoiceNumber) }}">
                                                @error('invoice_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Purchase Date') }}</label>
                                                <input type="text" class="form-control datepicker" name="purchase_date"
                                                    value="{{ old('purchase_date',now()->format('d-m-Y')) }}">
                                                @error('purchase_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Reference No') }}</label>
                                                <input type="text" class="form-control" name="reference_no"
                                                    value="{{ old('reference_no') }}">
                                                @error('reference_no')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Attachment') }}</label>
                                                <input type="file" class="form-control" name="attachment"
                                                    value="{{ old('attachment') }}">
                                                @error('attachment')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Purchase Status') }}</label>
                                                <select class="form-control" name="status">
                                                    <option value="">{{ __('Select Status') }}</option>
                                                    <option value="1" selected>{{ __('Pending') }}</option>
                                                    <option value="2">{{ __('Received') }}</option>
                                                </select>
                                                @error('status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- product search box --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('Product') }}</label>
                                                <select class="form-control select2" id="product_id">
                                                    <option value="">{{ __('Select Product') }}</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}
                                                            ({{ $product->sku }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Product Name') }}</th>
                                                        <th>{{ __('Product Stock') }}</th>
                                                        <th>{{ __('Quantity') }}</th>
                                                        <th>{{ __('Purchase Price') }}</th>
                                                        <th>{{ __('Sub Total') }}</th>
                                                        <th>{{ __('Profit') }}%</th>
                                                        <th>{{ __('Selling Price') }}</th>
                                                        <th>
                                                            <i class="fas fa-trash text-danger"></i>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="purchase_table">
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
                                                                <option value="{{ $key }}" @if ($key == 'cash')
                                                                    selected
                                                                @endif data-name="{{ $list }}">{{ $list }}
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
        'use strict';

        function addPurchaseRow(product) {
            // calculation profit per product on product cost and product price
            let profit = ((parseFloat(product.price) - parseFloat(product.cost)) / parseFloat(product.cost)) * 100;
            let tr = `
                <tr>
                    <td>
                        <input type="text" class="form-control" name="product_name[]" value="${product.name}" readonly>
                        <input type="hidden" name="product_id[]" value="${product.id}">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="stock[]" value="${product.stock}" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="quantity[]" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="unit_price[]" value="${product.cost}" min="0">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="total[]" value="${product.cost}" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="profit[]" value="${profit.toFixed(2)}">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="selling_price[]" value="${product.price}" min="0">
                    </td>
                    <td>
                        <button type="button" class="btn btn-white" onclick="removePurchaseRow(this)"><i class="fas fa-trash text-danger"></i></button>
                    </td>
                </tr>
            `;
            $('#purchase_table').append(tr);
            calculateTotalAmount();
        }

        function removePurchaseRow(row) {
            $(row).closest('tr').remove();
            calculateTotalAmount();
        }

        $(document).on('change', '#product_id', function() {
            let product_id = $(this).val();
            const products = @json($products);
            const product = products.find(p => p.id == product_id);
            addPurchaseRow(product);
        });

        $(document).on('input', 'input[name="quantity[]"], input[name="unit_price[]"]', function() {
            var tr = $(this).closest('tr');
            var quantity = tr.find('input[name="quantity[]"]').val();
            var unit_price = tr.find('input[name="unit_price[]"]').val();
            var total = quantity * unit_price;
            tr.find('input[name="total[]"]').val(total);



            calculateTotalAmount();

        });

        $(document).on('change', '[name="selling_price[]"]', function() {
            var tr = $(this).closest('tr');
            var selling_price = tr.find('input[name="selling_price[]"]').val();
            var unit_price = tr.find('input[name="unit_price[]"]').val();
            var profit = ((parseFloat(selling_price) - parseFloat(unit_price)) / parseFloat(unit_price)) * 100;

            // if unit price is 0 and selling price is greater than 0 then profit will 100
            if (unit_price == 0 && selling_price > 0) {
                profit = 100;
            }
            if (unit_price == 0 && selling_price == 0) {
                profit = 0;
            }
            profit = profit.toFixed(2)
            tr.find('input[name="profit[]"]').val(profit);
        });

        $(document).on('input', "[name='unit_price[]']", function() {
            var tr = $(this).closest('tr');
            var unit_price = tr.find('input[name="unit_price[]"]').val();
            var profit = tr.find('input[name="profit[]"]').val();

            if (unit_price != 0) {
                var selling_price = parseFloat(unit_price) + (parseFloat(unit_price) * parseFloat(profit) / 100);
                tr.find('input[name="selling_price[]"]').val(selling_price);
            }

            calculateTotalAmount();
        })

        $(document).on('change', '[name="payment_type"]', function() {
            let payment_type = $(this).val();
            if (payment_type != '') {
                $('[name="payment_method"]').val(payment_type);
            }
        })
        $(document).on('input', '[name="paid_amount"]', function() {
            let total_amount = parseFloat($('[name="total_amount"]').val());
            let paid_amount = parseFloat($(this).val());
            let due_amount = total_amount - paid_amount;
            $('[name="due_amount"]').val(due_amount);
        })

        $(document).on('change','[name="payment_type"]',function(){
            let payment_type = $(this).data('name');
            $('[name="payment_method"]').val(payment_type);
        })


        //

        function calculateTotalAmount() {

            let totalQuantity = 0;
            $('input[name="quantity[]"]').each(function() {
                totalQuantity += parseFloat($(this).val());
            });
            $('[name="items"]').val(totalQuantity);

            let totalAmount = 0;
            $('input[name="total[]"]').each(function() {
                totalAmount += parseFloat($(this).val());
            });
            $('[name="total_amount"]').val(totalAmount);
        }


        // calculate profit % per row on purchase price and selling price changes
        $(document).on('input', 'input[name="unit_price[]"], input[name="selling_price[]"]', function() {
            var tr = $(this).closest('tr');
            var unit_price = tr.find('input[name="unit_price[]"]').val();
            var selling_price = tr.find('input[name="selling_price[]"]').val();
            var profit = ((parseFloat(selling_price) - parseFloat(unit_price)) / parseFloat(unit_price)) * 100;

            // if unit price is 0 and selling price is greater than 0 then profit will 100
            if (unit_price == 0 && selling_price > 0) {
                profit = 100;
            }
            if (unit_price == 0 && selling_price == 0) {
                profit = 0;
            }
            profit = profit.toFixed(2)
            tr.find('input[name="profit[]"]').val(profit);
        });
    </script>
@endpush
