@extends('admin.master_layout')
@section('title')
    <title>{{ __('All suppliers') }}</title>
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
                <h1>{{ __('All suppliers') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.suppliers.index') }}" method="GET" onchange="this.submit()"
                                    class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="{{ __('Search') }}">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="1" {{ request('order_by') == '1' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="0" {{ request('order_by') == '0' ? 'selected' : '' }}>
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
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="javascript:;" data-toggle="modal" data-target="#addSupplier"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Supplier') }}</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
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
                                            @foreach ($ledgers as $ledger)
                                                @php
                                                    $due += $ledger->due_amount;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $ledger->date }}</td>
                                                    <td>{{ $ledger->supplier->name }}</td>
                                                    <td>{{ $ledger->supplier->phone }}</td>
                                                    <td>{{ $ledger->invoice_type }}</td>
                                                    <td><a href="{{ $ledger->invoice_url }}">{{ $ledger->invoice_no }}</a>
                                                    </td>
                                                    <td>{{ $ledger->note }}</td>
                                                    <td>
                                                        @if (
                                                            $ledger->invoice_type == 'purchase' ||
                                                                $ledger->invoice_type == 'Due Payment' ||
                                                                $ledger->invoice_type == 'Advance Payment')
                                                            -
                                                        @endif
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
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
