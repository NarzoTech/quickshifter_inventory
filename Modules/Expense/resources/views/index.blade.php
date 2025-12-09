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
                                    <select name="payment_status" id="payment_status" class="form-control">
                                        <option value="">{{ __('Payment Status') }}</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>
                                            {{ __('Paid') }}</option>
                                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>
                                            {{ __('Partial Paid') }}</option>
                                        <option value="due" {{ request('payment_status') == 'due' ? 'selected' : '' }}>
                                            {{ __('Due') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="expense_supplier_id" id="expense_supplier_id" class="form-control">
                                        <option value="">{{ __('All Suppliers') }}</option>
                                        @foreach ($expenseSuppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('expense_supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
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
                                    <th style="width: 4%">{{ __('Sl') }}</th>
                                    <th style="width: 8%">{{ __('Invoice') }}</th>
                                    <th style="width: 8%">{{ __('Date') }}</th>
                                    <th style="width: 12%">{{ __('Supplier') }}</th>
                                    <th style="width: 12%">{{ __('Type') }}</th>
                                    <th style="width: 8%">{{ __('Amount') }}</th>
                                    <th style="width: 8%">{{ __('Paid') }}</th>
                                    <th style="width: 8%">{{ __('Due') }}</th>
                                    <th style="width: 8%">{{ __('Status') }}</th>
                                    <th style="width: 12%">{{ __('Memo') }}</th>
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
                                        <td>{{ $expense->invoice ?? '-' }}</td>
                                        <td>{{ $expense->date }}</td>
                                        <td>{{ $expense->expenseSupplier->name ?? '-' }}</td>
                                        <td>{{ $expense->expenseType->name }}</td>
                                        <td>{{ currency($expense->amount) }}</td>
                                        <td>{{ currency($expense->paid_amount) }}</td>
                                        <td>{{ currency($expense->due_amount) }}</td>
                                        <td>
                                            @php $status = $expense->payment_status_label; @endphp
                                            @if($status == 'paid')
                                                <span class="badge bg-success">{{ __('Paid') }}</span>
                                            @elseif($status == 'partial')
                                                <span class="badge bg-warning">{{ __('Partial') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Due') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $expense->memo }}</td>
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
                                        colspan="11"></x-empty-table>
                                @endforelse

                                @if ($expenses->count() > 0)
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <b>{{ __('Total') }}</b>
                                        </td>
                                        <td>
                                            <b>{{ currency($totalAmount) }}</b>
                                        </td>
                                        <td>
                                            <b>{{ currency($totalPaid) }}</b>
                                        </td>
                                        <td>
                                            <b>{{ currency($totalDue) }}</b>
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
                                        value="{{ date('d-m-Y') }}" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expense_supplier_id_add">{{ __('Expense Supplier') }}</label>
                                    <select name="expense_supplier_id" id="expense_supplier_id_add" class="form-control select2"
                                        data-dropdown-parent="#addExpense">
                                        <option value="">{{ __('Select Supplier (Optional)') }}</option>
                                        @foreach ($expenseSuppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expense_type_id">{{ __('Expense Type') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="expense_type_id" id="expense_type_id" class="form-control select2"
                                        data-dropdown-parent="#addExpense" required>
                                        <option value="">{{ __('Select Expense Type') }}</option>
                                        @foreach ($types as $type)
                                            @if ($type->parent_id)
                                                @continue
                                            @endif
                                            <option value="{{ $type->id }}"
                                                data-has-children="{{ $type->children->count() > 0 ? '1' : '0' }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 sub-expense-wrapper" style="display: none;">
                                <div class="form-group">
                                    <label for="sub_expense_type_id">{{ __('Sub Expense Type') }}</label>
                                    <select name="sub_expense_type_id" id="sub_expense_type_id"
                                        class="form-control select2" data-dropdown-parent="#addExpense">
                                        <option value="">{{ __('Select Sub Expense Type') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_type">{{ __('Payment Type') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="payment_type" id="payment_type" class="select2" required
                                        data-dropdown-parent="#addExpense">
                                        <option value="">{{ __('Payment Type') }}</option>
                                        @foreach (accountList() as $key => $list)
                                            <option value="{{ $key }}">{{ $list }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 accounts">

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">{{ __('Total Amount') }}<span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                        value="{{ old('amount') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 paid-amount-wrapper" style="display: none;">
                                <div class="form-group">
                                    <label for="paid_amount">{{ __('Paid Amount') }}</label>
                                    <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount"
                                        value="0">
                                    <small class="text-muted">{{ __('Leave empty or 0 for full due') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="note">{{ __('Note') }}</label>
                                    <textarea name="note" id="note" cols="30" rows="2" class="form-control" placeholder="{{ __('Enter note (optional)') }}"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="memo">{{ __('Memo') }}</label>
                                    <textarea name="memo" id="memo" cols="30" rows="2" class="form-control" placeholder="{{ __('Enter memo (optional)') }}"></textarea>
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
                                            autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="expense_type_id_edit_{{ $expense->id }}">{{ __('Expense Type') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="expense_type_id" id="expense_type_id_edit_{{ $expense->id }}"
                                            class="form-control select2 expense-type-edit"
                                            data-dropdown-parent="#editExpense{{ $expense->id }}"
                                            data-expense-id="{{ $expense->id }}" required>
                                            <option value="">{{ __('Select Expense Type') }}</option>
                                            @foreach ($types as $type)
                                                @if ($type->parent_id)
                                                    @continue
                                                @endif
                                                <option value="{{ $type->id }}"
                                                    data-has-children="{{ $type->children->count() > 0 ? '1' : '0' }}"
                                                    {{ $type->id == $expense->expense_type_id ? 'selected' : '' }}>
                                                    {{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 sub-expense-wrapper-{{ $expense->id }}"
                                    style="display: {{ $expense->expenseType->children->count() > 0 ? 'block' : 'none' }};">
                                    <div class="form-group">
                                        <label
                                            for="sub_expense_type_id_edit_{{ $expense->id }}">{{ __('Sub Expense Type') }}</label>
                                        <select name="sub_expense_type_id"
                                            id="sub_expense_type_id_edit_{{ $expense->id }}"
                                            class="form-control select2"
                                            data-dropdown-parent="#editExpense{{ $expense->id }}">
                                            <option value="">{{ __('Select Sub Expense Type') }}</option>
                                            @foreach ($expense->expenseType->children as $child)
                                                <option value="{{ $child->id }}"
                                                    {{ $expense->sub_expense_type_id == $child->id ? 'selected' : '' }}>
                                                    {{ $child->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="payment_type">{{ __('Payment Type') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="payment_type" id="payment_type" class="select2" required
                                            data-dropdown-parent="#editExpense{{ $expense->id }}"
                                            data-expense-id="{{ $expense->id }}">
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
                                    @if ($expense->payment_type == 'cash' || $expense->payment_type == 'advance')
                                        <input type="hidden" name="account_id" value="{{ $expense->payment_type }}">
                                    @elseif($expense->account_id)
                                        <div class="form-group">
                                            <label for="account_id">{{ __('Select Account') }}<span
                                                    class="text-danger">*</span></label>
                                            <select name="account_id" id="account_id_edit_{{ $expense->id }}"
                                                class="form-control" required>
                                                <option value="">{{ __('Select Account') }}</option>
                                                @foreach ($accounts->where('account_type', $expense->payment_type) as $account)
                                                    @if ($expense->payment_type == 'bank')
                                                        <option value="{{ $account->id }}"
                                                            {{ $expense->account_id == $account->id ? 'selected' : '' }}>
                                                            {{ $account->bank_account_number }}
                                                            ({{ $account->bank->name ?? 'N/A' }})
                                                        </option>
                                                    @elseif($expense->payment_type == 'mobile_banking')
                                                        <option value="{{ $account->id }}"
                                                            {{ $expense->account_id == $account->id ? 'selected' : '' }}>
                                                            {{ $account->mobile_number }}
                                                            ({{ $account->mobile_bank_name }})
                                                        </option>
                                                    @elseif($expense->payment_type == 'card')
                                                        <option value="{{ $account->id }}"
                                                            {{ $expense->account_id == $account->id ? 'selected' : '' }}>
                                                            {{ $account->card_number }}
                                                            ({{ $account->bank->name ?? 'N/A' }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="amount">{{ __('Amount') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="amount" name="amount"
                                            value="{{ $expense->amount }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="note">{{ __('Note') }}</label>
                                        <textarea name="note" id="note" cols="30" rows="2" class="form-control" placeholder="{{ __('Enter note (optional)') }}">{{ $expense->note }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="memo">{{ __('Memo') }}</label>
                                        <textarea name="memo" id="memo" cols="30" rows="2" class="form-control" placeholder="{{ __('Enter memo (optional)') }}">{{ $expense->memo }}</textarea>
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
                $('#addExpense').on('shown.bs.modal', function() {
                    $(this).find('select[name="payment_type"]').select2({
                        dropdownParent: $('#addExpense')
                    });
                });

                @foreach ($expenses as $expense)
                    $('#editExpense{{ $expense->id }}').on('shown.bs.modal', function() {
                        $(this).find('select[name="payment_type"]').select2({
                            dropdownParent: $('#editExpense{{ $expense->id }}')
                        });
                    });
                @endforeach
                const reqType = '{{ request()->type }}';
                if (reqType) {
                    $('#addExpense').modal('show');
                }

                let accounts = @json($accounts);
                let expenseTypes = @json($types);

                // Handle expense type change for ADD modal
                $('#expense_type_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const hasChildren = selectedOption.data('has-children');
                    const expenseTypeId = $(this).val();

                    if (hasChildren == '1' && expenseTypeId) {
                        loadSubExpenseTypes(expenseTypeId, '#sub_expense_type_id', '#addExpense');
                        $('.sub-expense-wrapper').slideDown();
                    } else {
                        $('.sub-expense-wrapper').slideUp();
                        $('#sub_expense_type_id').val('');
                    }
                });

                // Handle expense type change for EDIT modals
                $('.expense-type-edit').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const hasChildren = selectedOption.data('has-children');
                    const expenseTypeId = $(this).val();
                    const expenseId = $(this).data('expense-id');
                    const subExpenseSelect = `#sub_expense_type_id_edit_${expenseId}`;
                    const parentModal = `#editExpense${expenseId}`;

                    if (hasChildren == '1' && expenseTypeId) {
                        loadSubExpenseTypes(expenseTypeId, subExpenseSelect, parentModal);
                        $(`.sub-expense-wrapper-${expenseId}`).slideDown();
                    } else {
                        $(`.sub-expense-wrapper-${expenseId}`).slideUp();
                        $(subExpenseSelect).val('');
                    }
                });

                // Function to load sub expense types via AJAX
                function loadSubExpenseTypes(parentId, selectElement, parentModal) {
                    // Filter children from expenseTypes
                    const children = expenseTypes.filter(type => type.parent_id == parentId);

                    let options = '<option value="">{{ __('Select Sub Expense Type') }}</option>';
                    children.forEach(child => {
                        options += `<option value="${child.id}">${child.name}</option>`;
                    });

                    $(selectElement).html(options);

                    // Reinitialize select2 if it's being used
                    if ($(selectElement).hasClass('select2')) {
                        $(selectElement).select2({
                            dropdownParent: $(parentModal)
                        });
                    }
                }

                // Payment type handling
                $('select[name="payment_type"]').on('change', function() {
                    const paymentType = $(this).val();

                    if (paymentType == '') {
                        $('.accounts').html('');
                        return;
                    }

                    let html = `<label for="account_id">{{ __('Select Account') }}<span class="text-danger">*</span></label>
                    <select name="account_id" id="account_id" class="form-control form-group" required>`;
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

            // Show/hide paid amount field based on supplier selection
            $('#expense_supplier_id_add').on('change', function() {
                if ($(this).val()) {
                    $('.paid-amount-wrapper').slideDown();
                } else {
                    $('.paid-amount-wrapper').slideUp();
                    $('#paid_amount').val('');
                }
            });
        </script>
    @endpush
@endsection
