@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Advance List') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form " action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search...">
                                    <button type="submit">
                                        <i class="bx bx-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <select name="payment_type" class="form-control">
                                        <option value="">{{ __('All Types') }}</option>
                                        <option value="advance_pay"
                                            {{ request('payment_type') == 'advance_pay' ? 'selected' : '' }}>
                                            {{ __('Advance Pay') }}
                                        </option>
                                        <option value="advance_refund"
                                            {{ request('payment_type') == 'advance_refund' ? 'selected' : '' }}>
                                            {{ __('Advance Refund') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <select name="order_by" id="order_by" class="form-control">
                                        <option value="">{{ __('Order By') }}</option>
                                        <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                            {{ __('ASC') }}
                                        </option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                            {{ __('DESC') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-1 col-md-4">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('10') }}
                                        </option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('50') }}
                                        </option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('100') }}
                                        </option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('All') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">{{ __('to') }}</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-5 mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> {{ __('Supplier Advance List') }}</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table common_table">
                    <thead>
                        <tr>
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Payment Method') }}</th>
                            <th>{{ __('Created By') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ formatDate($payment->payment_date) }}</td>
                                <td>
                                    @if ($payment->ledger_id)
                                        <a href="{{ route('admin.suppliers.ledger-details', $payment->ledger_id) }}">
                                            {{ $payment->invoice }}
                                        </a>
                                    @else
                                        {{ $payment->invoice }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.suppliers.ledger', $payment->supplier_id) }}">
                                        {{ $payment->supplier->name }}
                                    </a>
                                </td>
                                <td>
                                    @if ($payment->payment_type == 'advance_pay')
                                        <span class="badge bg-success">{{ __('Pay') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Refund') }}</span>
                                    @endif
                                </td>
                                <td>{{ currency($payment->amount) }}</td>
                                <td>{{ ucfirst($payment->account_type) }}</td>
                                <td>{{ $payment->createdBy->name ?? '' }}</td>
                            </tr>
                        @endforeach
                        @if ($payments->count() > 0)
                            <tr>
                                <td colspan="5" class="text-end fw-bold">
                                    {{ __('Total Pay') }}
                                </td>
                                <td colspan="3" class="fw-bold text-success">
                                    {{ currency($data['total_pay']) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">
                                    {{ __('Total Refund') }}
                                </td>
                                <td colspan="3" class="fw-bold text-danger">
                                    {{ currency($data['total_refund']) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">
                                    {{ __('Net Advance') }}
                                </td>
                                <td colspan="3" class="fw-bold">
                                    {{ currency($data['total']) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $payments->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
