@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Other Due Ledger') }}</title>
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
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="order_by" id="order_by" class="form-control">
                                        <option value="">{{ __('Order By') }}</option>
                                        <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                            {{ __('ASC') }}
                                        </option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                            {{ __('DESC') }}
                                        </option>
                                    </select>
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
                            <div class="col-xxl-3 col-md-6 col-lg-4">
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
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> {{ __('Supplier Other Due Ledger') }}</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Paid') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summeries as $summery)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ formatDate($summery->date) }}</td>
                                <td>{{ $summery->supplier->name }}</td>
                                <td>{{ $summery->supplier->company }}</td>
                                <td>{{ $summery->supplier->phone }}</td>
                                <td>{{ $summery->amount }}</td>
                                <td>{{ $summery->paid }}</td>
                                <td>{{ $summery->due }}</td>
                                <td>
                                    @if (checkAdminHasPermission('supplier.other.due.edit') || checkAdminHasPermission('supplier.other.due.delete'))
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop{{ $summery->id }}" type="button"
                                                class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $summery->id }}">

                                                @adminCan('supplier.other.due.edit')
                                                    <a href="javascript:void(0);" data-bs-toggle="modal"
                                                        data-bs-target="#editsupplier-{{ $summery->id }}"
                                                        class="dropdown-item">{{ __('Edit') }}</a>
                                                @endadminCan
                                                @adminCan('supplier.other.due.delete')
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $summery->id }})">{{ __('Delete') }}</a>
                                                @endadminCan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if ($summeries->count() > 0)
                            <tr>
                                <td colspan="5" class="text-center">
                                    <b>Total</b>
                                </td>
                                <td>
                                    <b>{{ currency($data['total_amount']) }}</b>
                                </td>
                                <td>
                                    <b>{{ currency($data['total_paid']) }}</b>
                                </td>
                                <td>
                                    <b>{{ currency($data['total_due']) }}</b>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $summeries->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>


    @foreach ($summeries as $summery)
        <div class="modal fade" id="editsupplier-{{ $summery->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="section_title">{{ __('Edit supplier Other Due') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.other-summery.supplier.update', $summery->id) }}" method="POST"
                            id="add-supplier-due-{{ $summery->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <input type="hidden" name="supplier_id" value="{{ $summery->supplier_id }}">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('Date') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" id="date"
                                            name="date" value="{{ formatDate($summery->date) }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">{{ __('Total Amount') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="amount" name="amount"
                                            value="{{ $summery->amount }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="paid">{{ __('Paid') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="paid" name="paid"
                                            value="{{ $summery->paid }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due">{{ __('Due') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="due" name="due"
                                            value="{{ $summery->due }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">{{ __('Description') }}</label>
                                        <textarea name="description" id="description" class="form-control height-80px" rows="3">{{ $summery->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="add-supplier-due-{{ $summery->id }}">Save</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            $('[name="paid"],[name="amount"]').on('input', function() {
                var paid = $(this).val() ? parseFloat($(this).val()) : 0;
                var amount = $('[name="amount"]').val() ? parseFloat($('[name="amount"]').val()) : 0;
                var due = amount - paid;

                const form = $(this).closest('form');
                form.find('[name="due"]').val(due);
            })
        })

        function deleteData(id) {
            let url = '{{ route('admin.other-summery.supplier.delete', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
