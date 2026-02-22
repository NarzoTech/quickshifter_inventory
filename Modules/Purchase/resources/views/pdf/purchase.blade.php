@extends('admin.layouts.pdf-layout')

@section('title', __('Purchase List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Date'),
                    __('Invoice Number'),
                    __('Supplier'),
                    __('Total Amount'),
                    __('Total Pay'),
                    __('Total Due'),
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
                $data['total_amount'] = 0;
                $data['paid_amount'] = 0;
                $data['due_amount'] = 0;
                $supplierAdvTracker = [];
            @endphp
            @foreach ($purchases as $index => $purchase)
                @php
                    $sid = $purchase->supplier_id;
                    if (!isset($supplierAdvTracker[$sid])) {
                        $supplierAdvTracker[$sid] = $purchase->supplier->advance ?? 0;
                    }
                    $offset = min(max(0, $purchase->due_amount), max(0, $supplierAdvTracker[$sid]));
                    $supplierAdvTracker[$sid] -= $offset;

                    $data['total_amount'] += $purchase->total_amount;
                    $data['paid_amount'] += $purchase->paid_amount + $offset;
                    $data['due_amount'] += $purchase->due_amount - $offset;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ formatDate($purchase->purchase_date) }}</td>
                    <td>{{ $purchase->invoice_number }}</td>
                    <td>{{ $purchase->supplier?->name }}</td>
                    <td>{{ currency($purchase->total_amount) }}</td>
                    <td>{{ currency($purchase->paid_amount + $offset) }}</td>
                    <td>{{ currency($purchase->due_amount - $offset) }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4" class="text-center">
                    <b> {{ __('Total') }}</b>
                </td>
                <td colspan="1">
                    <b>{{ currency($data['total_amount']) }}</b>
                </td>
                <td colspan="1">
                    <b>{{ currency($data['paid_amount']) }}</b>
                </td>
                <td colspan="1">
                    <b>{{ currency($data['due_amount']) }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
