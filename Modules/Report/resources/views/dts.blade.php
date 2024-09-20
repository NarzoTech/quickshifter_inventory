@extends('admin.master_layout')
@section('title')
    <title>{{ __('Daily Transaction Summary') }} - DTS</title>
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
                <h1>{{ __('Daily Transaction Summary') }} - DTS</h1>
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
                            <div class="card-header">
                                <h4>
                                    {{ __('DTS') }}
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Mode') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th>{{ __('Particular') }}</th>
                                                <th>{{ __('Revenue') . '/' . __('Received') . '/' . __('Credit') }}</th>
                                                <th>{{ __('Expense') . '/' . __('Paid') . '/' . __('Debit') }}</th>
                                                <th>{{ __('Balance') }}</th>
                                                <th>{{ __('IV Cost') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $dts)
                                                <tr>
                                                    <td>
                                                        {{ $dts->date }}
                                                    </td>
                                                    <td>
                                                        {{ $dts->mode }}
                                                    </td>
                                                    <td>
                                                        {{ $dts->category }}
                                                    </td>
                                                    <td>
                                                        {{ $dts->particular }}
                                                    </td>
                                                    <td>
                                                        {{ $dts->credit }}
                                                    </td>
                                                    <td>
                                                        {{ $dts->debit }}
                                                    </td>
                                                    <td>
                                                        {{ $openingBalance + $dts->credit - $dts->debit - $dts->iv }}
                                                    </td>
                                                    <td>
                                                        {{ $dts->iv }}
                                                    </td>
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
        </section>
    </div>
@endsection
