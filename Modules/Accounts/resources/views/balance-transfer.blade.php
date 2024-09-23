@extends('admin.master_layout')
@section('title')
    <title>{{ __('Balance Transfer') }}</title>
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
                <h1>{{ __('Balance Transfer') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.customers.index') }}" method="GET" onchange="this.submit()"
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
                                    <a href="javascript:;" data-toggle="modal" data-target="#transferModal"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add New') }}</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <th style=""> # </th>
                                            <th style=""> From Account </th>
                                            <th style=""> To Account </th>
                                            <th style=""> Amount </th>
                                            <th style=""> Added By </th>
                                            {{-- <th style=""> Business </th> --}}
                                            <th style=""> Date </th>
                                            <th style=""> Remark </th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($transfers as $key => $balanceTransfer)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ accountList()[$balanceTransfer->fromAccount->account_type] }}
                                                    </td>
                                                    <td>{{ accountList()[$balanceTransfer->toAccount->account_type] }}</td>
                                                    <td>{{ $balanceTransfer->amount }}</td>
                                                    <td>{{ $balanceTransfer->createdBy->name }}</td>
                                                    {{-- <td>{{ $balanceTransfer->business->name }}</td> --}}
                                                    <td>{{ $balanceTransfer->date }}</td>
                                                    <td>{{ $balanceTransfer->note }}</td>
                                                    <td>
                                                        <a href="javascript:;" data-toggle="modal"
                                                            data-target="#transferModal">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $transfers->onEachSide(0)->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    {{-- create balance transfer modal --}}
    <div class="modal" id="transferModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Create Balance Transfer') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.balance.transfer.store') }}" method="POST" id="add-transfer-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="date">{{ __('Date') }}</label>
                                <input type="text" class="form-control datepicker" id="date" name="date">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount">{{ __('Amount') }}</label>
                                <input type="text" class="form-control" id="amount" name="amount">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="from_account_type">{{ __('From Account Type') }}</label>
                                <select name="from_account_type" id="from_account_type" class="form-control mr-2">
                                    @foreach (accountList() as $key => $list)
                                        <option value="{{ $key }}"
                                            @if ($key == 'cash') selected @endif
                                            data-name="{{ $list }}">
                                            {{ $list }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="to_account_type">{{ __('To Account Type') }}</label>
                                <select name="to_account_type" id="to_account_type" class="form-control mr-2">
                                    @foreach (accountList() as $key => $list)
                                        <option value="{{ $key }}"
                                            @if ($key == 'cash') selected @endif
                                            data-name="{{ $list }}">
                                            {{ $list }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="from_account">{{ __('From Account') }}</label>
                                <select name="from_account" id="from_account" class="form-control">
                                    <option value="cash">{{ __('Cash') }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="to_account">{{ __('To Account') }}</label>
                                <select name="to_account" id="to_account" class="form-control">
                                    <option value="cash">{{ __('Cash') }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="remark">{{ __('Remark') }}</label>
                                <textarea name="note" id="remark" class="form-control height-80px" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-transfer-form">Save</button>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        'use strict';

        $(document).ready(function() {
            const accountsList = @json($accounts);
            $(document).on('change', 'select[name="from_account_type"], select[name="to_account_type"]',
                function() {
                    let placeName = $(this).attr('name');
                    if (placeName) {
                        placeName = placeName.replaceAll('_type', '');
                    }

                    const accounts = accountsList.filter(account => account.account_type == $(this).val());
                    const accountInput = $(`#${placeName}`);
                    if (accounts) {
                        let html = ``;
                        accounts.forEach(account => {
                            switch ($(this).val()) {
                                case 'bank':
                                    html +=
                                        `<option value="${account.id}">${account.bank_account_number} (${account.bank?.name})</option>`;
                                    break;
                                case "mobile_banking":
                                    html +=
                                        `<option value="${account.id}">${account.mobile_number}(${account.mobile_bank_name})</option>`;
                                    break;
                                case 'card':
                                    html +=
                                        `<option value="${account.id}">${account.card_number} (${account.bank?.name})</option>`;
                                    break;
                                default:
                                    break;
                            }

                        });
                        accountInput.html(html);
                    }

                    if ($(this).val() == 'cash') {
                        accountInput.html('');
                        const cash =
                            `<option value="cash">{{ __('Cash') }}</option>`;

                        accountInput.html(cash);
                    }
                });
        })
    </script>
@endpush
