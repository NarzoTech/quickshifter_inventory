@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Purchase List') }}</title>
@endsection

@section('content')
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
                                                <input type="text" class="form-control" name="invoice_number"
                                                    value="{{ old('invoice_number', $invoiceNumber) }}">
                                                @error('invoice_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Memo No') }}</label>
                                                <input type="text" class="form-control" name="memo_no"
                                                    value="{{ old('memo_no') }}">
                                                @error('memo_no')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Purchase Date') }}</label>
                                                <input type="text" class="form-control datepicker" name="purchase_date"
                                                    value="{{ old('purchase_date', now()->format('d-m-Y')) }}">
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
                                        <div class="col-md-12">
                                            <div class="row d-flex justify-content-end">
                                                <div class="col-md-10">
                                                    <div class="form-group row">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-3 text-md-right">
                                                            <label for=""
                                                                class="mt-2">{{ __('Item Count') }}</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="number" class="form-control" name="items"
                                                                value="0" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row d-flex justify-content-end">
                                                <div class="col-md-10">
                                                    <div class="form-group row">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-3 text-md-right">
                                                            <label for=""
                                                                class="mt-2">{{ __('Total Amount') }}</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="total_amount" class="form-control"
                                                                name="total_amount" value="0" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row d-flex justify-content-end">

                                                <div class="col-md-10">
                                                    <div class="">
                                                        <div class="form-group row">
                                                            <div class="col-md-4"></div>
                                                            <div class="col-md-3 text-md-right">
                                                                <label for=""
                                                                    class="mt-2">{{ __('Payment Type') }}</label>
                                                            </div>
                                                            <div class="col-md-5 paymentsystem">
                                                                @include('purchase::add-payment-method')
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="offset-md-10 col-md-10">
                                                    <div class="form-group row">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-3 text-md-right">
                                                            <label for=""
                                                                class="mt-2">{{ __('Due') }}</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control" name="due_amount"
                                                                readonly>
                                                        </div>
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

        $(document).ready(function() {
            const accountsList = @json($accounts);
            $(document).on('change', 'select[name="payment_type[]"]', function() {
                const accounts = accountsList.filter(account => account.account_type == $(this).val());
                const accountInput = $(this).closest('.payment-row').find('.account');
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


                    accountInput.html(html);
                }

                if ($(this).val() == 'cash') {
                    accountInput.html('');
                    const cash =
                        `<input type="text" name="account_id[]" class="form-control" value="${$(this).val()}" readonly>`;

                    accountInput.html(cash);
                }
            });

            $('.addPayment').on('click', function() {
                const add = `@include('purchase::add-payment-method', ['add' => true])`

                $('.paymentsystem').append(add);
            })
            $(document).on('click', '.removePayment', function() {
                $(this).parents('.payment-row').remove();
            })
        })

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
                        <input type="text" class="form-control" name="profit[]" value="${0}">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="selling_price[]" value="${product.price}" min="0">
                    </td>
                    <td>
                        <button type="button" class="btn btn-white" onclick="removePurchaseRow(this)"><i class="fas fa-trash text-danger"></i></button>
                    </td>
                </tr>
            `;

            // check if product is already added
            if ($('#purchase_table tr').length > 0) {
                let isProductAdded = false;
                $('#purchase_table tr').each(function() {
                    let product_id = $(this).find('input[name="product_id[]"]').val();
                    if (product_id == product.id) {
                        isProductAdded = true;
                    }
                });
                if (isProductAdded) {
                    return;
                }
            }
            $('#purchase_table').append(tr);
            calculateTotalAmount();
        }

        function removePurchaseRow(row) {
            $(row).closest('tr').remove();
            calculateTotalAmount();
        }
        const products = @json($products);
        $(document).on('change', '#product_id', function() {
            let product_id = $(this).val();

            const product = products.find(p => p.id == product_id);
            addPurchaseRow(product);
        });

        // when search product will not in the product list. it will search from the database;
        $(document).on('input', '[aria-controls="select2-product_id-results"]', function() {
            let input = $(this).val();
            // check if input its in product name or product code
            const filteredProducts = products.filter(p => p.name.toLowerCase().includes(input.toLowerCase()) ||
                p.barcode.toLowerCase().includes(input.toLowerCase()));

            if (filteredProducts.length == 0) {
                // check if input its in product name or product code in the database
                $.ajax({
                    url: "{{ route('admin.purchase.product.search') }}",
                    type: 'POST',
                    data: {
                        keyword: input
                    },
                    success: function(response) {
                        if (response.status) {
                            let html = '';
                            response.data.forEach(product => {
                                products.push(product);
                                html +=
                                    `<option value="${product.id}">${product.name}</option>`;
                            });
                            $('#product_id').append(html);
                        }

                    }
                })
            }
        })

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

            calculateTotalAmount();
        })

        $(document).on('change', '[name="payment_type"]', function() {
            let payment_type = $(this).val();
            if (payment_type != '') {
                $('[name="payment_method"]').val(payment_type);
            }
        })
        $(document).on('input', '[name="paid_amount[]"]', function() {
            calculateDue()
        })

        $(document).on('change', '[name="payment_type"]', function() {
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
            $('[name="due_amount"]').val(totalAmount);
        }

        function calculateDue() {

            let totalAmount = $('[name="total_amount"]').val();
            let paidAmount = $('[name="paid_amount[]"]');

            let dueAmount = totalAmount;
            paidAmount.each(function() {
                dueAmount -= parseFloat($(this).val() || 0);
            })

            $('[name="due_amount"]').val(dueAmount);
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
