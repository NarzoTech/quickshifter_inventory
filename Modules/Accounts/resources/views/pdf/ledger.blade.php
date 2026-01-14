@extends('admin.layouts.pdf-layout')

@section('title', $title)

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Date') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Description') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Reference') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: right;">{{ __('Debit') }} ({{ __('IN') }})</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: right;">{{ __('Credit') }} ({{ __('OUT') }})</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: right;">{{ __('Balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $runningBalance = $openingBalance;
            @endphp

            @if($hasDateFilter)
            <tr style="background-color: #f0f0f0;">
                <td colspan="6" style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;">
                    {{ __('Opening Balance') }}
                </td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">
                    {{ currency($openingBalance) }}
                </td>
            </tr>
            @endif

            @foreach ($transactions as $index => $transaction)
                @php
                    $runningBalance += $transaction['debit'] - $transaction['credit'];
                @endphp
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ formatDate($transaction['date']) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $transaction['description'] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $transaction['reference'] }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        @if($transaction['debit'] > 0)
                            {{ currency($transaction['debit']) }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        @if($transaction['credit'] > 0)
                            {{ currency($transaction['credit']) }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">
                        {{ currency($runningBalance) }}
                    </td>
                </tr>
            @endforeach

            <tr style="background-color: #e8f4fc;">
                <td colspan="4" style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;">
                    {{ __('Total') }}
                </td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; color: green;">
                    {{ currency($totalDebit) }}
                </td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; color: red;">
                    {{ currency($totalCredit) }}
                </td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">
                    {{ currency($closingBalance) }}
                </td>
            </tr>
        </tbody>
    </table>
@endsection
