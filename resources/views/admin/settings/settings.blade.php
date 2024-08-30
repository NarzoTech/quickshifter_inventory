@extends('admin.master_layout')
@section('title')
    <title>{{ __('Settings') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Settings') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Settings') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.purchase.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __('Create Purchase') }}</div>
                                </div>

                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-md-12 mb-3">
                                            <label for="">Business Name</label>
                                            <input type="text" name="app_name" value="Quick Shifter" class="form-control"
                                                required autocomplete="off">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="">Mobile Phone</label>
                                            <input type="text" name="mobile" value="01912523449" class="form-control"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="">Email</label>
                                            <input type="email" name="email" value="quickshifter21@gmail.com"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="">Address</label>
                                            <input type="text" name="address"
                                                value="Shop No - 01, Plot - 02, Road - 09, Sector -15/D, Uttara"
                                                class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="">Business Type</label>
                                            <input type="text" name="type" value="Owner" class="form-control"
                                                readonly required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="">City</label>
                                            <input type="text" name="city" value="Dhaka" class="form-control"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="">Zip</label>
                                            <input type="text" name="zip" value="1230" class="form-control"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="">Country</label>
                                            <select name="country" id="" class="select2">
                                                @foreach ($allCountries as $country)
                                                    <option value="{{ $country }}">
                                                        {{ $country }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="">Website</label>
                                            <input type="text" name="website" value="" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="">Business Start Date</label>
                                            <input type="date" name="start_date" value="2023-11-01" class="form-control"
                                                required>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="">Date Format</label>
                                            <select class="form-control" name="date_format">
                                                <option value="d/m/Y" selected>
                                                    d/m/Y (30/08/2024)
                                                </option>
                                                <option value="Y-m-d">
                                                    Y-m-d (2024-08-30)
                                                </option>
                                                <option value="Facebook">
                                                    Facebook (30th August)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="">Time Format</label>
                                            <select class="form-control" name="time_format">
                                                <option value="h:ia" selected>
                                                    12-hour (08:46pm)
                                                </option>
                                                <option value="H:i">
                                                    24-hour (20:46)
                                                </option>

                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="">Timezone</label>
                                            <select name="timezone" id="" class="form-control select2">
                                                @foreach ($all_timezones as $timezone)
                                                    <option value="{{ $timezone->name }}" @selected($setting->timezone == $timezone->name)>
                                                        {{ $timezone->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="">Report Date Sorting</label>
                                            <select class="form-control" name="report_default_days">
                                                <option value="1">
                                                    Current Date
                                                </option>
                                                <option value="7">
                                                    Last 7 Days
                                                </option>
                                                <option value="30">
                                                    Last 30 Days
                                                </option>
                                                <option value="365">
                                                    Last 365 Days
                                                </option>
                                                <option value="" selected>
                                                    All
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="">Color</label>
                                            <input type="color" name="color" value="#1ba161" class="form-control"
                                                required>
                                            <input type="hidden" name="status" value="1" required>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="">Currency</label>
                                            <input type="text" name="currency" value="TK" class="form-control"
                                                placeholder="Currency" required>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="">Select Business Vat(%)</label>
                                            <input type="number" name="vat" value="0" class="form-control"
                                                step=".01" placeholder="Ex: 10" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="">Number of Digits in Phone Number</label>
                                            <input type="number" name="min_phone_number" value="11"
                                                class="form-control" onchange="checkNumber()">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Invoice Prefix</label>
                                                <input type="text" name="invoice_prefix" value="IN-"
                                                    class="form-control" readonly placeholder="EX: AS">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Invoice Suffix</label>
                                                <input type="number" name="invoice_suffix" value="100001"
                                                    class="form-control" readonly placeholder="EX: 10000001">
                                            </div>

                                            <div class="col-md-12">
                                                <span class="text-info">
                                                    If invoice prefix or suffix is null system will auto generate invoice
                                                    number
                                                </span>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-12 mt-3">
                                            <h4>Options</h4>
                                            <div class="form-group permission-checkboxs">
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="deliverycharge" type="checkbox"
                                                        id="deliverycharge" value="1">
                                                    <label class="form-check-label" for="deliverycharge">
                                                        DeliveryCharge
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="pos_due_payment"
                                                        type="checkbox" id="due-payment" value="1" checked>
                                                    <label class="form-check-label" for="due-payment">
                                                        Due Payment
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_auto_print"
                                                        type="checkbox" id="enable-auto-print" value="1" checked>
                                                    <label class="form-check-label" for="enable-auto-print">
                                                        Enable Auto Print
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_exchange"
                                                        type="checkbox" id="exchange" value="1">
                                                    <label class="form-check-label" for="exchange">
                                                        Exchange
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_investment"
                                                        type="checkbox" id="investment" value="1">
                                                    <label class="form-check-label" for="investment">
                                                        Investment
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_minimum_sale_price"
                                                        type="checkbox" id="minimum-sale-price" value="1">
                                                    <label class="form-check-label" for="minimum-sale-price">
                                                        Minimum Sale Price
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_negative_sale"
                                                        type="checkbox" id="negative-sale" value="1" checked>
                                                    <label class="form-check-label" for="negative-sale">
                                                        Negative Sale
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_online_order"
                                                        type="checkbox" id="online-order" value="1">
                                                    <label class="form-check-label" for="online-order">
                                                        Online Order
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_product_model"
                                                        type="checkbox" id="product-model" value="1" checked>
                                                    <label class="form-check-label" for="product-model">
                                                        Product Model
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_product_variant"
                                                        type="checkbox" id="product-variant" value="1" checked>
                                                    <label class="form-check-label" for="product-variant">
                                                        Product Variant
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input"
                                                        name="enable_purchase_panel_profit_percent" type="checkbox"
                                                        id="profit-percent" value="1" checked>
                                                    <label class="form-check-label" for="profit-percent">
                                                        Profit Percent
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="sale_and_print_confirmation"
                                                        type="checkbox" id="sale-print-confirmation" value="1">
                                                    <label class="form-check-label" for="sale-print-confirmation">
                                                        Sale &amp; Print Confirmation
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="enable_service" type="checkbox"
                                                        id="service" value="1" checked>
                                                    <label class="form-check-label" for="service">
                                                        Service
                                                    </label>
                                                </div>
                                                <div class="form-check ml-4">
                                                    <input class="form-check-input" name="show_stock_in_pos"
                                                        type="checkbox" id="show-stock-in-pos" value="1" checked>
                                                    <label class="form-check-label" for="show-stock-in-pos">
                                                        Show Stock In Pos
                                                    </label>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-6">
                                            <label>{{ __('Logo') }}</label>
                                            <div id="logo-preview" class="image-preview"
                                                @if (!empty($setting->logo)) style="background-image: url({{ asset($setting->logo) }}); background-size: cover; background-position: center center;" @endif>
                                                <label for="logo-upload" id="logo-label">{{ __('Logo') }}</label>
                                                <input type="file" name="logo" id="logo-upload">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


@push('js')
    <script>
        prevImage('logo-upload', 'logo-preview', 'logo-label');
    </script>
@endpush
