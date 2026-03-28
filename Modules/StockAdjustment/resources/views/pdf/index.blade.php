@extends('admin.layouts.pdf-layout')

@section('title', __('Stock Adjustments'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Date'),
                    __('Invoice'),
                    __('Product'),
                    __('Qty'),
                    __('Reason'),
                    __('Unit Cost'),
                    __('Total Loss'),
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
            @foreach ($lists as $index => $adjustment)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 6px;">{{ ++$index }}</td>
                    <td style="border: 1px solid #ddd; padding: 6px;">{{ formatDate($adjustment->date) }}</td>
                    <td style="border: 1px solid #ddd; padding: 6px;">{{ $adjustment->invoice }}</td>
                    <td style="border: 1px solid #ddd; padding: 6px;">{{ $adjustment->product->name ?? '-' }}</td>
                    <td style="border: 1px solid #ddd; padding: 6px;">{{ $adjustment->quantity }}</td>
                    <td style="border: 1px solid #ddd; padding: 6px;">{{ ucfirst($adjustment->reason) }}</td>
                    <td style="border: 1px solid #ddd; padding: 6px;">{{ currency($adjustment->unit_cost) }}</td>
                    <td style="border: 1px solid #ddd; padding: 6px;">{{ currency($adjustment->total_loss) }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4" class="text-center" style="border: 1px solid #ddd; padding: 8px;">
                    <b>{{ __('Total') }}</b>
                </td>
                <td style="border: 1px solid #ddd; padding: 8px;">
                    <b>{{ $data['totalQuantity'] }}</b>
                </td>
                <td style="border: 1px solid #ddd; padding: 8px;"></td>
                <td style="border: 1px solid #ddd; padding: 8px;"></td>
                <td style="border: 1px solid #ddd; padding: 8px;">
                    <b>{{ currency($data['totalLoss']) }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
