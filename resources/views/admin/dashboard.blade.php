@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Dashboard') }}</title>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Dashboard') }}</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-money-bill-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Customer Due') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ currency($data['customerDues']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-money-bill-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Supplier Due') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ currency($data['total_supplier_due']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Total Products') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $data['totalProducts'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-money-bill-alt"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Today Sales') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ currency($data['todaySales']) }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            "use strict";
            var alertKey = 'missingCrentialsAlert';
            var dismissedTimestamp = localStorage.getItem(alertKey);

            if (!dismissedTimestamp || Date.now() - dismissedTimestamp > 24 * 60 * 60 * 1000) {
                $('#missingCrentialsAlert').removeClass('d-none');
                $('#missingCrentialsAlert').show();
            } else {
                $('#missingCrentialsAlert').hide();
            }

            $('#missingCrentialsAlertClose').click(function() {
                $('#missingCrentialsAlert').hide();
                localStorage.setItem(alertKey, Date.now());
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            "use strict";
            var alertKey = 'updateAvailablityAlert';
            var dismissedTimestamp = localStorage.getItem(alertKey);

            if (!dismissedTimestamp || Date.now() - dismissedTimestamp > 24 * 60 * 60 * 1000) {
                $('#updateAvailablityAlert').removeClass('d-none');
                $('#updateAvailablityAlert').show();
            } else {
                $('#updateAvailablityAlert').hide();
            }

            $('#updateAvailablityAlertClose').click(function() {
                $('#updateAvailablityAlert').hide();
                localStorage.setItem(alertKey, Date.now());
            });
        });
    </script>
@endpush
