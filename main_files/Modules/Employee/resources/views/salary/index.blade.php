@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Paid Salary') }}</title>
@endsection


@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Employee Name: {{ $employee->name }}</h4>
        </div>
        <div class="card-body">
            <p>Salary: {{ currency($employee->salary) }}</p>
            <p>Payable Salary: {{ currency($payableSalary) }}</p>
            <p>Total Working Day: {{ $totalAttendance }} Days</p>
            <p>Total Weekend & Holiday: {{ $totalDayOff }} Days</p>
            <p>Phone: {{ $employee->phone }}</p>
            <p>Paid Amount: {{ currency($payments->sum('amount')) }}</p>
            <p>Payment Month: <b>{{ $month }}</b></p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Paid') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th>{{ __('Date') }}</th>
                            {{-- <th style="display: none;">Business Branch</th> --}}
                            <th>{{ __('Note') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($payments->count() > 0)
                            <tr>
                                <td>0</td>
                                <td>{{ currency(0) }}</td>
                                <td>{{ currency($employee->salary) }}</td>
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
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ currency($payment->amount) }}</td>
                                <td>{{ currency($employee->salary - $paidAmount) }}</td>
                                <td>{{ $payment->date }}</td>
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
                    <tfoot>
                        <tr>
                            <td class="font-weight-bold">{{ __('Total') }}</td>

                            <td class="font-weight-bold">{{ currency($payments->sum('amount')) }}</td>
                            <td class="font-weight-bold">
                                {{ currency($employee->salary - $payments->sum('amount')) }}</td>
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
