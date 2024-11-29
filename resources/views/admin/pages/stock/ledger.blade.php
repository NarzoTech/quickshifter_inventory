@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Stock Ledger') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Stock Ledger') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group search-wrapper">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="{{ __('Search') }}">
                                            <button type="submit">
                                                <i class="far fa-arrow-alt-circle-right"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-2 form-group">
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
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
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
                                        <div class="col-md-1 form-group">
                                            <a href="{{ route('admin.stock.index') }}"
                                                class="btn btn-danger">{{ __('Reset') }}</a>
                                        </div>
                                    </div>
                                    {{-- excel  buttons --}}
                                    <div class="row">
                                        <div class="col-md-4 form-group mx-auto">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button type="button" class="btn btn-secondary export"><i
                                                        class="far fa-file-excel"></i>
                                                    Excel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
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
