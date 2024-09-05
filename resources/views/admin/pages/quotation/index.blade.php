@extends('admin.master_layout')
@section('title')
    <title>{{ __('Current Stock') }}</title>
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
                <h1>{{ __('Stock') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" onchange="this.submit()" class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="{{ __('Search') }}">
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
                                                <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('100') }}
                                                </option>
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <a href="{{ route('admin.quotation.index') }}"
                                                class="btn btn-info">{{ __('Reset Form') }}</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Quotation Date</th>
                                                <th>Quotation No</th>
                                                <th>Customer</th>
                                                <th>Total Amount</th>
                                                <th style="display: none;">Business Branch</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($quotations as $key => $quotation)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ date('d-m-Y', strtotime($quotation->date)) }}</td>
                                                    <td>{{ $quotation->quotation_no }}</td>
                                                    <td>{{ $quotation->customer->name }}</td>
                                                    <td>{{ currency($quotation->total) }}</td>
                                                    <td style="display: none;">
                                                        {{-- {{ $quotation->business_branch->name }} --}}
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('admin.quotation.show', $quotation->id) }}"
                                                                class="btn btn-sm btn-info">{{ __('View') }}</a>

                                                            <a href="{{ route('admin.quotation.edit', $quotation->id) }}"
                                                                class="btn btn-sm btn-primary">{{ __('Edit') }}</a>

                                                            <a href="{{ route('admin.quotation.edit', $quotation->id) }}"
                                                                class="btn btn-sm btn-primary">{{ __('Sale') }}</a>

                                                            <a href="{{ route('admin.quotation.destroy', $quotation->id) }}"
                                                                class="btn btn-sm btn-danger">{{ __('Delete') }}</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $quotations->onEachSide(0)->links() }}
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
