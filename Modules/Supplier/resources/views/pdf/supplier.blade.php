@extends('admin.layouts.pdf-layout')

@section('title', 'Supplier List')

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Name') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Phone') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Area') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Purchase Total') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Purchase Payment') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Total Due') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Advance') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Due Dismiss') }}</th>
            </tr>
        </thead>
        <tbody>

            @php
                $i = 1;
            @endphp

            @foreach ($suppliers as $index => $supplier)
                @php
                    $rawDue = $supplier->total_due;
                    $hasDateFilter = request()->from_date || request()->to_date;
                    $rawAdvance = $hasDateFilter ? $supplier->true_advance : $supplier->advance;
                    $offset = min(max(0, $rawDue), max(0, $rawAdvance));
                    $effectiveDue = $rawDue - $offset;
                    $effectiveAdvance = $rawAdvance - $offset;
                @endphp
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $i++ }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $supplier->name }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $supplier->phone }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $supplier->area->name }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        {{ currency($supplier->purchases->sum('total_amount')) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($supplier->payments->whereIn('payment_type', ['purchase', 'due_pay', 'advance_deduct'])->sum('amount') + $offset) }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($effectiveDue) }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($effectiveAdvance) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($supplier->total_due_dismiss) }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4" class="text-right font-weight-bold" style="border: 1px solid #ccc; padding: 8px;">
                    {{ __('Total') }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['totalPurchase']) }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['pay']) }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['total_due']) }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['total_advance']) }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['total_due_dismiss']) }}
                </td>
            </tr>
        </tbody>
    </table>
@endsection
