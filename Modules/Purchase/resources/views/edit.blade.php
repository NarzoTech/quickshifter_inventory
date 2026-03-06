@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Purchase') }}</title>
@endsection

@push('css')
<style>
    .product-name-wrapper {
        position: relative;
    }
    .product-name-wrapper .product-tooltip {
        visibility: hidden;
        opacity: 0;
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: #fff;
        color: #333;
        padding: 10px;
        border-radius: 8px;
        font-size: 13px;
        z-index: 1000;
        transition: opacity 0.3s, visibility 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        margin-bottom: 8px;
        min-width: 150px;
        max-width: 250px;
        width: max-content;
        text-align: center;
    }
    .product-name-wrapper .product-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 8px;
        border-style: solid;
        border-color: #fff transparent transparent transparent;
    }
    .product-name-wrapper .product-tooltip img {
        max-width: 120px;
        max-height: 120px;
        border-radius: 6px;
        margin-bottom: 8px;
        object-fit: cover;
    }
    .product-name-wrapper .product-tooltip .tooltip-name {
        font-weight: 600;
        word-wrap: break-word;
        white-space: normal;
        max-width: 200px;
        overflow-wrap: break-word;
    }
    .product-name-wrapper:hover .product-tooltip {
        visibility: visible;
        opacity: 1;
    }
