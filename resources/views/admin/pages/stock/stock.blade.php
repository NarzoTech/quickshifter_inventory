@extends('admin.master_layout')
@section('title')
    <title>{{ __('Current Stock') }}</title>
@endsection

@push('css')
    <style>
        thead tr:nth-child(odd) {
            background-color: lightskyblue;

        }


        thead tr:nth-child(even) {
            background-color: lightpink;
        }

        thead>tr>th {
            /* background-color: lightseagreen; */
            color: white !important;
        }
    </style>
@endpush
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Stock') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" onchange="this.submit()" class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="{{ __('Search') }}">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="1" {{ request('order_by') == '1' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="0" {{ request('order_by') == '0' ? 'selected' : '' }}>
                                                    {{ __('DESC') }}
                                                </option>
                                            </select>
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
                                                <option value="100"
                                                    {{ '100' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('100') }}
                                                </option>
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
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
                                                        <img src="{{ asset($product->single_image) }}"
                                                            alt="Product Picture" width="100">
                                                    </td>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->avg_purchase_price }}</td>
                                                    <td>{{ $product->last_purchase_price }}</td>
                                                    <td>{{ $product->selling_price }}</td>
                                                    {{-- <td style="display: none;">{{ $product->business_branch->name }}</td> --}}
                                                    <td>{{ $product->stockDetails->sum('quantity') }}</td>
                                                    <td>{{ $product->stockDetails->sum('quantity') - $product->stock }}
                                                    </td>
                                                    <td>{{ $product->stock }}</td>
                                                    <td>{{ remove_comma($stock) * remove_comma($product->avg_purchase_price) }}
                                                    </td>
                                                    <td>
                                                        {{ remove_comma($stock) * remove_comma($selling_price) }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.product.show', $product->id) }}"
                                                            class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
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
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
