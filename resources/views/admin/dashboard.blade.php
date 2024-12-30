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
                    <div class="card h-100 mt-5">
                        <div class="card-body pb-2">
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
                enabled: false
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
@endpush
