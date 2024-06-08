@extends('admin.master_layout')
@section('title')
    <title>{{ __('Expenses') }}</title>
@endsection

@push('css')
    <style>
        thead tr:nth-child(odd) {
            background-color: lightskyblue;

        }


        thead tr:nth-child(even) {
            background-color: lightpink;
        }

        thead>tr>th {
            /* background-color: lightseagreen; */
            color: white !important;
        }
    </style>
@endpush
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Bank List') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.expense.type.index') }}" method="GET" onchange="this.submit()"
                                    class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="{{ __('Search') }}">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="1" {{ request('order_by') == '1' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="0" {{ request('order_by') == '0' ? 'selected' : '' }}>
                                                    {{ __('DESC') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="par-page" id="par-page" class="form-control">
                                                <option value="">{{ __('Per Page') }}</option>
                                                <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('10') }}
                                                </option>
                                                <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('50') }}
                                                </option>
                                                <option value="100"
                                                    {{ '100' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('100') }}
                                                </option>
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="javascript:;" data-toggle="modal" data-target="#addExpense"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Expense') }}</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($expenses as $index => $expense)
                                                <tr>
                                                    <td>{{ $loop->first + $index }}</td>
                                                    <td>{{ $expense->name }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button id="btnGroupDrop{{ $expense->id }}" type="button"
                                                                class="btn btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu"
                                                                aria-labelledby="btnGroupDrop{{ $expense->id }}">
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    data-toggle="modal"
                                                                    data-target="#editType{{ $expense->id }}">Edit</a>
                                                                <a href="javascript:;" data-toggle="modal"
                                                                    data-target="#deleteModal" class="dropdown-item"
                                                                    onclick="deleteData({{ $expense->id }})">
                                                                    Delete</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('Bank')" route="" create="no"
                                                    :message="__('No data found!')" colspan="6"></x-empty-table>
                                            @endforelse
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
            </div>
        </section>
    </div>

    <x-admin.delete-modal />


    {{-- add Expense type--}}
    <div class="modal" id="addExpense">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Expense') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.expense.store') }}" method="POST" id="add-bank-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="date">{{ __('Date') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" id="date" name="date">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="name">{{ __('Select Type') }}<span class="text-danger">*</span></label>
                                <select name="expense_type_id" id="" class="form-control">
                                    <option value="">{{ __('Select Type') }}</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="name">{{ __('Payment Type') }}<span class="text-danger">*</span></label>
                                <select name="payment_type" id="" class="form-control">
                                    <option value="">{{ __('Payment Type') }}</option>
                                    @foreach (accountList() as $key => $list)
                                        <option value="{{ $key }}">{{ $list }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12 accounts">
                                
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-bank-form">Save</button>
                </div>

            </div>
        </div>
    </div>

    {{-- edit expense --}}
    @foreach ($expenses as $index => $expense)
        <div class="modal" id="editExpense{{ $expense->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Expense') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.expense.update', $expense->id) }}" method="POST"
                            id="edit-type-form{{ $expense->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $expense->name }}">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('Close') }}</button>
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
                const accounts = '@json($accounts)';
                
                $('select[name="payment_type"]').on('change', function() {
                    const paymentType = $(this).val();
                    let html = '';
                    const filterAccount = accounts.filter(account => account.payment_type == paymentType);
                    filterAccount.forEach(account => {
                        html += `
                            <label for="account_id">{{ __('Select Account') }}<span class="text-danger">*</span></label>
                            <select name="account_id" id="" class="form-control">
                                <option value="">{{ __('Select Account') }}</option>
                                <option value="${account.id}">${account.name}</option>
                            </select>
                        `;
                    });
                    $('.accounts').html(html);
                });
            });

            function deleteData(id) {
                $("#deleteForm").attr("action", '{{ route('admin.expense.type.destroy', '') }}' + "/" + id)
            }
        </script>
    @endpush
@endsection
