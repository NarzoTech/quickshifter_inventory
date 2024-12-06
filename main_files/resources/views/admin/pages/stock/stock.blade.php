@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Stock List') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-1">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <select name="order_by" id="order_by" class="form-control">
                                        <option value="">{{ __('Order By') }}</option>
                                        <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                            {{ __('ASC') }}
                                        </option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                            {{ __('DESC') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('10') }}
                                        </option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('50') }}
                                        </option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('100') }}
                                        </option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('All') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <select name="brand_id" id="brand_id" class="form-control select2">
                                        <option value="" selected disabled>{{ __('Brand') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <select name="category_id" id="categories" class="form-control select2">
                                        <option value="" selected disabled>{{ __('Categories') }}
                                        </option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <select name="stock_status" id="stock_status" class="form-control select2">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="in_stock"
                                            {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>
                                            {{ __('In Stock') }}</option>
                                        <option value="out_of_stock"
                                            {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>
                                            {{ __('Stock Out') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="From Date" name="from_date"
                                        value="{{ request()->get('from_date') }}" class="form-control datepicker"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="To Date" name="to_date"
                                        value="{{ request()->get('to_date') }}" class="form-control datepicker"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-1 col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn bg-label-danger"><i
                                            class='bx bx-rotate-right'></i></button>

                                    <button type="submit" class="btn bg-label-primary"><i
                                            class='bx bx-search'></i></button>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"><i class="fas fa-list"></i> Stock List</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="javascript:;" class="btn btn-danger reset-button">{{ __('Reset Stock') }}</a>
                <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Picture') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Avg P.P') }}</th>
                            <th>{{ __('L. P.P') }}</th>
                            <th>{{ __('Selling Price') }}</th>
                            {{-- <th style="display: none;">Business Branch</th> --}}
                            <th>{{ __('In Quantity') }}</th>
                            <th>{{ __('Out Quantity') }}</th>
                            <th>{{ __('Stock') }}</th>
                            <th>{{ __('Stock P.P') }}</th>
                            <th>{{ __('Stock S.P') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $index => $product)
                            @php
                                $stock = $product->stock < 0 ? 0 : $product->stock;
                                $selling_price = $product->selling_price ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ asset($product->single_image) }}" alt="Product Picture" width="100">
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->avg_purchase_price }}</td>
                                <td>{{ $product->last_purchase_price }}</td>
                                <td>{{ $product->selling_price }}</td>
                                {{-- <td style="display: none;">{{ $product->business_branch->name }}</td> --}}
                                <td>{{ $product->stockDetails->sum('in_quantity') }}</td>
                                <td>{{ $product->stockDetails->sum('out_quantity') }}
                                </td>
                                <td>{{ $product->stock }}</td>
                                <td>{{ remove_comma($stock) * remove_comma($product->avg_purchase_price) }}
                                </td>
                                <td>
                                    {{ remove_comma($stock) * remove_comma($selling_price) }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.product.show', $product->id) }}"
                                        class="btn btn-primary btn-sm" title="Product Details"><i
                                            class="fa fa-eye"></i></a>
                                    <a href="{{ route('admin.stock.ledger', $product->id) }}" class="btn btn-info btn-sm"
                                        title="Stock Ledger">
                                        <i class="fas fa-clipboard-list"></i>
                                    </a>

                                    {{-- reset stock --}}
                                    {{-- {{ route('admin.stock.reset', $product->id) }} --}}
                                    <a href="javascript:;" class="btn btn-danger btn-sm" title="Reset Stock"
                                        onclick="resetStock({{ $product->id }})" data-bs-target="#stockModal"
                                        data-bs-toggle="modal">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $products->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="stockModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Stock Reset Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are You sure want to Reset Stock') }}?</p>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <form id="resetForm" action="" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Reset') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            'use strict';
            $('.export').on('click', function() {
                // get full url including query string
                var fullUrl = window.location.href;
                if (fullUrl.includes('?')) {
                    fullUrl += '&export=true';
                } else {
                    fullUrl += '?export=true';
                }

                window.location.href = fullUrl;
            })

            $('.reset-button').on('click', function() {
                $('#resetForm').attr('action', "{{ route('admin.stock.reset.all') }}");

                $('#stockModal').modal('show');
            })
        });

        function resetStock(id) {
            $('#resetForm').attr('action', "{{ route('admin.stock.reset', ':id') }}".replace(':id', id));
        }
    </script>
@endpush
