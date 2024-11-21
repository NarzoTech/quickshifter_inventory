@extends('admin.master_layout')
@section('title')
    <title>{{ __('Paid Salary') }}</title>
@endsection

@push('css')
    <style>
        thead tr:nth-child(odd) {
            background-color: lightskyblue;

        }


        thead tr:nth-child(even) {
            background-color: lightpink;
        }

        thead>tr>th {
            /* background-color: lightseagreen; */
            color: white !important;
        }
    </style>
@endpush
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Employee Paid Salary List') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Employee Name: {{ $employee->name }}</h4>
                            </div>
                            <div class="card-body">
                                <p>Salary: {{ currency($employee->salary) }}</p>
                                <p>Phone: {{ $employee->phone }}</p>
                                <p>Paid Amount: {{ currency($payments->sum('amount')) }}</p>
                                <p>Payment Month: <b>{{ $month }}</b></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body"></div>
                            <div class="table-responsive">
                                <table class="table table-striped">
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
                                        <tr>
                                            <td>0</td>
                                            <td>{{ currency(0) }}</td>
                                            <td>{{ currency($employee->salary) }}</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        @php
                                            $paidAmount = 0;
                                        @endphp
                                        @forelse ($payments as $index => $payment)
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
                                                            class="btn btn-primary btn-sm mr-2"><i
                                                                class="fa fa-edit"></i></a>
                                                        <a href="javascript:;" class="btn btn-danger btn-sm"
                                                            onclick="deleteData({{ $payment->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <x-empty-table :name="__('Bank')" route="" create="no" :message="__('No data found!')"
                                                colspan="10"></x-empty-table>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
