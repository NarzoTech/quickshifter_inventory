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
                                        <div class="col-md-3 form-group">
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
                                        <div class="col-md-3 form-group">
                                            <input type="text" placeholder="From Date" name="from_date"
                                                value="{{ request()->get('from_date') }}" class="form-control datepicker">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <input type="text" placeholder="To Date" name="to_date"
                                                value="{{ request()->get('to_date') }}" class="form-control datepicker">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <button type="submit" class="btn btn-primary w-100"><i
                                                    class="fas fa-search"></i></button>
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
                    </div>
                </div>
            </div>
        </section>
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
