@extends('admin.layouts.master')
@section('title')
    <title>{{ __('New Stock Adjustment') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('admin.stock-adjustment.store') }}">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="section_title">{{ __('New Stock Adjustment') }}</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Product') }} <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-control select2">
                                        <option value="">{{ __('Select Product') }}</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} ({{ $product->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Current Stock') }}</label>
                                    <input type="text" class="form-control" id="current_stock" value="-" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Unit Cost') }}</label>
                                    <input type="text" class="form-control" id="unit_cost_display" value="-" readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="quantity" id="quantity"
                                        value="{{ old('quantity') }}" min="1" placeholder="Enter quantity">
                                    @error('quantity')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Reason') }} <span class="text-danger">*</span></label>
                                    <select name="reason" id="reason" class="form-control">
                                        <option value="">{{ __('Select Reason') }}</option>
                                        <option value="damage" {{ old('reason') == 'damage' ? 'selected' : '' }}>{{ __('Damage') }}</option>
                                        <option value="missing" {{ old('reason') == 'missing' ? 'selected' : '' }}>{{ __('Missing') }}</option>
                                        <option value="theft" {{ old('reason') == 'theft' ? 'selected' : '' }}>{{ __('Theft') }}</option>
                                        <option value="expired" {{ old('reason') == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                        <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                    </select>
                                    @error('reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Date') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker" name="date" id="date"
                                        value="{{ old('date', date('d-m-Y')) }}" autocomplete="off">
                                    @error('date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>{{ __('Note') }}</label>
                                    <textarea class="form-control" name="note" id="note" rows="2"
                                        placeholder="Optional note...">{{ old('note') }}</textarea>
                                    @error('note')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Total Loss') }}</label>
                                    <input type="text" class="form-control fw-bold text-danger" id="total_loss_display"
                                        value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Submit Adjustment') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Product data for stock/cost lookup
            let productsData = @json($productsData);

            // AJAX search for products not in initial list
            let searchTimeout = null;
            $(document).on('input', '[aria-controls="select2-product_id-results"]', function() {
                let input = $(this).val();
                if (searchTimeout) clearTimeout(searchTimeout);
                if (input.length < 2) return;

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('admin.stock-adjustment.product-search') }}",
                        type: 'GET',
                        data: { keyword: input },
                        success: function(response) {
                            if (response.status) {
                                response.data.forEach(function(product) {
                                    if (!productsData[product.id]) {
                                        productsData[product.id] = {
                                            id: product.id,
                                            stock: product.stock,
                                            cost: product.cost,
                                        };
                                        $('#product_id').append(
                                            '<option value="' + product.id + '">' + product.name + ' (' + (product.sku || '') + ')</option>'
                                        );
                                    }
                                });
                            }
                        }
                    });
                }, 300);
            });

            // On product change, show stock and cost
            $('#product_id').on('change', function() {
                let productId = $(this).val();
                if (productId && productsData[productId]) {
                    let product = productsData[productId];
                    $('#current_stock').val(product.stock);
                    $('#unit_cost_display').val(parseFloat(product.cost).toFixed(2));
                    calculateTotalLoss(product.cost);
                } else {
                    $('#current_stock').val('-');
                    $('#unit_cost_display').val('-');
                    $('#total_loss_display').val('0.00');
                }
            });

            // Calculate total loss on quantity change
            $('#quantity').on('input', function() {
                let productId = $('#product_id').val();
                if (productId && productsData[productId]) {
                    calculateTotalLoss(productsData[productId].cost);
                }
            });

            function calculateTotalLoss(cost) {
                let qty = parseInt($('#quantity').val()) || 0;
                let totalLoss = qty * (parseFloat(cost) || 0);
                $('#total_loss_display').val(totalLoss.toFixed(2));
            }
        });
    </script>
@endpush