</style>
@endpush

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.purchase.update', $purchase->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card">

                                <div class="card-header d-flex justify-content-between">
                                    <h4 class="section_title">{{ __('Edit Purchase') }}</h4>
                                    <div>
                                        <a href="{{ route('admin.purchase.index') }}" class="btn btn-primary"><i
                                                class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Supplier') }}</label>
                                                <select class="form-control select2" name="supplier_id">
                                                    <option value="">{{ __('Select Supplier') }}</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ $supplier->id == $purchase->supplier_id ? 'selected' : '' }}>
                                                            {{ $supplier->company }}</option>
                                                    @endforeach
                                                </select>
                                                <small id="supplier-advance-info" class="text-info fw-bold" style="display:none;"></small>
                                                @error('supplier_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Invoice Number') }}</label>
                                                <input type="text" class="form-control" id="invoice_number_display" value="{{ $purchase->invoice_number }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Memo No') }}</label>
                                                <input type="text" class="form-control" name="memo_no"
                                                    value="{{ $purchase->memo_no }}">
                                                @error('memo_no')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Purchase Date') }}</label>
                                                <input type="text" class="form-control datepicker" name="purchase_date"
                                                    value="{{ formatDate($purchase->purchase_date) }}"
                                                    autocomplete="off">
                                                @error('purchase_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Reference No') }}</label>
                                                <input type="text" class="form-control" name="reference_no"
                                                    value="{{ $purchase->reference_no }}">
                                                @error('reference_no')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{ __('Attachment') }}</label>
                                                <input type="file" class="form-control" name="attachment" value="">
                                                @error('attachment')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
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
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Product Name') }}</th>
                                                            <th>{{ __('Product Stock') }}</th>
                                                            <th>{{ __('Quantity') }}</th>
                                                            <th>{{ __('Purchase Price') }}</th>
                                                            <th>{{ __('Sub Total') }}</th>
                                                            <th>{{ __('Profit') }}%</th>
                                                            <th>{{ __('Selling Price') }}</th>
                                                            <th class="text-center">
                                                                <i class="fas fa-trash text-danger"></i>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="purchase_table">
                                                        @php
                                                            $qty = 0;
                                                            $sub_total = 0;
                                                        @endphp
                                                        @foreach ($purchase->purchaseDetails as $purchaseDetail)
                                                            @php
                                                                $qty += $purchaseDetail->quantity;
                                                                $sub_total += $purchaseDetail->sub_total;
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <div class="product-name-wrapper">
                                                                        <input type="text" class="form-control"
                                                                            name="product_name[]"
                                                                            value="{{ $purchaseDetail->product->name }}"
                                                                            readonly>
                                                                        <div class="product-tooltip">
                                                                            <img src="{{ $purchaseDetail->product->singleImage }}" alt="{{ $purchaseDetail->product->name }}" onerror="this.src='{{ asset('backend/img/image_icon.png') }}'">
                                                                            <div class="tooltip-name">{{ $purchaseDetail->product->name }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="product_id[]"
                                                                        value="{{ $purchaseDetail->product_id }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="stock[]"
                                                                        value="{{ $purchaseDetail->product->stock }}"
                                                                        readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="quantity[]"
                                                                        value="{{ $purchaseDetail->quantity }}"
                                                                        min="1">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="unit_price[]"
                                                                        value="{{ $purchaseDetail->purchase_price }}"
                                                                        min="0" step="0.01">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="total[]"
                                                                        value="{{ $purchaseDetail->sub_total }}" readonly
                                                                        step="0.01">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        name="profit[]"
                                                                        value="{{ $purchaseDetail->profit }}"
                                                                        step="0.01">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control"
                                                                        name="selling_price[]"
                                                                        value="{{ $purchaseDetail->sale_price }}"
                                                                        min="0" step="0.01">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-white"
                                                                        onclick="removePurchaseRow(this)"><i
                                                                            class="fas fa-trash text-danger"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- summery --}}
                                    <div class="row justify-content-end mt-5">
                                        <div class="col-xxl-5 col-xl-6 col-lg-7">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="mt-2">{{ __('Item Count') }}</label>
                                                        <input type="number" class="form-control" name="items"
                                                            value="{{ $purchase->items }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Total Amount') }}</label>
                                                        <input type="total_amount" class="form-control"
                                                            name="total_amount" value="{{ $purchase->total_amount }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <label class="mb-0">{{ __('Payment Type') }}</label>
                                                            <div class="d-flex gap-1">
                                                                <a href="javascript:;" class="btn btn-primary d-none" id="useAdvanceBtn" onclick="useAdvancePayment()" style="font-size: 12px; padding: 4px 10px; color: white;">
                                                                    {{ __('Use Advance') }} (<span id="advanceAvailable">0</span>)
                                                                </a>
                                                                <a href="javascript:;" class="btn bg-label-warning d-none" id="offsetDueBtn" onclick="offsetDueWithAdvance()" style="font-size: 12px; padding: 4px 10px;">
                                                                    {{ __('Offset Due') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="paymentsystem">
                                                            @include('purchase::edit-payment-method')
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Due') }}</label>
                                                        <input type="text" class="form-control" name="due_amount"
                                                            readonly value="{{ $purchase->due_amount }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card-action d-flex justify-content-end">
                                                <a href="{{ route('admin.purchase.index') }}"
                                                    class="btn me-2 btn-danger">{{ __('Cancel') }}</a>
                                                <button type="submit"
                                                    class="btn btn-primary">{{ __('Submit') }}</button>
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
        'use strict';

        let currentSupplierAdvance = 0;
        let currentSupplierDue = 0;

        $(document).ready(function() {
            const accountsList = @json($accounts);
            const supplierAdvances = {
                @foreach($suppliers as $supplier)
                    @php
                        $rawDue = $supplier->total_due;
                        $rawAdv = $supplier->advance;
                        $off = min(max(0, $rawDue), max(0, $rawAdv));
                    @endphp
                    {{ $supplier->id }}: {{ $rawAdv - $off }},
                @endforeach
            };

            // Show advance on load for selected supplier
            const initialSupplier = $('select[name="supplier_id"]').val();
            if (initialSupplier) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('admin.supplier.single', '') }}/" + initialSupplier,
                    success: function(response) {
                        currentSupplierAdvance = parseFloat(response.advance_balance) || 0;
                        currentSupplierDue = parseFloat(response.total_due) || 0;
                        if (currentSupplierAdvance > 0) {
                            $('#supplier-advance-info').text('{{ __("Advance Balance") }}: ' + currentSupplierAdvance.toLocaleString()).show();
                        }
                        updateAdvanceButtons();
                    }
                });
            }

            $('select[name="supplier_id"]').on('change', function() {
                const supplierId = $(this).val();
                const advanceInfo = $('#supplier-advance-info');
                if (supplierId) {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('admin.supplier.single', '') }}/" + supplierId,
                        success: function(response) {
                            currentSupplierAdvance = parseFloat(response.advance_balance) || 0;
                            currentSupplierDue = parseFloat(response.total_due) || 0;
                            if (currentSupplierAdvance > 0) {
                                advanceInfo.text('{{ __("Advance Balance") }}: ' + currentSupplierAdvance.toLocaleString()).show();
                            } else {
                                advanceInfo.hide();
                            }
                            updateAdvanceButtons();
                        }
                    });
                } else {
                    currentSupplierAdvance = 0;
                    currentSupplierDue = 0;
                    advanceInfo.hide();
                    updateAdvanceButtons();
                }
            });

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

                if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                    accountInput.html('');
                    const cash =
                        `<input type="text" name="account_id[]" class="form-control" value="${$(this).val()}" readonly>`;

                    accountInput.html(cash);
                }
            });

            $('.addPayment').on('click', function() {
                const add = `@include('purchase::add-payment-method', ['add' => true])`

                $('.paymentsystem').append(add);
                $('select.nice-select').niceSelect();
            })
            $(document).on('click', '.removePayment', function() {
                $(this).parents('.payment-row').remove();
                calculateDue()
            })
        })

        function addPurchaseRow(product) {
            // calculation profit per product on product cost and product price
            let profit = ((parseFloat(product.price) - parseFloat(product.cost)) / parseFloat(product.cost)) * 100;
            let tr = `
                <tr>
                    <td>
                        <div class="product-name-wrapper">
                            <input type="text" class="form-control" name="product_name[]" value="${product.name}" readonly>
                            <div class="product-tooltip">
                                <img src="${product.single_image}" alt="${product.name}" onerror="this.src='{{ asset('backend/img/image_icon.png') }}'">
                                <div class="tooltip-name">${product.name}</div>
                            </div>
                        </div>
                        <input type="hidden" name="product_id[]" value="${product.id}">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="stock[]" value="${product.stock}" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="quantity[]" value="1" min="1">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="unit_price[]" value="${product.cost}" min="0" step="0.01">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="total[]" value="${product.cost}" readonly step="0.01">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="profit[]" value="${0}" step="0.01">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="selling_price[]" value="${product.price}" min="0" step="0.01">
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

        // Debounce function to limit API calls
        let searchTimeout = null;

        // when search product will not in the product list. it will search from the database;
        $(document).on('input', '[aria-controls="select2-product_id-results"]', function() {
            let input = $(this).val();

            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            if (input.length < 2) {
                return;
            }

            // check if input its in product name or product code
            const filteredProducts = products.filter(p =>
                p.name.toLowerCase().includes(input.toLowerCase()) ||
                (p.barcode && p.barcode.toLowerCase().includes(input.toLowerCase())) ||
                (p.sku && p.sku.toLowerCase().includes(input.toLowerCase()))
            );

            if (filteredProducts.length == 0) {
                // Debounce the API call - wait 300ms after user stops typing
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('admin.purchase.product.search') }}",
                        type: 'POST',
                        data: {
                            keyword: input
                        },
                        success: function(response) {
                            if (response.status) {
                                response.data.forEach(product => {
                                    // Check if product already exists in the list
                                    const existingProduct = products.find(p => p.id == product.id);

                                    if (!existingProduct) {
                                        products.push(product);
                                        $('#product_id').append(
                                            `<option value="${product.id}">${product.name} (${product.sku})</option>`
                                        );
                                    }
                                });
                            }
                        }
                    });
                }, 300);
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

            calculateDue()
        }

        function calculateDue() {

            let totalAmount = parseFloat($('[name="total_amount"]').val()) || 0;
            let paidAmount = $('[name="paid_amount[]"]');

            let dueAmount = totalAmount;
            paidAmount.each(function() {
                dueAmount -= parseFloat($(this).val()) || 0;
            })

            $('[name="due_amount"]').val(dueAmount);
        }

        function updateAdvanceButtons() {
            if (currentSupplierAdvance > 0) {
                $('#advanceAvailable').text(parseFloat(currentSupplierAdvance).toLocaleString());
                $('#useAdvanceBtn').removeClass('d-none');
            } else {
                $('#useAdvanceBtn').addClass('d-none');
            }
            if (currentSupplierAdvance > 0 && currentSupplierDue > 0) {
                $('#offsetDueBtn').removeClass('d-none');
            } else {
                $('#offsetDueBtn').addClass('d-none');
            }
        }

        function useAdvancePayment() {
            let advanceExists = false;
            $('[name="payment_type[]"]').each(function() {
                if ($(this).val() === 'advance') advanceExists = true;
            });
            if (advanceExists) {
                toastr.warning("{{ __('Advance payment row already exists') }}");
                return;
            }

            let totalAmount = parseFloat($('[name="total_amount"]').val()) || 0;
            let currentlyPaid = 0;
            $('[name="paid_amount[]"]').each(function() {
                currentlyPaid += parseFloat($(this).val()) || 0;
            });
            let remaining = totalAmount - currentlyPaid;
            if (remaining <= 0) {
                toastr.warning("{{ __('No remaining amount to pay') }}");
                return;
            }

            let advanceAmount = Math.min(currentSupplierAdvance, remaining);

            const add = `@include('purchase::add-payment-method', ['add' => true])`;
            $('.paymentsystem').append(add);
            $('select.nice-select').niceSelect();

            const lastRow = $('.payment-row:last');
            lastRow.find('[name="payment_type[]"]').val('advance').niceSelect('update');
            lastRow.find('.account').html('<input type="text" name="account_id[]" class="form-control" value="advance" readonly>');
            lastRow.find('[name="paid_amount[]"]').val(advanceAmount);

            calculateDue();
        }

        function offsetDueWithAdvance() {
            const supplierId = $('select[name="supplier_id"]').val();
            if (!supplierId) {
                toastr.warning("{{ __('Please select a supplier') }}");
                return;
            }

            if (!confirm("{{ __('This will use advance balance to pay off existing outstanding dues. Continue?') }}")) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('admin.supplier.offset-due-advance') }}",
                data: { supplier_id: supplierId, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        currentSupplierAdvance = parseFloat(response.advance_balance) || 0;
                        currentSupplierDue = parseFloat(response.total_due) || 0;

                        const advanceInfo = $('#supplier-advance-info');
                        if (currentSupplierAdvance > 0) {
                            advanceInfo.text('{{ __("Advance Balance") }}: ' + currentSupplierAdvance.toLocaleString()).show();
                        } else {
                            advanceInfo.hide();
                        }

                        updateAdvanceButtons();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    toastr.error(response.responseJSON?.message || "{{ __('Server error occurred') }}");
                }
            });
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

        // Form validation
        function validatePurchaseForm() {
            let errors = [];

            // Supplier validation
            if (!$('[name="supplier_id"]').val()) {
                errors.push('{{ __("Supplier is required") }}');
                $('[name="supplier_id"]').closest('.form-group').find('.text-danger').remove();
                $('[name="supplier_id"]').closest('.form-group').append('<span class="text-danger">{{ __("Supplier is required") }}</span>');
            } else {
                $('[name="supplier_id"]').closest('.form-group').find('.text-danger').remove();
            }

            // Purchase date validation
            if (!$('[name="purchase_date"]').val()) {
                errors.push('{{ __("Purchase date is required") }}');
                $('[name="purchase_date"]').closest('.form-group').find('.text-danger').remove();
                $('[name="purchase_date"]').closest('.form-group').append('<span class="text-danger">{{ __("Purchase date is required") }}</span>');
            } else {
                $('[name="purchase_date"]').closest('.form-group').find('.text-danger').remove();
            }

            // Products validation
            if ($('#purchase_table tr').length === 0) {
                errors.push('{{ __("At least one product is required") }}');
            }

            // Quantity validation
            let quantityValid = true;
            $('input[name="quantity[]"]').each(function() {
                if (!$(this).val() || parseFloat($(this).val()) <= 0) {
                    quantityValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            if (!quantityValid) {
                errors.push('{{ __("Quantity must be greater than 0") }}');
            }

            // Payment type validation
            let paymentTypeValid = true;
            $('[name="payment_type[]"]').each(function() {
                if (!$(this).val()) {
                    paymentTypeValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            if (!paymentTypeValid) {
                errors.push('{{ __("Payment type is required") }}');
            }

            // Paid amount validation
            let paidAmountValid = true;
            $('[name="paid_amount[]"]').each(function() {
                if ($(this).val() === '' || $(this).val() === null) {
                    paidAmountValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            if (!paidAmountValid) {
                errors.push('{{ __("Paid amount is required") }}');
            }

            return errors;
        }

        // Form submit handler
        $('form').on('submit', function(e) {
            let errors = validatePurchaseForm();

            if (errors.length > 0) {
                e.preventDefault();
                errors.forEach(function(error) {
                    toastr.error(error);
                });
                return false;
            }

            return true;
        });

        // Real-time validation on field change
        $(document).on('change', '[name="supplier_id"]', function() {
            if ($(this).val()) {
                $(this).closest('.form-group').find('.text-danger').remove();
            }
        });

        $(document).on('change', '[name="purchase_date"]', function() {
            if ($(this).val()) {
                $(this).closest('.form-group').find('.text-danger').remove();
                $.get("{{ route('admin.purchase.invoice-number') }}", { date: $(this).val() }, function(response) {
                    $('#invoice_number_display').val(response.invoice_number);
                });
            }
        });

        $(document).on('change', '[name="payment_type[]"]', function() {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
            }
        });

        $(document).on('input', '[name="paid_amount[]"]', function() {
            if ($(this).val() !== '') {
                $(this).removeClass('is-invalid');
            }
        });

        $(document).on('input', '[name="quantity[]"]', function() {
            if ($(this).val() && parseFloat($(this).val()) > 0) {
                $(this).removeClass('is-invalid');
            }
        });
    </script>
@endpush
