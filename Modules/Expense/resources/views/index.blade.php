@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Expenses List') }}</title>
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
                                    <select name="order_type" id="order_type" class="form-control">
                                        <option value="id" {{ request('order_type') == 'id' ? 'selected' : '' }}>
                                            {{ __('Serial') }}</option>

                                        <option value="date" {{ request('order_type') == 'date' ? 'selected' : '' }}>
                                            {{ __('Date') }}</option>

                                        <option value="amount" {{ request('order_type') == 'amount' ? 'selected' : '' }}>
                                            {{ __('Amount') }}</option>

                                    </select>
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
                        <h4 class="section_title"> Expenses List</h4>
                    </div>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        @adminCan('expense.create')
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addExpense"
                                class="btn btn-primary"><i class="fa fa-plus"></i>
                                {{ __('Add Expense') }}</a>
                        @endadminCan
                        @adminCan('expense.excel.download')
                            <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                                Excel</button>
                        @endadminCan
                        @adminCan('expense.pdf.download')
                            <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                                PDF</button>
                        @endadminCan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th style="width: 5%">{{ __('Sl') }}</th>
                                    <th style="width: 15%">{{ __('Date') }}</th>
                                    <th style="width: 25%">{{ __('Created By') }}</th>
                                    <th style="width: 25%">{{ __('Type') }}</th>
                                    <th style="width: 15%">{{ __('Amount') }}</th>
                                    <th style="width: 15%">{{ __('Payment Type') }}</th>
                                    <th style="width: 30%">{{ __('Note') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $start =
                                        $expenses instanceof \Illuminate\Pagination\LengthAwarePaginator
                                            ? $expenses->firstItem()
                                            : 1;
                                @endphp
                                @forelse ($expenses as $index => $expense)
                                    <tr>
                                        <td>{{ $start + $index }}</td>
                                        <td>{{ $expense->date }}</td>
                                        <td>{{ $expense->createdBy->name }}</td>
                                        <td>{{ $expense->expenseType->name }}</td>
                                        <td>{{ currency($expense->amount) }}</td>
                                        <td>{{ ucfirst($expense->payment_type) }}</td>
                                        <td>{{ $expense->note }}</td>
                                        <td>
                                            @if (checkAdminHasPermission('expense.edit') || checkAdminHasPermission('expense.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $expense->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $expense->id }}">
                                                        @adminCan('expense.edit')
                                                            <a class="dropdown-item" href="javascript:;"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editExpense{{ $expense->id }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('expense.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $expense->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Expense')" route="" create="no" :message="__('No data found!')"
                                        colspan="8"></x-empty-table>
                                @endforelse

                                @if ($expenses->count() > 0)
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <b>{{ __('Total') }}</b>
                                        </td>
                                        <td>
                                            <b>{{ currency($totalAmount) }}</b>
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if (request()->get('par-page') !== 'all')
                        <div class="float-right">
                            {{ $expenses->onEachSide(0)->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- add Expense type --}}
    <div class="modal fade" id="addExpense">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Expense') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body py-0">
                    <form action="{{ route('admin.expense.store') }}" method="POST" id="add-bank-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">{{ __('Date') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker" id="date" name="date"
                                        value="{{ date('d-m-Y') }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('Expense Type') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="expense_type_id" id="" class="form-control select2"
                                        data-dropdown-parent="#addExpense">
                                        <option value="">{{ __('Expense Type') }}</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">{{ __('Payment Type') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="payment_type" id="" class="form-control">
                                        <option value="">{{ __('Payment Type') }}</option>
                                        @foreach (accountList() as $key => $list)
                                            <option value="{{ $key }}">{{ $list }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 accounts">

                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="amount">{{ __('Amount') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="amount" name="amount"
                                        value="{{ old('amount') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="amount">{{ __('Note') }}</label>
                                    <textarea name="note" id="note" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary" form="add-bank-form">{{ __('Save') }}</button>
                </div>

            </div>
        </div>
    </div>

    {{-- edit expense --}}
    @foreach ($expenses as $index => $expense)
        <div class="modal fade" id="editExpense{{ $expense->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Expense') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.expense.update', $expense->id) }}" method="POST"
                            id="edit-type-form{{ $expense->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('Date') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" id="date"
                                            name="date" value="{{ now()->parse($expense->date)->format('d-m-Y') }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Expense Type') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="expense_type_id" id="" class="form-control select2"
                                            data-dropdown-parent="#editExpense{{ $expense->id }}">
                                            <option value="">{{ __('Expense Type') }}</option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ $type->id == $expense->expense_type_id ? 'selected' : '' }}>
                                                    {{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{ __('Payment Type') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="payment_type" id="" class="form-control">
                                            <option value="">{{ __('Payment Type') }}</option>
                                            @foreach (accountList() as $key => $list)
                                                <option value="{{ $key }}"
                                                    {{ $key == $expense->payment_type ? 'selected' : '' }}>
                                                    {{ $list }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 accounts">
                                    <input type="hidden" name="account_id" value="{{ $expense->account_id }}">
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="amount">{{ __('Amount') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="amount" name="amount"
                                            value="{{ $expense->amount }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="amount">{{ __('Note') }}</label>
                                        <textarea name="note" id="note" cols="30" rows="5" class="form-control">{{ $expense->note }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-type-form{{ $expense->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
    @push('js')
        <script>
            $(document).ready(function() {
                const reqType = '{{ request()->type }}';
                if (reqType) {
                    $('#addExpense').modal('show');
                }

                let accounts = @json($accounts);
                $('select[name="payment_type"]').on('change', function() {
                    const paymentType = $(this).val();
                    let html = `<label for="account_id">{{ __('Select Account') }}<span class="text-danger">*</span></label>
                    <select name="account_id" id="" class="form-control form-group">`;
                    const filterAccount = accounts.filter(account => account.account_type === paymentType);
                    html = accountsType(filterAccount, html, paymentType);
                    $('.accounts').html(html);

                    if ($(this).val() == 'cash' || $(this).val() == 'advance') {
                        const cash =
                            `<input type="hidden" name="account_id" class="form-control" value="${$(this).val()}" readonly>`;
                        $('.accounts').html(cash);
                    }
                });
            });

            function deleteData(id) {
                let url = "{{ route('admin.expense.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
