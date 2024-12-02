@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Create Quotation') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.quotation.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __('Create Quotation') }}</div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('Customer') }}</label>
                                                <select class="form-control select2" name="customer_id">
                                                    <option value="">{{ __('Select Customer') }}</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('customer_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('Date') }}</label>
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ date('d-m-Y') }}">
                                                @error('date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('Note') }}</label>
                                                <textarea name="note" id="note" class="form-control" cols="30" rows="10"></textarea>
                                                @error('note')
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
                                                        <th>{{ __('Quantity') }}</th>
                                                        <th>{{ __('Unit Price') }}</th>
                                                        <th>{{ __('Total Cost') }}</th>
                                                        <th>
                                                            <i class="fas fa-trash text-danger"></i>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="quotation_table">
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
                                                                class="mt-2">{{ __('Subtotal') }}</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="number" class="form-control" name="subtotal"
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
                                                                class="mt-2">{{ __('Discount / Less') }}</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control" name="discount"
                                                                value="" placeholder="amount or %">
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
                                                                class="mt-2">{{ __('After Discount') }}</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="number" class="form-control" name="after_discount"
                                                                value="" readonly>
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
                                                                class="mt-2">{{ __('Vat / Add') }}</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control" name="vat"
                                                                value="" placeholder="amount or %">
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
                                        </div>
                                    </div>
                                    <div class="card-action d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success me-2">{{ __('Submit') }}</button>
                                        <a href="{{ route('admin.quotation.index') }}"
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
            $(document).on('change', '#product_id', function() {
                let product_id = $(this).val();
                const products = @json($products);
                const product = products.find(p => p.id == product_id);
                addQuotationRow(product);
            });

            $(document).on('input', 'input[name="quantity[]"], input[name="unit_price[]"]', function() {
                var tr = $(this).closest('tr');
                var quantity = tr.find('input[name="quantity[]"]').val();
                var unit_price = tr.find('input[name="unit_price[]"]').val();
                var total = quantity * unit_price;
                tr.find('input[name="total[]"]').val(total);
                calculateTotalAmount();
            });


            $('input[name="discount"], input[name="vat"]').on('input', function() {
                calculateTotalAmount();
            })
        })



        function calculateTotalAmount() {

            let totalAmount = 0;
            $('input[name="total[]"]').each(function() {
                totalAmount += parseFloat($(this).val());
            });
            $('[name="subtotal"]').val(totalAmount);

            // discount

            const discount = $('[name="discount"]').val();
            let discountAmount = 0;

            if (discount.includes('%')) {
                const discountPercentage = parseFloat(discount.replace('%', '')); // Remove '%' and parse the number
                discountAmount = totalAmount * (discountPercentage / 100);
            } else {
                discountAmount = parseFloat(discount || 0);
            }

            // total after discount = total - discount
            const amountAfterDiscount = totalAmount - discountAmount;
            $('[name="after_discount"]').val(amountAfterDiscount);


            // vat

            const vat = $('[name="vat"]').val();
            let vatAmount = 0;
            if (vat.includes('%')) {
                const vatPercentage = parseFloat(vat.replace('%', '')); // Remove '%' and parse the number
                vatAmount = totalAmount * (vatPercentage / 100);
            } else {
                vatAmount = parseFloat(vat || 0);
            }

            // total amount

            $('[name="total_amount"]').val(amountAfterDiscount + vatAmount);

        }

        function addQuotationRow(product) {

            let tr = `
                <tr>
                    <td>
                        <input type="text" class="form-control" name="product_name[]" value="${product.name}" readonly>
                        <input type="hidden" name="product_id[]" value="${product.id}">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="quantity[]" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="unit_price[]" value="${product.current_price}" min="0">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="total[]" value="${product.current_price}" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-white" onclick="removequotationRow(this)"><i class="fas fa-trash text-danger"></i></button>
                    </td>
                </tr>
            `;

            // check if product is already added
            if ($('#quotation_table tr').length > 0) {
                let isProductAdded = false;
                $('#quotation_table tr').each(function() {
                    let product_id = $(this).find('input[name="product_id[]"]').val();
                    if (product_id == product.id) {
                        isProductAdded = true;
                    }
                });
                if (isProductAdded) {
                    return;
                }
            }



            $('#quotation_table').append(tr);
            calculateTotalAmount();
        }

        function removequotationRow(row) {
            $(row).closest('tr').remove();
            calculateTotalAmount();
        }
    </script>
@endpush
