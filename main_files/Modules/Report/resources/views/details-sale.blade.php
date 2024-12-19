@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Details Sales Report') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                {{-- Search filter --}}
                <div class="card">
                    <div class="card-body pb-0">
                        <form class="search_form" action="" method="GET">
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group search-wrapper">
                                        <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                            class="form-control" placeholder="Product Name, SKU, Barcode...">
                                        <button type="submit">
                                            <i class='bx bx-search'></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6">
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
                                <div class="col-lg-2 col-md-6">
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
                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                            <input type="text" id="dateRangePicker" placeholder="From Date"
                                                class="form-control datepicker" name="from_date"
                                                value="{{ request()->get('from_date') }}" autocomplete="off">
                                            <span class="input-group-text">to</span>
                                            <input type="text" placeholder="To Date" class="form-control datepicker"
                                                name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                {{-- excel  buttons --}}
                                <div class="col-lg-1">
                                    <div class="form-group">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <button type="button" class="btn btn-primary export"><i
                                                    class="far fa-file-excel me-2"></i>
                                                Excel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-5">
                    <div class="card-header">
                        <h4 class="section_title">Report List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">{{ __('Sl') }}</th>
                                        <th rowspan="2">{{ __('Date') }}</th>
                                        <th rowspan="2">{{ __('Invoice') }}</th>
                                        <th rowspan="2">{{ __('Customer') }}</th>
                                        <th rowspan="2">{{ __('Total ') }}</th>
                                        <th rowspan="2">{{ __('Paid') }}</th>
                                        <th rowspan="2">{{ __('Paid By') }}</th>
                                        <th rowspan="2">{{ __('Due') }}</th>
                                        <th rowspan="2">{{ __('Return Amount') }}</th>
                                        <th rowspan="2">{{ __('Payment Status') }}</th>
                                        <th rowspan="2">{{ __('Remark') }}</th>
                                        <th colspan="5">{{ __('Product Details Info') }}</th>
                                    </tr>
                                    <tr>
                                        <th>
                                            {{ __('Product Name') }}
                                        </th>
                                        <th>
                                            {{ __('Selling Qty') }}
                                        </th>

                                        <th>
                                            {{ __('Return Qty') }}
                                        </th>
                                        <th>
                                            {{ __('Selling Price') }}
                                        </th>
                                        <th>
                                            {{ __('Profit/Loss') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $key => $sale)
                                        <tr>
                                            <td>{{ $sales->firstItem() + $key }}</td>
                                            <td>{{ $sale->order_date->format('d-m-Y') }}</td>
                                            <td>{{ $sale->invoice }}</td>
                                            <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                                            <td>
                                                {{ $sale->grand_total }}
                                            </td>
                                            <td>
                                                {{ $sale->paid_amount }}
                                            </td>
                                            <td>
                                                @foreach ($sale->payment as $payment)
                                                    {{ $payment->account->account_type }} :
                                                    {{ $payment->amount }}
                                                    <br>
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ $sale->due_amount }}
                                            </td>
                                            <td>
                                                {{ $sale->saleReturns->sum('return_amount') }}
                                            </td>
                                            <td>
                                                {{ $sale->due_amount == 0 ? 'Paid' : 'Due' }}
                                            </td>
                                            <td>
                                                {{ $sale->sale_note }}
                                            </td>
                                            <td colspan="5">
                                                <table>
                                                    <tbody>
                                                        @foreach ($sale->products as $details)
                                                            <tr>
                                                                <td>
                                                                    {{ $details->product->name }}
                                                                </td>
                                                                <td>
                                                                    {{ $details->quantity }}
                                                                </td>
                                                                <td>
                                                                    {{ $sale->saleReturnDetails->where('product_id', $details->product_id)->sum('quantity') }}
                                                                </td>
                                                                <td>
                                                                    {{ $details->price }}
                                                                </td>
                                                                <td>
                                                                </td>
                                                                <br>
                                                            </tr>
                                                        @endforeach

                                                        @foreach ($sale->services as $service)
                                                            <tr>
                                                                <td>
                                                                    {{ $service->service->category->name == 'Wash' ? 'Wash' : $service->service->category->name }}
                                                                    {{ $service->service->name }}
                                                                </td>
                                                                <td>
                                                                    {{ $service->quantity }}
                                                                </td>
                                                                <td>
                                                                    0
                                                                </td>
                                                                <td>
                                                                    {{ $service->price }}
                                                                </td>
                                                                <td>
                                                                </td>
                                                                <br>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if (request()->get('par-page') !== 'all')
                            <div class="float-right">
                                {{ $sales->onEachSide(0)->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
