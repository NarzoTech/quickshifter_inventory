@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Due Pay List') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form " action="" method="GET">
                        @if (request()->get('supplier'))
                            <input type="hidden" name="supplier" value="{{ request()->get('supplier') }}">
                        @endif
                        <div class="row">
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search...">
                                    <button type="submit">
                                        <i class="bx bx-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <select name="supplier" class="form-control select2">
                                        <option value="">{{ __('All Suppliers') }}</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-1 col-md-4">
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
                            <div class="col-xxl-1 col-md-4">
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
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">{{ __('to') }}</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> {{ __('Supplier Due Pay List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn btn-primary export"><i class="fa fa-file-excel"></i>
                    {{ __('Excel') }}</button>
                <button type="button" class="btn btn-success export-pdf"><i class="fa fa-file-pdf"></i>
                    {{ __('PDF') }}</button>
            </div>
        </div>
        <div class="card-body">
            @adminCan('supplier.due.pay.delete')
            <div class="alert alert-danger d-none justify-content-between delete-section danger-bg flex-wrap align-items-center mb-3">
                <span>
                    <span class="selected-count">0</span> {{ __('rows selected') }}
                </span>
                <button class="btn btn-danger bulk-delete-btn">{{ __('Delete Selected') }}</button>
            </div>
            @endadminCan
            <div class="table-responsive">
                <table style="width: 100%;" class="table common_table">
                    <thead>
                        <tr>
                            @if(checkAdminHasPermission('supplier.due.pay.delete'))
                            <th>
                                <div class="custom-checkbox custom-control">
                                    <input type="checkbox" data-checkboxes="checkgroup" data-checkbox-role="dad"
                                        class="custom-control-input" id="checkbox-all">
                                    <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                </div>
                            </th>
                            @endif
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Paid By') }}</th>
                            @if(checkAdminHasPermission('supplier.due.pay.list') || checkAdminHasPermission('supplier.due.pay.delete'))
                            <th>{{ __('Action') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                @if(checkAdminHasPermission('supplier.due.pay.delete'))
                                <td>
                                    <div class="custom-checkbox custom-control">
                                        <input type="checkbox" data-checkboxes="checkgroup" class="custom-control-input"
                                            id="checkbox-{{ $payment->id }}" name="select">
                                        <label for="checkbox-{{ $payment->id }}" class="custom-control-label">&nbsp;</label>
                                    </div>
                                </td>
                                @endif
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ formatDate($payment->payment_date) }}</td>
                                <td>{{ $payment->purchase?->invoice_number }}</td>
                                <td>{{ $payment->supplier->name }}</td>
                                <td>{{ currency($payment->amount) }}</td>
                                <td>{{ $payment->createdBy->name }}</td>
                                @if(checkAdminHasPermission('supplier.due.pay.list') || checkAdminHasPermission('supplier.due.pay.delete'))
                                <td>
                                    <div class="btn-group">
                                        @adminCan('supplier.due.pay.list')
                                            <a href="{{ route('admin.supplier.due-pay.edit', $payment->id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endadminCan
                                        @adminCan('supplier.due.pay.delete')
                                            <a href="javascript:;" class="btn btn-danger btn-sm"
                                                onclick="deleteData({{ $payment->id }})">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endadminCan
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        @if ($payments->count() > 0)
                            <tr>
                                @if(checkAdminHasPermission('supplier.due.pay.delete'))
                                <td></td>
                                @endif
                                <td colspan="4" class="text-center fw-bold">
                                    {{ __('Total') }}
                                </td>
                                <td colspan="{{ (checkAdminHasPermission('supplier.due.pay.list') || checkAdminHasPermission('supplier.due.pay.delete')) ? 3 : 2 }}" class="fw-bold">
                                    {{ currency($data['total']) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $payments->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection


@push('js')
    <script>
        function deleteData(id) {
            let url = "{{ route('admin.supplier.due-pay.delete', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }

        $(document).ready(function() {
            // Select all checkbox
            $('#checkbox-all').on('change', function() {
                $('input[name="select"]').prop('checked', $(this).is(':checked'));
                updateSelectedCount();
            });

            // Individual checkbox
            $(document).on('change', 'input[name="select"]', function() {
                var total = $('input[name="select"]').length;
                var checked = $('input[name="select"]:checked').length;
                $('#checkbox-all').prop('checked', total == checked);
                updateSelectedCount();
            });

            function updateSelectedCount() {
                var count = $('input[name="select"]:checked').length;
                $('.selected-count').text(count);

                if (count > 0) {
                    $('.delete-section').removeClass('d-none').addClass('d-flex');
                } else {
                    $('.delete-section').addClass('d-none').removeClass('d-flex');
                }
            }

            // Bulk delete
            $('.bulk-delete-btn').on('click', function() {
                var ids = [];
                $('input[name="select"]:checked').each(function() {
                    ids.push($(this).attr('id').split('-')[1]);
                });

                if (ids.length === 0) return;

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to delete ' + ids.length + ' due payment record(s). This will restore due amounts.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete them!',
                    cancelButtonText: 'No, keep them'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.supplier.due-pay.bulk-delete') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: ids
                            },
                            beforeSend: function() {
                                $('.bulk-delete-btn').prop('disabled', true).text('Deleting...');
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    toastr.error(response.message);
                                    $('.bulk-delete-btn').prop('disabled', false).text('Delete Selected');
                                }
                            },
                            error: function(xhr) {
                                var message = xhr.responseJSON?.message || '{{ __("Permission Denied, You can not perform this action!") }}';
                                toastr.error(message);
                                $('.bulk-delete-btn').prop('disabled', false).text('Delete Selected');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
