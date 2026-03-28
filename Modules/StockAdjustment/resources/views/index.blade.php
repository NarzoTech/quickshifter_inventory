@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Stock Adjustments') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="reason" class="form-control">
                                        <option value="">{{ __('All Reasons') }}</option>
                                        <option value="damage" {{ request('reason') == 'damage' ? 'selected' : '' }}>{{ __('Damage') }}</option>
                                        <option value="missing" {{ request('reason') == 'missing' ? 'selected' : '' }}>{{ __('Missing') }}</option>
                                        <option value="theft" {{ request('reason') == 'theft' ? 'selected' : '' }}>{{ __('Theft') }}</option>
                                        <option value="expired" {{ request('reason') == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                        <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="order_type" class="form-control">
                                        <option value="id" {{ request('order_type') == 'id' ? 'selected' : '' }}>{{ __('Serial') }}</option>
                                        <option value="date" {{ request('order_type') == 'date' ? 'selected' : '' }}>{{ __('Date') }}</option>
                                        <option value="total_loss" {{ request('order_type') == 'total_loss' ? 'selected' : '' }}>{{ __('Total Loss') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="order_by" class="form-control">
                                        <option value="">{{ __('Order By') }}</option>
                                        <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>{{ __('ASC') }}</option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>{{ __('DESC') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>{{ __('10') }}</option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>{{ __('50') }}</option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>{{ __('100') }}</option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>{{ __('All') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-label-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-5">
                <div class="card-header">
                    <div class="card-header-title">
                        <h4 class="section_title">{{ __('Stock Adjustments') }}</h4>
                    </div>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        @adminCan('stock.adjustment.create')
                            <a href="{{ route('admin.stock-adjustment.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> {{ __('New Adjustment') }}
                            </a>
                        @endadminCan
                        @adminCan('stock.adjustment.view')
                            <button type="button" class="btn bg-label-warning export-pdf">
                                <i class="fa fa-file-pdf"></i> PDF
                            </button>
                        @endadminCan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th style="width: 4%">{{ __('Sl') }}</th>
                                    <th style="width: 8%">{{ __('Invoice') }}</th>
                                    <th style="width: 8%">{{ __('Date') }}</th>
                                    <th style="width: 18%">{{ __('Product') }}</th>
                                    <th style="width: 8%">{{ __('Qty') }}</th>
                                    <th style="width: 10%">{{ __('Reason') }}</th>
                                    <th style="width: 10%">{{ __('Unit Cost') }}</th>
                                    <th style="width: 10%">{{ __('Total Loss') }}</th>
                                    <th style="width: 12%">{{ __('Note') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $start =
                                        $lists instanceof \Illuminate\Pagination\LengthAwarePaginator
                                            ? $lists->firstItem()
                                            : 1;
                                @endphp
                                @forelse ($lists as $index => $adjustment)
                                    <tr>
                                        <td>{{ $start + $index }}</td>
                                        <td>{{ $adjustment->invoice }}</td>
                                        <td>{{ formatDate($adjustment->date) }}</td>
                                        <td>{{ $adjustment->product->name ?? '-' }}</td>
                                        <td>{{ $adjustment->quantity }}</td>
                                        <td>
                                            @if($adjustment->reason == 'damage')
                                                <span class="badge bg-danger">{{ __('Damage') }}</span>
                                            @elseif($adjustment->reason == 'missing')
                                                <span class="badge bg-warning">{{ __('Missing') }}</span>
                                            @elseif($adjustment->reason == 'theft')
                                                <span class="badge bg-dark">{{ __('Theft') }}</span>
                                            @elseif($adjustment->reason == 'expired')
                                                <span class="badge bg-info">{{ __('Expired') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Other') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ currency($adjustment->unit_cost) }}</td>
                                        <td>{{ currency($adjustment->total_loss) }}</td>
                                        <td>{{ $adjustment->note ?? '-' }}</td>
                                        <td>
                                            @if (checkAdminHasPermission('stock.adjustment.view') || checkAdminHasPermission('stock.adjustment.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $adjustment->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $adjustment->id }}">
                                                        @adminCan('stock.adjustment.view')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.stock-adjustment.show', $adjustment->id) }}">{{ __('View') }}</a>
                                                        @endadminCan
                                                        @adminCan('stock.adjustment.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $adjustment->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($lists) > 0)
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>{{ __('Total') }}</strong></td>
                                        <td><strong>{{ $data['totalQuantity'] }}</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td><strong>{{ currency($data['totalLoss']) }}</strong></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                    @if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-center mt-3">
                            {{ $lists->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function deleteData(id) {
            let url = "{{ route('admin.stock-adjustment.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }

        // PDF export
        $('.export-pdf').on('click', function() {
            var fullUrl = window.location.href;
            if (fullUrl.includes('?')) {
                fullUrl += '&export_pdf=true';
            } else {
                fullUrl += '?export_pdf=true';
            }
            window.open(fullUrl, '_blank');
        })
    </script>
@endpush
