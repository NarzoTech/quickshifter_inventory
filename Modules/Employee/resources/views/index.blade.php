@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Employee List') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-4 col-md-6">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <select name="order_type" id="order_type" class="form-control">
                                        <option value="id" {{ request('order_type') == 'id' ? 'selected' : '' }}>
                                            {{ __('Serial') }}</option>
                                        <option value="name" {{ request('order_type') == 'name' ? 'selected' : '' }}>
                                            {{ __('Name') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
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
                            <div class="col-xxl-2 col-md-6">
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
                            <div class="col-xxl-2 col-md-6">
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
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                        <h4 class="section_title"> {{ __('Employee List') }}</h4>
                    </div>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        @adminCan('employee.create')
                            <a href="{{ route('admin.employee.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i>
                                {{ __('Add New Employee') }}</a>
                        @endadminCan
                        <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                            Excel</button>
                        <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                            PDF</button>
                    </div>
                </div>
                <div class="card-body">
                    @adminCan('employee.salary.increment')
                    <div class="alert alert-info d-none justify-content-between increment-section flex-wrap align-items-center mb-3">
                        <span><span class="increment-count">0</span> {{ __('employee(s) selected') }}</span>
                        <button class="btn btn-success increment-selected-btn" data-bs-toggle="modal"
                            data-bs-target="#salaryIncrementModal">
                            <i class="fa fa-arrow-up"></i> {{ __('Increment Salary') }}
                        </button>
                    </div>
                    @endadminCan
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table mb-3">
                            <thead>
                                <tr>
                                    @if(checkAdminHasPermission('employee.salary.increment'))
                                    <th>
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" data-checkboxes="checkgroup" data-checkbox-role="dad"
                                                class="custom-control-input" id="checkbox-all">
                                            <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </th>
                                    @endif
                                    <th>{{ __('Sl') }}</th>
                                    <th>{{ __('Employee Name') }}</th>
                                    <th>{{ __('Employee Picture') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Base Salary') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Joining Date') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $index => $employee)
                                    <tr>
                                        @if(checkAdminHasPermission('employee.salary.increment'))
                                        <td>
                                            <div class="custom-checkbox custom-control">
                                                <input type="checkbox" data-checkboxes="checkgroup" class="custom-control-input"
                                                    id="checkbox-{{ $employee->id }}" name="select">
                                                <label for="checkbox-{{ $employee->id }}" class="custom-control-label">&nbsp;</label>
                                            </div>
                                        </td>
                                        @endif
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $employee->name }}</td>
                                        <td>
                                            <img src="{{ $employee->image ? asset($employee->image) : asset('/uploads/employee/default.png') }}"
                                                alt="" width="50px" height="50px">
                                        </td>
                                        <td>{{ $employee->designation }}</td>
                                        <td>{{ $employee->mobile }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->salary }}</td>
                                        <td>
                                            @if ($employee->status == 1)
                                                <span class="badge badge-success">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ formatDate($employee->join_date) }}</td>
                                        <td>
                                            @if (checkAdminHasPermission('employee.edit') ||
                                                    checkAdminHasPermission('employee.view.payment') ||
                                                    checkAdminHasPermission('employee.pay.salary') ||
                                                    checkAdminHasPermission('employee.pay.advance') ||
                                                    checkAdminHasPermission('employee.salary.increment') ||
                                                    checkAdminHasPermission('employee.status') ||
                                                    checkAdminHasPermission('employee.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $employee->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $employee->id }}">
                                                        @adminCan('employee.edit')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.edit', $employee->id) }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.salary.increment')
                                                            <a class="dropdown-item increment-single" href="javascript:;"
                                                                data-id="{{ $employee->id }}">{{ __('Increment Salary') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.view.payment')
                                                            <a class="dropdown-item view-payment" href="javascript:;"
                                                                data-id="{{ $employee->id }}">{{ __('View Payments') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.pay.salary')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.salary.create', $employee->id) }}?pay=1">{{ __('Pay Salary') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.pay.advance')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.salary.create', $employee->id) }}?pay=2">{{ __('Pay Advance') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.status')
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.status', $employee->id) }}">{{ $employee->status == 1 ? __('Inactive') : __('Active') }}</a>
                                                        @endadminCan
                                                        @adminCan('employee.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $employee->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Employee List')" route="" create="no" :message="__('No data found!')"
                                        colspan="{{ checkAdminHasPermission('employee.salary.increment') ? 11 : 10 }}"></x-empty-table>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if (request()->get('par-page') !== 'all')
                        <div class="float-right">
                            {{ $employees->onEachSide(0)->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>




    <div tabindex="-1" role="dialog" id="viewDate" class ='modal'>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Pick Date') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body py-0">
                    <div id="calendar">
                        <form id="viewDateForm" action="" method="get">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Months') }}</label>
                                        @php
                                            $months = [];
                                            for ($i = 1; $i <= 12; $i++) {
                                                $m = date('m', mktime(0, 0, 0, $i, 1));
                                                $months[$m] = date('F', mktime(0, 0, 0, $i, 1));
                                            }
                                        @endphp
                                        <select name="month" id="month" class="form-control">
                                            <option value="">{{ __('Select Month') }}</option>
                                            @foreach ($months as $key => $month)
                                                <option value="{{ $month }}"
                                                    {{ $month == date('F') ? 'selected' : '' }}>{{ $month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Year') }}</label>
                                        @php
                                            $years = [];
                                            for ($i = 0; $i < 5; $i++) {
                                                $years[] = date('Y') - $i;
                                            }

                                        @endphp
                                        <select name="year" id="year" class="form-control">
                                            <option value="">{{ __('Select Year') }}</option>
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="submit" class="btn btn-primary" form="viewDateForm">{{ __('Show') }}</button>
                </div>
            </div>
        </div>
    </div>


    {{-- Salary Increment Modal --}}
    @adminCan('employee.salary.increment')
    <div tabindex="-1" role="dialog" id="salaryIncrementModal" class="modal fade" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Salary Increment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Step 1: Input --}}
                    <div id="increment-step-1">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Increment Type') }} <span class="text-danger">*</span></label>
                                    <select id="increment_type" class="form-control">
                                        <option value="amount">{{ __('Fixed Amount') }}</option>
                                        <option value="percentage">{{ __('Percentage (%)') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Value') }} <span class="text-danger">*</span></label>
                                    <input type="number" id="increment_value" class="form-control"
                                        min="0.01" step="any" placeholder="{{ __('Enter amount or percentage') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Note (Optional)') }}</label>
                                    <input type="text" id="increment_note" class="form-control"
                                        placeholder="{{ __('e.g. Annual review 2026') }}" maxlength="255">
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="preview-increment-btn">
                                {{ __('Preview Changes') }}
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Preview --}}
                    <div id="increment-step-2" class="d-none">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Employee') }}</th>
                                        <th>{{ __('Current Salary') }}</th>
                                        <th>{{ __('New Salary') }}</th>
                                        <th>{{ __('Change') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="preview-table-body"></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary" id="back-to-step1-btn">
                                {{ __('Back') }}
                            </button>
                            <button type="button" class="btn btn-success" id="apply-increment-btn">
                                {{ __('Apply Increment') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endadminCan

    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.employee.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }

            $('.view-payment').on('click', function() {
                var id = $(this).data('id');
                $('#viewDate').modal('show');
                let url = "{{ route('admin.employee.salary.view', ':id') }}";
                url = url.replace(':id', id);
                $('#viewDateForm').attr('action', url);
            });

            // Salary Increment — checkbox & bulk logic
            @if(checkAdminHasPermission('employee.salary.increment'))
            $('#checkbox-all').on('change', function() {
                $('input[name="select"]').prop('checked', $(this).is(':checked'));
                updateIncrementBar();
            });

            $(document).on('change', 'input[name="select"]', function() {
                var total = $('input[name="select"]').length;
                var checked = $('input[name="select"]:checked').length;
                $('#checkbox-all').prop('checked', total === checked);
                updateIncrementBar();
            });

            function updateIncrementBar() {
                var count = $('input[name="select"]:checked').length;
                $('.increment-count').text(count);
                if (count > 0) {
                    $('.increment-section').removeClass('d-none').addClass('d-flex');
                } else {
                    $('.increment-section').addClass('d-none').removeClass('d-flex');
                }
            }

            function getSelectedIds() {
                var ids = [];
                $('input[name="select"]:checked').each(function() {
                    ids.push($(this).attr('id').split('-')[1]);
                });
                return ids;
            }

            // Single employee increment from Action dropdown
            $(document).on('click', '.increment-single', function() {
                $('input[name="select"]').prop('checked', false);
                var id = $(this).data('id');
                $('#checkbox-' + id).prop('checked', true);
                updateIncrementBar();
                $('#salaryIncrementModal').modal('show');
            });

            // Preview
            $('#preview-increment-btn').on('click', function() {
                var ids = getSelectedIds();
                var type = $('#increment_type').val();
                var value = $('#increment_value').val();

                if (!value || parseFloat(value) <= 0) {
                    toastr.error('{{ __("Please enter a valid increment value.") }}');
                    return;
                }
                if (ids.length === 0) {
                    toastr.error('{{ __("No employees selected.") }}');
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.employee.salary.increment.preview') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        employee_ids: ids,
                        increment_type: type,
                        increment_value: value,
                    },
                    success: function(response) {
                        var html = '';
                        response.preview.forEach(function(item) {
                            var change = item.new_salary - item.previous_salary;
                            html += '<tr>' +
                                '<td>' + item.name + '</td>' +
                                '<td>' + item.previous_salary.toLocaleString() + '</td>' +
                                '<td>' + item.new_salary.toLocaleString() + '</td>' +
                                '<td class="text-success fw-bold">+' + change.toLocaleString() + '</td>' +
                                '</tr>';
                        });
                        $('#preview-table-body').html(html);
                        $('#increment-step-1').addClass('d-none');
                        $('#increment-step-2').removeClass('d-none');
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON?.message || '{{ __("Failed to generate preview.") }}';
                        toastr.error(message);
                    }
                });
            });

            // Back
            $('#back-to-step1-btn').on('click', function() {
                $('#increment-step-2').addClass('d-none');
                $('#increment-step-1').removeClass('d-none');
            });

            // Apply — close modal first so SweetAlert isn't behind it
            $('#apply-increment-btn').on('click', function() {
                var ids = getSelectedIds();
                var type = $('#increment_type').val();
                var value = $('#increment_value').val();
                var note = $('#increment_note').val();

                $('#salaryIncrementModal').modal('hide');
                $('#salaryIncrementModal').one('hidden.bs.modal', function() {
                    Swal.fire({
                        title: '{{ __("Confirm Salary Increment") }}',
                        text: '{{ __("Are you sure you want to apply this salary increment to") }} ' + ids.length + ' {{ __("employee(s)?") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __("Yes, apply it!") }}',
                        cancelButtonText: '{{ __("Cancel") }}'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            // Re-open modal if cancelled
                            $('#salaryIncrementModal').modal('show');
                            return;
                        }
                        $.ajax({
                            url: "{{ route('admin.employee.salary.increment') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                employee_ids: ids,
                                increment_type: type,
                                increment_value: value,
                                note: note,
                            },
                            beforeSend: function() {
                                $('#apply-increment-btn').prop('disabled', true).text('{{ __("Processing...") }}');
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    setTimeout(function() { location.reload(); }, 1000);
                                } else {
                                    toastr.error(response.message);
                                    $('#apply-increment-btn').prop('disabled', false).text('{{ __("Apply Increment") }}');
                                }
                            },
                            error: function(xhr) {
                                var message = xhr.responseJSON?.message || '{{ __("Failed to apply increment.") }}';
                                toastr.error(message);
                                $('#apply-increment-btn').prop('disabled', false).text('{{ __("Apply Increment") }}');
                            }
                        });
                    });
                });
            });

            // Reset modal state when closed via X button (not during confirm flow)
            var isConfirmFlow = false;
            $('#apply-increment-btn').on('mousedown', function() { isConfirmFlow = true; });
            $('#salaryIncrementModal').on('hidden.bs.modal', function() {
                if (!isConfirmFlow) {
                    $('#increment-step-2').addClass('d-none');
                    $('#increment-step-1').removeClass('d-none');
                    $('#increment_value').val('');
                    $('#increment_note').val('');
                    $('#preview-table-body').html('');
                }
                isConfirmFlow = false;
            });
            @endif
        </script>
    @endpush
@endsection
