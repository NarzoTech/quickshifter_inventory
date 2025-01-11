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
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card dashboard_card mt-5">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                        <div class="avatar flex-shrink-0">
                                            <i class='bx bx-dollar'></i>
                                        </div>
                                    </div>
                                    <p class="mb-1">{{ 'Expense' }} ({{ now()->format('F') }})</p>
                                    <h4 class="card-title mb-3">{{ currency($chart['currentMonthExpense']) }}</h4>
                                    <small
                                        class="{{ $chart['expensePercentage'] > 0 ? 'text-success' : ($chart['expensePercentage'] < 0 ? 'text-danger' : 'text-primary') }} fw-medium">
                                        @if ($chart['expensePercentage'] > 0)
                                            <i class="bx bx-up-arrow-alt"></i>
                                            {{ $chart['expensePercentage'] }}%
                                        @elseif($chart['expensePercentage'] < 0)
                                            <i class="bx bx-down-arrow-alt"></i>
                                            {{ $chart['expensePercentage'] }}%
                                        @else
                                            0%
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card dashboard_card mt-5">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                        <div class="avatar flex-shrink-0">
                                            <i class='bx bx-basket'></i>
                                        </div>
                                    </div>
                                    <p class="mb-1">{{ 'Sales' }} ({{ now()->format('F') }})</p>
                                    <h4 class="card-title mb-3">{{ currency($chart['currentSales']) }}</h4>
                                    <small
                                        class="{{ $chart['salePercentage'] > 0 ? 'text-success' : ($chart['salePercentage'] < 0 ? 'text-danger' : 'text-primary') }} fw-medium">
                                        @if ($chart['salePercentage'] > 0)
                                            <i class="bx bx-up-arrow-alt"></i>
                                            {{ $chart['salePercentage'] }}%
                                        @elseif($chart['salePercentage'] < 0)
                                            <i class="bx bx-down-arrow-alt"></i>
                                            {{ $chart['salePercentage'] }}%
                                        @else
                                            0%
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card dashboard_card mt-5">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between mb-0">
                                        <div class="avatar flex-shrink-0">
                                            <i class='bx bx-cart-add'></i>
                                        </div>
                                    </div>
                                    <p class="mb-1">{{ 'Purchase' }} ({{ now()->format('F') }})</p>
                                    <h4 class="card-title mb-3">{{ currency($chart['currentPurchases']) }}</h4>
                                    <small
                                        class="{{ $chart['purchasePercentage'] > 0 ? 'text-success' : ($chart['purchasePercentage'] < 0 ? 'text-danger' : 'text-primary') }} fw-medium">
                                        @if ($chart['purchasePercentage'] > 0)
                                            <i class="bx bx-up-arrow-alt"></i>
                                            {{ $chart['purchasePercentage'] }}%
                                        @elseif($chart['purchasePercentage'] < 0)
                                            <i class="bx bx-down-arrow-alt"></i>
                                            {{ $chart['purchasePercentage'] }}%
                                        @else
                                            0%
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 my-5">
                    <div class="card text-center h-100">
                        <div class="card-header nav-align-top">
                            <ul class="nav nav-pills flex-wrap row-gap-2" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-browser" aria-controls="navs-pills-browser"
                                        aria-selected="true">{{ __('Low Stock') }}</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-os" aria-controls="navs-pills-os"
                                        aria-selected="false">{{ __('Customer Due') }}</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-country" aria-controls="navs-pills-country"
                                        aria-selected="false">{{ __('Supplier Due') }}</button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content pt-0 pb-4">
                            <div class="tab-pane fade show active" id="navs-pills-browser" role="tabpanel">
                                <div class="table-responsive text-start text-nowrap">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>{{ __('No') }}</th>
                                                <th>{{ __('Product') }}</th>
                                                <th>{{ __('Stock Alert') }}</th>
                                                <th>{{ __('Stock') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['low_stock_products'] as $index => $product)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        {{ $product->name }}
                                                    </td>
                                                    <td class="text-heading">{{ $product->stock_alert }}</td>
                                                    <td class="text-heading">{{ $product->stock }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-pills-os" role="tabpanel">
                                <div class="table-responsive text-start text-nowrap">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>System</th>
                                                <th>Visits</th>
                                                <th class="w-50">Data In Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="../../assets/img/icons/brands/windows.png"
                                                            alt="Windows" height="24" class="me-3" />
                                                        <span class="text-heading">Windows</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">875.24k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: 61.50%" aria-valuenow="61.50"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">61.50%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="../../assets/img/icons/brands/mac.png" alt="Mac"
                                                            height="24" class="me-3" />
                                                        <span class="text-heading">Mac</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">89.68k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-primary" role="progressbar"
                                                                style="width: 16.67%" aria-valuenow="16.67"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">16.67%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="../../assets/img/icons/brands/ubuntu.png" alt="Ubuntu"
                                                            height="24" class="me-3" />
                                                        <span class="text-heading">Ubuntu</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">37.68k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-info" role="progressbar"
                                                                style="width: 12.82%" aria-valuenow="12.82"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">12.82%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="../../assets/img/icons/brands/chrome.png" alt="Chrome"
                                                            height="24" class="me-3" />
                                                        <span class="text-heading">Chrome</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">8.34k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-warning" role="progressbar"
                                                                style="width: 6.25%" aria-valuenow="6.25"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">6.25%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="../../assets/img/icons/brands/cent.png" alt="Cent"
                                                            height="24" class="me-3" />
                                                        <span class="text-heading">Cent</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">2.25k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-danger" role="progressbar"
                                                                style="width: 2.76%" aria-valuenow="2.76"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">2.76%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="../../assets/img/icons/brands/linux.png" alt="linux"
                                                            height="24" class="me-3" />
                                                        <span class="text-heading">Linux</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">328k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-danger" role="progressbar"
                                                                style="width: 20.14%" aria-valuenow="2.76"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">20.14%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-pills-country" role="tabpanel">
                                <div class="table-responsive text-start text-nowrap">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Country</th>
                                                <th>Visits</th>
                                                <th class="w-50">Data In Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fis fi fi-us rounded-circle fs-4 me-3"></i>
                                                        <span class="text-heading">USA</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">87.24k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: 38.12%" aria-valuenow="38.12"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">38.12%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fis fi fi-br rounded-circle fs-4 me-3"></i>
                                                        <span class="text-heading">Brazil</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">42.68k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-primary" role="progressbar"
                                                                style="width: 28.23%" aria-valuenow="28.23"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">28.23%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fis fi fi-in rounded-circle fs-4 me-3"></i>
                                                        <span class="text-heading">India</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">12.58k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-info" role="progressbar"
                                                                style="width: 14.82%" aria-valuenow="14.82"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">14.82%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fis fi fi-au rounded-circle fs-4 me-3"></i>
                                                        <span class="text-heading">Australia</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">4.13k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-warning" role="progressbar"
                                                                style="width: 12.72%" aria-valuenow="12.72"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">12.72%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fis fi fi-fr rounded-circle fs-4 me-3"></i>
                                                        <span class="text-heading">France</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">2.21k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-danger" role="progressbar"
                                                                style="width: 7.11%" aria-valuenow="7.11"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">7.11%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fis fi fi-ca rounded-circle fs-4 me-3"></i>
                                                        <span class="text-heading">Canada</span>
                                                    </div>
                                                </td>
                                                <td class="text-heading">22.35k</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center gap-4">
                                                        <div class="progress w-100" style="height:10px;">
                                                            <div class="progress-bar bg-danger" role="progressbar"
                                                                style="width: 15.13%" aria-valuenow="7.11"
                                                                aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="fw-medium">15.13%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-6">
                    <div class="card h-100 mt-5">
                        <div class="card-body pb-2">
                            <h5 class="card-title mb-1">{{ __('Current Month Sales') }}</h5>
                            <div id="salesChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-6">
                    <div class="card h-100 mt-5">
                        <div class="card-body pb-2">
                            <h5 class="card-title mb-1">{{ __('Year Wise Sales & Purchase') }}</h5>
                            <div id="profitChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        const purchase = @json($purchaseData);
        const purchaseVal = Object.values(purchase).map(value => parseInt(value, 10)).filter(value => !isNaN(value));

        const sales = @json($saleData);
        const salesVal = Object.values(sales).map(value => parseInt(value, 10)).filter(value => !isNaN(value));

        var chartOptions = {
            series: [{
                name: 'Purchase',
                data: purchaseVal
            }, {
                name: 'Sales',
                data: salesVal
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 5,
                    borderRadiusApplication: 'end'
                },
            },
            dataLabels: {
                enabled: true
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#profitChart"), chartOptions);
        chart.render();
    </script>


    <script>
        let currentMonthData = @json($chart['currentMonthSaleData']);
        const currentMonthKeys = Object.keys(currentMonthData).map(key => new Date(key).toISOString());
        currentMonthData = Object.values(currentMonthData).map(value => parseInt(value, 10)).filter(value => !isNaN(value));
        // sales chart by date
        var salesOptions = {
            series: [{
                name: 'Sales',
                data: [...currentMonthData]
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: [...currentMonthKeys],
            },
            tooltip: {
                x: {
                    format: 'dd/MM'
                },
                y: {
                    formatter: function(val) {
                        return "{{ currency_icon() }} " + val
                    }
                }
            },
        };

        var chart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
        chart.render();
    </script>
@endpush
