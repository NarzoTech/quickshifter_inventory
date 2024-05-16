@extends('admin.master_layout')
@section('title')
    <title>{{ $title }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Invoice No') }}</th>
                                                <th>{{ __('Customer') }}</th>
                                                <th>{{ __('Sale By') }}</th>
                                                <th>{{ __('Sale Amount') }}</th>
                                                <th>{{ __('Total Amount') }}</th>
                                                <th>{{ __('Paid Amount') }}</th>
                                                <th>{{ __('Due') }}</th>
                                                <th>{{ __('Payment Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sales as $key => $sale)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $sale->order_date }}</td>
                                                    <td>{{ $sale->invoice }}</td>
                                                    <td>{{ $sale?->customer?->name }}</td>
                                                    <td>{{ $sale->user->name }}</td>
                                                    <td>{{ $sale->products->sum('sub_total') }}</td>
                                                    <td>{{ $sale->grand_total }}</td>
                                                    <td>{{ $sale->paid_amount }}</td>
                                                    <td>{{ $sale->grand_total - $sale->paid_amount }}</td>
                                                    <td>
                                                        @if ($sale->payment_status == 'paid')
                                                            <span class="badge badge-success">{{ $sale->payment_status }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $sale->payment_status }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('admin/order-view/' . $sale->id) }}"
                                                            class="btn btn-primary">View</a>
                                                        <a href="{{ url('admin/order-edit/' . $sale->id) }}"
                                                            class="btn btn-info">Edit</a>
                                                        <a href="javascript:void(0)" class="btn btn-danger"
                                                            onclick="deleteData({{ $sale->id }})">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('components.admin.preloader')

    @push('js')
        <script>
            'use strict'

            $(document).ready(function() {})

            function deleteData(id) {
                const modal = $('#deleteModal');
                $('#deleteForm').attr('action', "{{ url('admin/order-delete') }}/" + id);
                modal.modal('show');
            }
        </script>
    @endpush
@endsection
