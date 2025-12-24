@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Barcode Wise Product Report') }}</title>
@endsection


@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET">
                <div class="row">
                    <div class="col-xxl-3 col-md-4">
                        <div class="form-group search-wrapper">
                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}" class="form-control"
                                placeholder="Search">
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
                    <div class="col-xxl-3 col-md-4">
                        <div class="form-group">
                            <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                <input type="text" id="dateRangePicker" placeholder="From Date"
                                    class="form-control datepicker" name="from_date"
                                    value="{{ request()->get('from_date') }}" autocomplete="off">
                                <span class="input-group-text">to</span>
                                <input type="text" placeholder="To Date" class="form-control datepicker" name="to_date"
                                    value="{{ request()->get('to_date') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4">
                        <div class="form-group">
                            <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                            <button type="submit" class="btn bg-label-primary">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Report List') }}</h4>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Attribute') }}</th>
                            <th>{{ __('Barcode') }}</th>
                            <th>{{ __('Brand Name') }}</th>
                            <th>{{ __('Sale') }}</th>
                            <th>{{ __('Sale Return') }}</th>
                            <th>{{ __('Purchase') }}</th>
                        </tr>

                    </thead>
                    <tbody>
                        @php
                            $start = checkPaginate($products) ? $products->firstItem() : 1;
                        @endphp
                        @foreach ($products as $index => $product)
                            @php
                                // Only count sales from own inventory (source = 1)
                                $ownSalesQuery = \Modules\Sales\app\Models\ProductSale::where('product_id', $product->id)
                                    ->where('source', 1);
                                
                                $ownSalesReturnsQuery = \Modules\Sales\app\Models\SalesReturnDetails::where('product_id', $product->id)
                                    ->where('source', 1);
                                
                                // Apply date filters if provided
                                if (request('from_date') || request('to_date')) {
                                    $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
                                    $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
                                    
                                    $ownSalesQuery->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                                        $q->whereBetween('order_date', [$fromDate, $toDate]);
                                    });
                                    
                                    $ownSalesReturnsQuery->whereHas('saleReturn', function ($q) use ($fromDate, $toDate) {
                                        $q->whereBetween('created_at', [$fromDate, $toDate]);
                                    });
                                }
                                
                                $ownSales = $ownSalesQuery->get();
                                $ownSalesReturns = $ownSalesReturnsQuery->get();
                                
                                // Calculate sales
                                $saleQty = $ownSales->sum('quantity');
                                $salePrice = $ownSales->sum('sub_total');
                                
                                // Calculate returns
                                $returnQty = $ownSalesReturns->sum('quantity');
                                $returnPrice = $ownSalesReturns->sum('sub_total');
                                
                                // Get purchase data
                                $purchasePrice = (int) $product->total_purchase['price'];
                                $purchaseQty = $product->total_purchase['qty'];
                            @endphp
                            <tr>
                                <td>{{ $start + $index }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->attribute }}</td>
                                <td>{{ $product->barcode }}</td>
                                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                <td>{{ currency($salePrice) }}({{ number_format($saleQty, 0) }})
                                </td>
                                <td>{{ currency($returnPrice) }}({{ number_format($returnQty, 0) }})
                                </td>
                                <td>{{ currency($purchasePrice) }}({{ number_format($purchaseQty, 0) }})
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="text-end">
                                <b>{{ __('Total') }}</b>
                            </td>
                            <td>
                                <b>{{ currency($data['totalSalePrice']) }}({{ $data['totalSaleQty'] }})</b>
                            </td>
                            <td>
                                <b>{{ currency($data['totalReturnPrice']) }}({{ $data['totalReturnQty'] }})</b>
                            </td>
                            <td>
                                <b>{{ currency($data['totalPurchasePrice']) }}({{ $data['totalPurchaseQty'] }})</b>
                            </td>
                        </tr>
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
@endsection
