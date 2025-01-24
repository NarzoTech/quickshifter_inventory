@extends('admin.layouts.pdf-layout')

@section('title', __('Customer Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Name'), __('Company'), __('Phone'), __('Total Sales'), __('Total'), __('Paid'), __('Due')];
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
                $totalSales = 0;
                $totalAmount = 0;
                $totalPaid = 0;
                $totalDue = 0;
            @endphp
            @foreach ($customers as $index => $customer)
                @php
                    $totalSales += $customer->sales->count();
                    $totalAmount += $customer->sales->sum('grand_total');
                    $totalPaid += $customer->total_paid;
                    $totalDue += $customer->total_due;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->company }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->sales->count() }}</td>
                    <td>{{ currency($customer->sales->sum('grand_total')) }}</td>

                    <td>{{ currency($customer->total_paid) }}</td>
                    <td>{{ currency($customer->total_due) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-end">
                    <b>Total</b>
                </td>
                <td>
                    <b>{{ $totalSales }}</b>
                </td>
                <td>
                    <b>{{ currency($totalAmount) }}</b>
                </td>
                <td>
                    <b>{{ currency($totalPaid) }}</b>
                </td>
                <td>
                    <b>{{ currency($totalDue) }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
