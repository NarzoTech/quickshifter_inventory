@extends('admin.master_layout')
@section('title')
    <title>{{ __('Customer Other Due') }}</title>
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
                <h1>{{ __('Customer Other Due') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group search-wrapper">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="Product Name, SKU, Barcode...">
                                            <button type="submit">
                                                <i class="far fa-arrow-alt-circle-right"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="desc"
                                                    {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                                    {{ __('DESC') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="par-page" id="par-page" class="form-control">
                                                <option value="">{{ __('Per Page') }}</option>
                                                <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('10') }}
                                                </option>
                                                <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('50') }}
                                                </option>
                                                <option value="100"
                                                    {{ '100' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('100') }}
                                                </option>
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="From Date" name="from_date"
                                                value="{{ request()->get('from_date') }}" class="form-control datepicker">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="To Date" name="to_date"
                                                value="{{ request()->get('to_date') }}" class="form-control datepicker">
                                        </div>
                                    </div>
                                    {{-- excel  buttons --}}
                                    <div class="row">
                                        <div class="col-md-4 form-group mx-auto">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button type="button" class="btn btn-secondary export"><i
                                                        class="far fa-file-excel"></i>
                                                    Excel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>
                                    {{ __('Customer Other Due') }}
                                </h4>
                                <div>
                                    <a href="javascript:;" data-toggle="modal" data-target="#addCustomer"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Customer') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Sl') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Company') }}</th>
                                                <th>{{ __('Phone') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Paid') }}</th>
                                                <th>{{ __('Due') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($summeries as $summery)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $summery->customer->name }}</td>
                                                    <td>{{ $summery->customer->company }}</td>
                                                    <td>{{ $summery->customer->phone }}</td>
                                                    <td>{{ $summery->amount }}</td>
                                                    <td>{{ $summery->paid }}</td>
                                                    <td>{{ $summery->due }}</td>
                                                </tr>
                                            @endforeach

                                            @if ($summeries->count() > 0)
                                                <tr>
                                                    <td colspan="4" class="text-right">
                                                        Total
                                                    </td>
                                                    <td>
                                                        {{ $data['total_amount'] }}
                                                    </td>
                                                    <td>
                                                        {{ currency($data['total_paid']) }}
                                                    </td>
                                                    <td>
                                                        {{ currency($data['total_due']) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $summeries->onEachSide(0)->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>



    <div class="modal" id="addCustomer">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Customer Other Due') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.other-summery.customer.store') }}" method="POST" id="add-customer-due">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="customer_id">{{ __('Customer Name') }}</label>
                                <select name="customer_id" id="customer_id" class="form-control select2"
                                    data-control="select2" data-dropdown-parent="#addCustomer">
                                    <option value="">{{ __('Select Group') }}</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} -
                                            {{ $customer->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="date">{{ __('Date') }}</label>
                                <input type="text" class="form-control datepicker" id="date" name="date"
                                    value="{{ date('d-m-Y') }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="amount">{{ __('Total Amount') }}</label>
                                <input type="text" class="form-control" id="amount" name="amount">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="paid">{{ __('Paid') }}</label>
                                <input type="text" class="form-control" id="paid" name="paid">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="due">{{ __('Due') }}</label>
                                <input type="text" class="form-control" id="due" name="due">
                            </div>

                            <div class="form-group col-md-12">
                                <label for="description">{{ __('Description') }}</label>
                                <textarea name="description" id="description" class="form-control height-80px" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-customer-due">Save</button>
                </div>

            </div>
        </div>
    </div>
@endsection
