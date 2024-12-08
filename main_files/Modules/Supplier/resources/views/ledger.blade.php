@extends('admin.layouts.master')
@section('title')
    <title>{{ $title }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-1">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search...">
                                    <button type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
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
                            <div class="col-xxl-2 col-md-4">
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
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="From Date" name="from_date"
                                        value="{{ request()->get('from_date') }}" class="form-control datepicker">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="To Date" name="to_date"
                                        value="{{ request()->get('to_date') }}" class="form-control datepicker">
                                </div>
                            </div>
                            <div class="col-xxl-1 col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="card mt-3 mb-3">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4><i class="fas fa-list"></i> Supplier Ledger</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn btn-primary export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn btn-success export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-hover">
                    <thead>
                        <tr>
                            <th title="Sl">{{ __('Sl') }}</th>
                            <th title="Date">{{ __('Date') }}</th>
                            <th title="Name">{{ __('Name') }}</th>
                            <th title="Mobile">{{ __('Mobile') }}</th>
                            <th title="Type">{{ __('Type') }}</th>
                            <th title="Invoice No">{{ __('Invoice No') }}</th>
                            <th title="Note">{{ __('Note') }}</th>
                            <th title="Amount">{{ __('Amount') }}</th>
                            <th title="Due">{{ __('Due') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $due = 0;
                        @endphp
                        @foreach ($ledgers as $index => $ledger)
                            @php
                                $due += $ledger->amount;
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ledger->date }}</td>
                                <td>
                                    {{ $ledger->supplier->name ?? $ledger->customer->name }}

                                </td>
                                <td>{{ $ledger->supplier->phone ?? $ledger->customer->phone }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $ledger->invoice_type)) }}</td>
                                <td><a href="{{ $ledger->invoice_url }}">{{ $ledger->invoice_no }}</a>
                                </td>
                                <td>{{ $ledger->note }}</td>

                                <td>
                                    {{-- @if ($ledger->supplier_id && ($ledger->invoice_type == 'purchase' || $ledger->invoice_type == 'Due Payment' || $ledger->invoice_type == 'Advance Payment' || $ledger->invoice_type == 'purchase_return'))
                                        -
                                    @endif --}}

                                    {{-- @if ($ledger->customer_id && $ledger->invoice_type == 'Sale Return')
                                        -
                                    @endif --}}

                                    {{-- @if ($ledger->invoice_type == 'purchase_return')
                                        @php
                                            $due -= $ledger->amount;
                                        @endphp
                                    @endif --}}
                                    {{ currency($ledger->amount) }}
                                </td>
                                <td>{{ currency($due) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $ledgers->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
