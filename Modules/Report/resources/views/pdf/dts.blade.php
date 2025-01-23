@extends('admin.layouts.pdf-layout')

@section('title', __('Daily Transaction Summary List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Date'),
                    __('Mode'),
                    __('Category'),
                    __('Particular'),
                    __('Revenue') . '/' . __('Received') . '/' . __('Credit'),
                    __('Expense') . '/' . __('Paid') . '/' . __('Debit'),
                    __('Balance'),
                    __('IV Cost'),
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
                $balance = 0;
                $accountList = array_values(accountList());
            @endphp
            @foreach ($data as $index => $dts)
                @php
                    if ($dts->mode != 'Credit' && $dts->category != 'Inventory') {
                        $balance += $dts->credit - $dts->debit;
                    }
                    if (
                        $dts->category == 'Inventory' &&
                        ($dts->mode == 'R/P Credit' || in_array($dts->mode, $accountList))
                    ) {
                        $balance += $dts->credit - $dts->debit;
                    }
                @endphp
                <tr>
                    <td>
                        {{ $dts->date }}
                    </td>
                    <td>
                        {{ $dts->mode }}
                    </td>
                    <td>
                        {{ $dts->category }}
                    </td>
                    <td>
                        {{ $dts->particular }}
                    </td>
                    <td>
                        {{ $dts->credit }}
                    </td>
                    <td>
                        {{ $dts->debit }}
                    </td>
                    <td>
                        {{ $balance }}
                    </td>
                    <td>
                        {{ $dts->iv }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
