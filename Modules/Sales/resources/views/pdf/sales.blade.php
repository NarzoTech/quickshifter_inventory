@extends('admin.layouts.pdf-layout')

@section('title', __('Sales List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Date'),
                    __('Invoice No'),
                    __('Customer'),
                    __('Remark'),
                    __('Sale Amount'),
                    __('Total Amount'),
                    __('Paid Amount'),
                    __('Due'),
                    __('Payment Status'),
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
                $data['sale_amount'] = 0;
                $data['total_amount'] = 0;
                $data['paid_amount'] = 0;
                $data['due_amount'] = 0;
            @endphp
            @foreach ($sales as $index => $sale)
                @php
                    $data['sale_amount'] += $sale->total_price;
                    $data['total_amount'] += $sale->grand_total;
                    $data['paid_amount'] += $sale->paid_amount;
                    $data['due_amount'] += $sale->due_amount;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $sale->order_date->format('d-m-y') }}</td>
                    <td>{{ $sale->invoice }}</td>
                    <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                    <td>{{ $sale->sale_note }}</td>
                    <td>{{ $sale->total_price }}</td>
                    <td>{{ $sale->grand_total }}</td>
                    <td>{{ $sale->paid_amount }}</td>
                    <td>{{ $sale->due_amount }}</td>
                    <td>
                        @if ($sale->paid_amount == $sale->grand_total)
                            <span class="badge badge-success">{{ __('Paid') }}</span>
                        @elseif ($sale->due_amount > 0 && $sale->paid_amount < $sale->total_price)
                            <span class="badge badge-danger">{{ __('Partial Due') }}</span>
                        @else
                            <span class="badge badge-danger">{{ __('Due') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-center">
                    <b> {{ __('Total') }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['sale_amount'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['total_amount'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['paid_amount'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['due_amount'] }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
