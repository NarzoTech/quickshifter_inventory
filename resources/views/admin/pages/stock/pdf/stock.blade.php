@extends('admin.layouts.pdf-layout')

@section('title', __('Stock List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Name'),
                    __('Barcode'),
                    __('Category'),
                    __('Avg P.P'),
                    __('L. P.P'),
                    __('Selling Price'),
                    __('In Quantity'),
                    __('Out Quantity'),
                    __('Stock'),
                    __('Stock P.P'),
                    __('Stock S.P'),
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
                    $stock = $product->stock < 0 ? 0 : $product->stock;
                    $selling_price = $product->selling_price ?? 0;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ $product->category->name ?? '' }}</td>
                    <td>{{ currency($product->avg_purchase_price) }}</td>
                    <td>{{ currency($product->last_purchase_price) }}</td>
                    <td>{{ currency($product->selling_price) }}</td>
                    <td>{{ $product->stockDetails->sum('in_quantity') }}</td>
                    <td>{{ $product->stockDetails->sum('out_quantity') }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ currency(remove_comma($stock) * remove_comma($product->avg_purchase_price)) }}</td>
                    <td>{{ currency(remove_comma($stock) * remove_comma($selling_price)) }}</td>
                </tr>
            @endforeach

            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="7" style="text-align: right; padding: 8px;">{{ __('Total') }}</td>
                <td style="padding: 8px;">{{ $totals['totalInQty'] ?? 0 }}</td>
                <td style="padding: 8px;">{{ $totals['totalOutQty'] ?? 0 }}</td>
                <td style="padding: 8px;">{{ $totals['totalStock'] ?? 0 }}</td>
                <td style="padding: 8px;">{{ currency($totals['totalStockPP'] ?? 0) }}</td>
                <td style="padding: 8px;">{{ currency($totals['totalStockSP'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
