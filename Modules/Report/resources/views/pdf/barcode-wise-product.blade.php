@extends('admin.layouts.pdf-layout')

@section('title', __('Barcode Wise Product Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Product Name'),
                    __('Attribute'),
                    __('Barcode'),
                    __('Brand Name'),
                    __('Sale'),
                    __('Sale Return'),
                    __('Purchase'),
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
                    <td>{{ ++$index }}</td>
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
            <tr style="font-weight: bold;">
                <td colspan="5" style="text-align: right;">{{ __('Total') }}</td>
                <td>{{ currency($data['totalSalePrice']) }}({{ $data['totalSaleQty'] }})</td>
                <td>{{ currency($data['totalReturnPrice']) }}({{ $data['totalReturnQty'] }})</td>
                <td>{{ currency($data['totalPurchasePrice']) }}({{ $data['totalPurchaseQty'] }})</td>
            </tr>
        </tbody>
    </table>
@endsection
