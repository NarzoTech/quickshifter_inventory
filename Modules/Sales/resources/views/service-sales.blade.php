@extends('admin.layouts.master')
@section('title')
    <title>{{ $title }}</title>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
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
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control select2" name="service_id">
                                        <option value="">{{ __('Service') }}</option>
                                        @foreach ($servicesList as $service)
                                            <option value="{{ $service->id }}"
                                                {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control select2" name="customer">
                                        <option value="">{{ __('Customer') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn bg-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Service Sales List') }}</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Service') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sn = 0; @endphp
                        @foreach ($sales as $sale)
                            @foreach ($sale->services as $serviceSale)
                                @php $sn++; @endphp
                                <tr>
                                    <td>{{ $sn }}</td>
                                    <td>{{ formatDate($sale->order_date) }}</td>
                                    <td>{{ $sale->invoice }}</td>
                                    <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                                    <td>{{ $serviceSale->service->name ?? '' }}</td>
                                    <td>{{ $serviceSale->quantity }}</td>
                                    <td>{{ currency($serviceSale->price) }}</td>
                                    <td>{{ currency($serviceSale->sub_total) }}</td>
                                    <td>
                                        @if (checkAdminHasPermission('sales.view') || checkAdminHasPermission('sales.invoice'))
                                            <div class="btn-group">
                                                @adminCan('sales.view')
                                                    <a class="btn btn-sm btn-info view-sale" href="javascript:;"
                                                        data-id="{{ $sale->id }}"><i class="fas fa-eye"></i></a>
                                                @endadminCan
                                                @adminCan('sales.invoice')
                                                    <a class="btn btn-sm btn-primary"
                                                        href="{{ route('admin.sales.invoice', $sale->id) }}"><i class="fas fa-file-invoice"></i></a>
                                                @endadminCan
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        @if ($sn > 0)
                            <tr>
                                <td colspan="5" class="text-center">
                                    <b>{{ __('Total') }}</b>
                                </td>
                                <td colspan="1">
                                    <b>{{ $data['service_qty'] }}</b>
                                </td>
                                <td></td>
                                <td colspan="1">
                                    <b>{{ currency($data['service_amount']) }}</b>
                                </td>
                                <td></td>
                            </tr>
                        @endif
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
    @include('components.admin.preloader')

    <div class="modal fade bd-example-modal-xl" id="salemodal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent" style="width: 100%">
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        'use strict'

        $(document).ready(function() {
            $(document).on('click', '.view-sale', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.sales.show', '') }}/" + id,
                    success: function(data) {
                        $('#modalcontent').html(data);
                        $('#salemodal').modal('show');
                    }
                });
            })
        })
    </script>
@endpush
