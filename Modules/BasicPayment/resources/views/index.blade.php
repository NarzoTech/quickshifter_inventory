@extends('admin.master_layout')
@section('title')
    <title>{{ __('Basic Payment') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.settings') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ __('Basic Payment') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.settings') }}">{{ __('Settings') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Basic Payment') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills flex-column" id="basicPaymentTab" role="tablist">
                                    @include('basicpayment::tabs.navbar')
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="tab-content" id="myTabContent4">
                                    @include('basicpayment::sections.stripe')
                                    @include('basicpayment::sections.paypal')
                                    @include('basicpayment::sections.direct-bank')
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
                $('#basicPaymentTab a[href="#' + activeTab + '"]').tab("show");
            } else {
                $("#basicPaymentTab a:first").tab("show");
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
            setupImagePreview('stripe_img_input', 'stripe_img_preview');
            setupImagePreview('paypal_img_input', 'paypal_img_preview');
            setupImagePreview('bank_img_input', 'bank_img_preview');
        });
    </script>
@endpush
