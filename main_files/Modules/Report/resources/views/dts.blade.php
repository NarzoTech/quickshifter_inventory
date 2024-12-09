@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Daily Transaction Summary') }} - DTS</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-1">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
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
                                        value="{{ request()->get('from_date') }}" class="form-control datepicker"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="To Date" name="to_date"
                                        value="{{ request()->get('to_date') }}" class="form-control datepicker"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-1 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-label-danger form-reset"><i
                                            class='bx bx-rotate-right'></i></button>

                                    <button type="submit" class="btn bg-label-primary"><i
                                            class='bx bx-search'></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"><i class="fas fa-list"></i> {{ __('DTS') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">

                <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
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
                        @php
                            $balance = 0;
                        @endphp
                        @foreach ($data as $index => $dts)
                            @php
                                if ($dts->mode != 'Credit' && $dts->category != 'Inventory') {
                                    $balance += $dts->credit - $dts->debit;
                                }
                                if (
                                    $dts->category == 'Inventory' &&
                                    ($dts->mode == 'R/P Credit' || $dts->mode == 'Cash')
                                ) {
                                    $balance += $dts->credit - $dts->debit;
                                }
                            @endphp
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
                                    {{ $balance }}
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
@endsection


@push('js')
    <script>
        $('.export').on('click', function() {
            // get full url including query string
            var fullUrl = window.location.href;
            if (fullUrl.includes('?')) {
                fullUrl += '&export=true';
            } else {
                fullUrl += '?export=true';
            }

            window.location.href = fullUrl;
        })
    </script>
@endpush
