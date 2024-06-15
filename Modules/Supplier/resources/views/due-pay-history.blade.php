@extends('admin.master_layout')
@section('title')
    <title>{{ __('Supplier Due Pay') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Supplier Due Pay') }}</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.purchase.index') }}">{{ __('Supplier Due Pay') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Supplier Due Pay') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="">{{ __('Supplier Due Pay') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        {{ __('SL')}}
                                                    </th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Invoice No') }}</th>
                                                    <th>{{ __('Supplier') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Paid By') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($payments as $payment)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ now()->parse($payment->payment_date)->format('d M , Y') }}</td>
                                                        <td>{{ $payment->purchase?->invoice_number }}</td>
                                                        <td>{{ $payment->supplier->name }}</td>
                                                        <td>{{ $payment->amount }}</td>
                                                        <td>{{ $payment->createdBy->name }}</td>
                                                    </tr>
                                                    
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
