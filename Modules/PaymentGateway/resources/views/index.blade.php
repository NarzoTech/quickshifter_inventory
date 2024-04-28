@extends('admin.master_layout')
@section('title')
    <title>{{ __('Payment Gateway') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.settings') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ __('Payment Gateway') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.settings') }}">{{ __('Settings') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Payment Gateway') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills flex-column" id="paymentGatewayTab" role="tablist">
                                    @include('paymentgateway::tabs.navbar')
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="tab-content" id="myTabContent4">
                                    @include('paymentgateway::sections.razorpay')
                                    @include('paymentgateway::sections.flutterwave')
                                    @include('paymentgateway::sections.paystack')
                                    @include('paymentgateway::sections.mollie')
                                    @include('paymentgateway::sections.instamojo')
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
        $(document).ready(function() {
            "use strict";
            var activeTab = localStorage.getItem("activeTab");
            if (activeTab) {
                $('#paymentGatewayTab a[href="#' + activeTab + '"]').tab("show");
            } else {
                $("#paymentGatewayTab a:first").tab("show");
            }

            $('a[data-toggle="tab"]').on("shown.bs.tab", function(e) {
                var newTab = $(e.target).attr("href").substring(1);
                localStorage.setItem("activeTab", newTab);
            });
        });
    </script>
    <script>
        //input image preview function
        "use strict";
        $(document).ready(function() {
            setupImagePreview('razorpay_img_input', 'razorpay_img_preview');
            setupImagePreview('flutterwave_img_input', 'flutterwave_img_preview');
            setupImagePreview('paystack_img_input', 'paystack_img_preview');
            setupImagePreview('mollie_img_input', 'mollie_img_preview');
            setupImagePreview('instamojo_img_input', 'instamojo_img_preview');
        });
    </script>
@endpush
