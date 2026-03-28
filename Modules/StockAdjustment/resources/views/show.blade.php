@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Stock Adjustment Details') }}</title>
@endsection

@section('content')
    {{-- Top Stats Row --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <small class="text-muted d-block mb-1">{{ __('Quantity Adjusted') }}</small>
                            <h4 class="mb-0 text-danger">-{{ $adjustment->quantity }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="bx bx-package"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <small class="text-muted d-block mb-1">{{ __('Unit Cost') }}</small>
                            <h4 class="mb-0">{{ currency($adjustment->unit_cost) }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-dollar-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <small class="text-muted d-block mb-1">{{ __('Total Loss') }}</small>
                            <h4 class="mb-0 text-danger">{{ currency($adjustment->total_loss) }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-trending-down"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <small class="text-muted d-block mb-1">{{ __('Current Stock') }}</small>
                            <h4 class="mb-0 {{ $currentStock <= 0 ? 'text-danger' : 'text-success' }}">{{ $currentStock }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-{{ $currentStock <= 0 ? 'danger' : 'success' }}">
                                <i class="bx bx-box"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Adjustment Details --}}
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center pb-0">
                    <div>
                        <h5 class="mb-1">{{ __('Adjustment Details') }}</h5>
                        <small class="text-muted">{{ $adjustment->invoice }}</small>
                    </div>
                    @if($adjustment->reason == 'damage')
                        <span class="badge bg-danger px-3 py-2" style="font-size: 13px;">{{ __('Damage') }}</span>
                    @elseif($adjustment->reason == 'missing')
                        <span class="badge bg-warning px-3 py-2" style="font-size: 13px;">{{ __('Missing') }}</span>
                    @elseif($adjustment->reason == 'theft')
                        <span class="badge bg-dark px-3 py-2" style="font-size: 13px;">{{ __('Theft') }}</span>
                    @elseif($adjustment->reason == 'expired')
                        <span class="badge bg-info px-3 py-2" style="font-size: 13px;">{{ __('Expired') }}</span>
                    @else
                        <span class="badge bg-secondary px-3 py-2" style="font-size: 13px;">{{ __('Other') }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('SKU') }}</th>
                                    <th class="text-center">{{ __('Qty') }}</th>
                                    <th class="text-end">{{ __('Unit Cost') }}</th>
                                    <th class="text-end">{{ __('Total Loss') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $adjustment->product->single_image }}" alt="" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <span>{{ $adjustment->product->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td><code>{{ $adjustment->product->sku ?? '-' }}</code></td>
                                    <td class="text-center"><span class="text-danger fw-bold fs-5">-{{ $adjustment->quantity }}</span></td>
                                    <td class="text-end">{{ currency($adjustment->unit_cost) }}</td>
                                    <td class="text-end"><span class="text-danger fw-bold">{{ currency($adjustment->total_loss) }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-3">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <small class="text-muted text-uppercase d-block mb-1">{{ __('Date') }}</small>
                                <span><i class="bx bx-calendar me-1 text-primary"></i> {{ formatDate($adjustment->date) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <small class="text-muted text-uppercase d-block mb-1">{{ __('Created By') }}</small>
                                <span><i class="bx bx-user me-1 text-primary"></i> {{ $adjustment->createdBy->name ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <small class="text-muted text-uppercase d-block mb-1">{{ __('Created At') }}</small>
                                <span><i class="bx bx-time me-1 text-primary"></i> {{ $adjustment->created_at->format('d-m-Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($adjustment->note)
                        <div class="alert alert-secondary mb-0">
                            <i class="bx bx-note me-1"></i> <strong>{{ __('Note') }}:</strong> {{ $adjustment->note }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Pie Chart + Linked Records --}}
        <div class="col-xl-4">
            {{-- Pie Chart --}}
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-pie-chart-alt-2 me-1 text-primary"></i>
                        {{ __('Loss by Reason') }}
                    </h5>
                    <small class="text-muted">{{ $adjustment->product->name ?? '' }}</small>
                </div>
                <div class="card-body">
                    <div id="reasonPieChart"></div>
                </div>
            </div>

            {{-- Linked Records --}}
            <div class="card">
                <div class="card-header pb-2">
                    <h6 class="mb-0">{{ __('Linked Records') }}</h6>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bx bx-receipt text-warning me-2"></i>
                                <small class="text-muted">{{ __('Expense') }}</small>
                            </div>
                            @if($adjustment->expense && $adjustment->expense->invoice)
                                <span class="badge bg-label-warning">{{ $adjustment->expense->invoice }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bx bx-category text-info me-2"></i>
                                <small class="text-muted">{{ __('Expense Type') }}</small>
                            </div>
                            <span>{{ $adjustment->expense->expenseType->name ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bx bx-transfer text-success me-2"></i>
                                <small class="text-muted">{{ __('Stock Record') }}</small>
                            </div>
                            <span class="badge bg-label-success">{{ __('Stock Adjustment') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-4">
        <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> {{ __('Back to List') }}
        </a>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Reason-wise pie chart data
            var reasonData = @json($reasonSummary);
            var reasonColors = {
                'damage': '#ff3e1d',
                'missing': '#ffab00',
                'theft': '#233446',
                'expired': '#03c3ec',
                'other': '#8592a3'
            };

            if (reasonData.length > 0) {
                var options = {
                    series: reasonData.map(function(item) { return parseFloat(item.total_loss); }),
                    chart: {
                        type: 'pie',
                        height: 280
                    },
                    labels: reasonData.map(function(item) {
                        return item.reason.charAt(0).toUpperCase() + item.reason.slice(1);
                    }),
                    colors: reasonData.map(function(item) {
                        return reasonColors[item.reason] || '#8592a3';
                    }),
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "{{ currency_icon() }} " + val.toLocaleString();
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: { height: 220 }
                        }
                    }]
                };

                var chart = new ApexCharts(document.querySelector("#reasonPieChart"), options);
                chart.render();
            } else {
                document.querySelector("#reasonPieChart").innerHTML = '<p class="text-center text-muted py-4">{{ __("No data available") }}</p>';
            }
        });
    </script>
@endpush
