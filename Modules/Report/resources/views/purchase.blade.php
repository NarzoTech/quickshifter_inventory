@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Purchase Report') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Purchase Report') }}</h1>
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
                                    Purchase Report
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Sl') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Invoice') }}</th>
                                                <th>{{ __('Supplier') }}</th>
                                                <th>{{ __('Purchased By') }}</th>
                                                <th>{{ __('Product (Qty)') }}</th>
                                                <th>{{ __('Invoice Qty') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Paid') }}</th>
                                                <th>{{ __('Due') }}</th>
                                                <th>{{ __('Payment Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchases as $purchase)
                                                <tr>
                                                    <td>
                                                        {{ $purchases->firstItem() + $loop->index }}
                                                    </td>
                                                    <td>
                                                        {{ now()->parse($purchase->purchase_date)->format('d-m-Y') }}
                                                    </td>
                                                    <td>
                                                        {{ $purchase->invoice_number }}
                                                    </td>
                                                    <td>
                                                        {{ $purchase->supplier->name ?? 'Guest' }}
                                                    </td>
                                                    <td>
                                                        {{ $purchase->createdBy->name }}
                                                    </td>
                                                    <td>
                                                        <table>
                                                            @foreach ($purchase->purchaseDetails as $key => $purchaseDetail)
                                                                <tr>
                                                                    <td>{{ $purchaseDetail->product->name }}</td>
                                                                    <td>{{ $purchaseDetail->quantity }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td>
                                                        {{ $purchase->purchaseDetails->sum('quantity') }}
                                                    </td>
                                                    <td>
                                                        {{ currency($purchase->total_amount) }}
                                                    </td>
                                                    <td>
                                                        {{ currency($purchase->paid_amount) }}
                                                    </td>
                                                    <td>
                                                        {{ currency($purchase->due_amount) }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $purchase->due_amount == 0 ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $purchase->due_amount == 0 ? 'Paid' : 'Due' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $purchases->onEachSide(0)->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
