@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Dashboard') }}</title>
@endsection
@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card dashboard_card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                <div class="avatar flex-shrink-0">
                                    <i class='bx bx-user-plus'></i>
                                </div>
                            </div>
                            <h5 class="mb-1">{{ __('Customer Due') }}</h5>
                            <h4 class="card-title text-primary fw-medium"> {{ currency($data['customerDues']) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card dashboard_card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                <div class="avatar flex-shrink-0">
                                    <i class='bx bx-user-minus'></i>
                                </div>
                            </div>
                            <h5 class="mb-1">{{ __('Supplier Due') }}</h5>
                            <h4 class="card-title text-primary fw-medium"> {{ currency($data['total_supplier_due']) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card dashboard_card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                <div class="avatar flex-shrink-0">
                                    <i class='bx bx-list-ul'></i>
                                </div>
                            </div>
                            <h5 class="mb-1">{{ __('Total Products') }}</h5>
                            <h4 class="card-title text-primary fw-medium"> {{ $data['totalProducts'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card dashboard_card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                <div class="avatar flex-shrink-0">
                                    <i class='bx bx-basket'></i>
                                </div>
                            </div>
                            <h5 class="mb-1">{{ __('Today Sales') }}</h5>
                            <h4 class="card-title text-primary fw-medium"> {{ currency($data['todaySales']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
