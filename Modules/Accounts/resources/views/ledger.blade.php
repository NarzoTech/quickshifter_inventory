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
                            <div class="col-xxl-3 col-md-6 col-lg-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="{{ __('Search') }}..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
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
                            <div class="col-xxl-4 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="{{ __('From Date') }}"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">{{ __('to') }}</span>
                                        <input type="text" placeholder="{{ __('To Date') }}" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-md-6 col-lg-4">
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
                <h4 class="section_title">{{ $title }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn bg-label-success export-excel">
                    <i class="fa fa-file-excel"></i> {{ __('Excel') }}
                </button>
                <button type="button" class="btn bg-label-warning export-pdf">
                    <i class="fa fa-file-pdf"></i> {{ __('PDF') }}
                </button>
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th class="text-end">{{ __('Debit') }} ({{ __('IN') }})</th>
                            <th class="text-end">{{ __('Credit') }} ({{ __('OUT') }})</th>
                            <th class="text-end">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $runningBalance = $openingBalance;
                        @endphp

                        @if($hasDateFilter)
                        <tr class="table-secondary">
                            <td colspan="6" class="text-center fw-bold">
                                {{ __('Opening Balance') }}
                            </td>
                            <td class="text-end fw-bold">{{ currency($openingBalance) }}</td>
                        </tr>
                        @endif

                        @forelse ($ledgers as $index => $transaction)
                            @php
                                $runningBalance += $transaction['debit'] - $transaction['credit'];
                            @endphp
                            <tr>
                                <td>{{ is_object($ledgers) && method_exists($ledgers, 'firstItem') ? $ledgers->firstItem() + $index : $index + 1 }}</td>
                                <td>{{ formatDate($transaction['date']) }}</td>
                                <td>{{ $transaction['description'] }}</td>
                                <td>
                                    @if(!empty($transaction['url']))
                                        <a href="{{ $transaction['url'] }}" target="_blank" class="text-primary">
                                            {{ $transaction['reference'] }}
                                            <i class="fas fa-external-link-alt fa-xs"></i>
                                        </a>
                                    @else
                                        {{ $transaction['reference'] }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($transaction['debit'] > 0)
                                        <span class="text-success">{{ currency($transaction['debit']) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($transaction['credit'] > 0)
                                        <span class="text-danger">{{ currency($transaction['credit']) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ currency($runningBalance) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-2 d-block"></i>
                                        {{ __('No transactions found') }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        @if(count($ledgers) > 0)
                        <tr class="table-primary">
                            <td colspan="4" class="text-center fw-bold">
                                {{ __('Total') }}
                            </td>
                            <td class="text-end fw-bold text-success">{{ currency($totalDebit) }}</td>
                            <td class="text-end fw-bold text-danger">{{ currency($totalCredit) }}</td>
                            <td class="text-end fw-bold">{{ currency($closingBalance) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if (is_object($ledgers) && method_exists($ledgers, 'links') && request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $ledgers->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
<script>
    'use strict';

    $(document).ready(function() {
        // Excel Export
        $(document).on('click', '.export-excel', function() {
            let url = new URL(window.location.href);
            url.searchParams.set('export', 'excel');
            window.location.href = url.toString();
        });

        // PDF Export
        $(document).on('click', '.export-pdf', function() {
            let url = new URL(window.location.href);
            url.searchParams.set('export', 'pdf');
            window.open(url.toString(), '_blank');
        });
    });
</script>
@endpush
