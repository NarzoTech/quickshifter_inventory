@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Stock Ledger') }}</title>
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

                            <div class="col-xxl-1 col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn bg-label-danger reset-form"><i
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
                <h4 class="section_title"><i class="fas fa-list"></i> Stock Ledger</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
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
                            <th title="Sl">Sl</th>
                            <th title="Date">Date</th>
                            <th title="Details">Details</th>
                            <th title="Invoice No">Invoice No</th>
                            <th title="Type">Type</th>
                            <th title="In Qty">In Qty</th>
                            <th title="Out Qty">Out Qty</th>
                            {{-- <th title="Used Qty">Used Qty</th> --}}
                            <th title="Available Qty">Available Qty</th>
                            <th title="Rate">Rate</th>
                            <th title="Total">Total</th>
                            <th title="Profit/loss">Profit/loss</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $available = 0;
                        @endphp

                        @foreach ($stocks as $stock)
                            @php
                                $available = $stock->in_quantity - $stock->out_quantity;
                                $qty = $stock->in_quantity ?? $stock->out_quantity;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $stock->created_at->format('d-m-Y') }}</td>
                                <td>{{ $product->barcode }}</td>
                                <td>
                                    <a href="{{ $stock->invoice }}">
                                        {{ $stock->purchase->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ ucwords($stock->type) }}</td>
                                <td>{{ $stock->in_quantity }}</td>
                                <td>{{ $stock->out_quantity }}</td>
                                {{-- <td>{{ $stock->used_qty }}</td> --}}
                                <td>{{ $available }}</td>
                                <td>{{ $stock->rate }}</td>
                                <td>{{ $stock->rate * $qty }}</td>
                                <td>{{ $stock->profit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $stocks->onEachSide(0)->links() }}
                </div>
            @endif
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
        });
    </script>
@endpush
