@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Paid Salary') }}</title>
@endsection


@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="section_title">{{ __('Employee Name') }}: {{ $employee->name }}</h4>
        </div>
        <div class="card-body">
            <p><b class="me-2">{{ __('Salary') }}:</b> {{ currency($employee->salary) }}</p>
            <p><b class="me-2">{{ __('Payable Salary') }}:</b> {{ currency($payableSalary) }}</p>
            @if($carryForward > 0)
            <p class="text-danger"><b class="me-2">{{ __('Previous Month Advance (Carry Forward)') }}:</b> {{ currency($carryForward) }}</p>
            <p class="text-success"><b class="me-2">{{ __('Effective Payable (After Deduction)') }}:</b> {{ currency($effectivePayable) }}</p>
            @endif
            <p><b class="me-2">{{ __('Total Working Day & Weekend') }}:</b> {{ $totalAttendance }} {{ __('Days') }}</p>
            <p><b class="me-2">{{ __('Total Holiday') }}:</b> {{ $totalDayOff }} {{ __('Days') }}</p>
            <p><b class="me-2">{{ __('Phone') }}:</b> {{ $employee->mobile }}</p>
            <p><b class="me-2">{{ __('Paid Amount') }}:</b> {{ currency($payments->sum('amount')) }}</p>
            <p><b class="me-2">{{ __('Payment Month') }}:</b> {{ $month }}</p>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Payment Details') }}</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table common_table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Paid') }}</th>
                            <th>{{ __('Due / Advance') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Note') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($payments->count() > 0)
                            <tr>
                                <td>0</td>
                                <td>{{ currency(0) }}</td>
                                <td class="text-success">{{ currency($effectivePayable) }} {{ __('Due') }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        @endif
                        @php
                            $paidAmount = 0;
                        @endphp
                        @foreach ($payments as $index => $payment)
                            @php
                                $paidAmount += $payment->amount;
                                $remaining = $effectivePayable - $paidAmount;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ currency($payment->amount) }}</td>
                                <td>
                                    @if($remaining >= 0)
                                        <span class="text-success">{{ currency($remaining) }} {{ __('Due') }}</span>
                                    @else
                                        <span class="text-danger">{{ currency(abs($remaining)) }} {{ __('Advance (Carry to Next Month)') }}</span>
                                    @endif
                                </td>
                                <td>{{ formatDate($payment->date) }}</td>
                                <td>{{ $payment->note }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.employee.salary.edit', $payment->id) }}"
                                            class="btn btn-primary btn-sm me-2"><i class="fa fa-edit"></i></a>
                                        <a href="javascript:;" class="btn btn-danger btn-sm"
                                            onclick="deleteData({{ $payment->id }})">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    @php
                        $totalRemaining = $effectivePayable - $payments->sum('amount');
                    @endphp
                    <tfoot>
                        <tr>
                            <td class="font-weight-bold">{{ __('Total') }}</td>
                            <td class="font-weight-bold">{{ currency($payments->sum('amount')) }}</td>
                            <td class="font-weight-bold">
                                @if($totalRemaining >= 0)
                                    <span class="text-success">{{ currency($totalRemaining) }} {{ __('Due') }}</span>
                                @else
                                    <span class="text-danger">{{ currency(abs($totalRemaining)) }} {{ __('Advance (Carry to Next Month)') }}</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        function deleteData(id) {
            let url = "{{ route('admin.employee.salary.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
