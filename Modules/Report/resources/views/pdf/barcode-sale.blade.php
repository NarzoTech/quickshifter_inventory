@extends('admin.layouts.pdf-layout')

@section('title', __('Barcode Wise Sale Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Product Name'),
                    __('Barcode'),
                    __('Brand Name'),
                    __('Stock Qty'),
                    __('Selling Qty'),
                    __('Selling Price'),
                    __('Purchase Price'),
                    __('Profit/Loss'),
                ];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
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
                    
                    // Calculate quantities
                    $sellQty = $ownSales->sum('quantity') - $ownSalesReturns->sum('quantity');
                    $totalSalesPrice = $ownSales->sum('sub_total');
                    $totalSalesReturnPrice = $ownSalesReturns->sum('sub_total');
                    
                    // Net sales price after returns
                    $netSalesPrice = $totalSalesPrice - $totalSalesReturnPrice;
                    
                    // Average selling price per unit
                    $avgSellingPrice = $sellQty > 0 ? $netSalesPrice / $sellQty : 0;
                    
                    // Get purchase price
                    $purchasePrice = $product->LastPurchasePrice ?: $product->cost;
                    
                    // Calculate profit/loss
                    $profitLoss = $netSalesPrice - ($sellQty * $purchasePrice);
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                    <td>{{ number_format($product->stock_count, 0) }}</td>
                    <td>{{ number_format($sellQty, 0) }}</td>
                    <td>{{ currency($avgSellingPrice) }}</td>
                    <td>{{ currency($purchasePrice) }}</td>
                    <td>{{ currency($profitLoss) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-end">
                    <b>Total</b>
                </td>
                <td>
                    <b>{{ number_format($data['totalStock'], 0) }}</b>
                </td>
                <td>
                    <b>{{ number_format($data['sellCount'], 0) }}</b>
                </td>
                <td>
                    <b>{{ currency($data['sellPrice']) }}</b>
                </td>
                <td>
                    <b>{{ currency($data['totalPurchasePrice']) }}</b>
                </td>
                <td>
                    <b>{{ currency($data['totalProfitLoss']) }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
