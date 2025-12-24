@extends('admin.layouts.pdf-layout')

@section('title', __('Salary Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Employee Name'), __('Total Salary'), __('Total Paid Amount')];
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
                $totalSalary = 0;
                $totalPaidSalary = 0;
            @endphp
            @foreach ($employees as $index => $employee)
                @php
                    $totalSalary += $employee->total_salary;
                    $totalPaidSalary += $employee->paid_salary;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ currency($employee->total_salary) }}</td>
                    <td>{{ currency($employee->paid_salary) }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="2" style="text-align: right; padding: 8px;">{{ __('Total') }}</td>
                <td style="padding: 8px;">{{ currency($totalSalary) }}</td>
                <td style="padding: 8px;">{{ currency($totalPaidSalary) }}</td>
            </tr>

        </tbody>
    </table>
@endsection
