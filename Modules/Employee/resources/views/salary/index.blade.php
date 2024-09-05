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
                                        @forelse ($payments as $index => $payment)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ currency($payment->amount) }}</td>
                                                <td>{{ currency($employee->salary - $payment->amount) }}</td>
                                                <td>{{ $payment->date }}</td>
                                                <td>{{ $payment->note }}</td>
                                                <td>
                                                    <a href="{{ route('admin.employee.salary.edit', $payment->id) }}"
                                                        class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
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
