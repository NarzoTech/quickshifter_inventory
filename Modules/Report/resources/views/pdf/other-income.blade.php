@extends('admin.layouts.pdf-layout')

@section('title', __('Other Income List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Product Name'),
                    __('Sku'),
                    __('Brand Name'),
                    __('Sale Qty'),
                    __('Sale Return'),
                    __('Purchase Price'),
                    __('Sale Price'),
                    __('Total'),
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
            @php
                $data['quantity'] = 0;
                $data['sale_return'] = 0;
                $data['purchase_price'] = 0;
                $data['sale_price'] = 0;
                $data['total'] = 0;
            @endphp
            @foreach ($reports as $index => $report)
                @php
                    $data['quantity'] += $report->quantity;
                    $data['sale_return'] += $report->sale_return;
                    $data['purchase_price'] += $report->purchase_price;
                    $data['sale_price'] += $report->selling_price;
                    $data['total'] += $report->sub_total - $report->purchase_price * $report->quantity;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $report->product->name }}</td>
                    <td>{{ $report->product->sku }}</td>
                    <td>{{ $report->product->brand->name }}</td>
                    <td>{{ $report->quantity }}</td>
                    <td>{{ $report->sale_return }}</td>
                    <td>{{ $report->purchase_price }}</td>
                    <td>{{ $report->selling_price }}</td>
                    <td>{{ $report->sub_total - $report->purchase_price * $report->quantity }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td style="text-align: center; font-weight: bold" colspan="4">
                    <b>{{ __('Total') }}</b>
                </td>
                <td>
                    <b>{{ $data['quantity'] }}</b>
                </td>
                <td>
                    <b>{{ $data['sale_return'] }}</b>
                </td>
                <td>
                    <b>{{ $data['purchase_price'] }}</b>
                </td>
                <td>
                    <b>{{ $data['sale_price'] }}</b>
                </td>
                <td>
                    <b>{{ $data['total'] }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
