@extends('admin.master_layout')
@section('title')
    <title>{{ __('Opening Balance') }}</title>
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
                <h1>{{ __('Opening Balance') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-money-bill-alt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Current Balance</h4>
                                </div>
                                <div class="card-body">
                                    ৳50.00
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-money-bill-alt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Deposit</h4>
                                </div>
                                <div class="card-body">
                                    ৳50.00
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-money-bill-alt"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Withdraw</h4>
                                </div>
                                <div class="card-body">
                                    ৳50.00
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-5">
                        <div class="card-box">
                            <div class="card card-statistic-1">
                                <div class="card-header">

                                    <h4 class="header-title m-t-0 m-b-30 mb-4">Balance</h4>
                                </div>

                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.opening-balance.update', $balance->id) }}"
                                        class="">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <label for="">Balance Type</label>
                                            <select name="balance_type" class="form-control" required>
                                                <option value="deposit"
                                                    {{ $balance->balance_type == 'deposit' ? 'selected' : '' }}>Deposit
                                                </option>
                                                <option value="withdraw"
                                                    {{ $balance->balance_type == 'withdraw' ? 'selected' : '' }}>Withdraw
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Date</label>
                                            <input type="date" class="form-control" name="date"
                                                value="{{ $balance->date }}" required>
                                        </div>

                                        {{-- <div class="form-group" style="display: none;">
                                            <label for="product_locations">Branch <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="bussiness_id" name="bussiness_id"
                                                required>
                                                <option value="610" selected>Quick Shifter
                                                </option>
                                            </select>
                                        </div> --}}

                                        <div class="form-group">
                                            <label for="">Account Type</label>
                                            <select name="payment_type" id="" class="form-control">
                                                <option value="">{{ __('Payment Type') }}</option>
                                                @foreach (accountList() as $key => $list)
                                                    <option value="{{ $key }}"
                                                        {{ $balance->payment_type == $key ? 'selected' : '' }}>
                                                        {{ $list }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group accounts">
                                            <input type="hidden" name="account_id" value="{{ $balance->account_id }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="">Amount</label>
                                            <input type="text" class="form-control" name="amount" required
                                                placeholder="Amount" autocomplete="off" value="{{ $balance->amount }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="">Remark</label>
                                            <textarea name="note" rows="2" class="form-control" placeholder="Note">{{ $balance->note }}</textarea>
                                        </div>

                                        <div class="text-right">
                                            <button class="btn btn-success" type="submit">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a href="#home1" class="btn btn-success" data-toggle="tab" aria-expanded="false"
                                    class="nav-link active">
                                    Deposit
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#profile1" class="btn btn-info ml-2" data-toggle="tab" aria-expanded="true"
                                    class="nav-link">
                                    Withdraw
                                </a>
                            </li>
                        </ul>
                        <div class="card">
                            <div class="card-body">
                                <div class="tab-content px-0">
                                    <div role="tabpanel" class="tab-pane fade active show" id="home1">
                                        <div class="card-box">
                                            <h4 class="header-title m-t-0 m-b-30 mb-4">Deposit History</h4>
                                            <div class="table-responsive table-invoice">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('SN') }}</th>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Note') }}</th>
                                                            <th>{{ __('Amount') }}</th>
                                                            <th>{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($deposits as $deposit)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $deposit->date }}</td>
                                                                <td>{{ $deposit->note }}</td>
                                                                <td>{{ currency($deposit->amount) }}</td>
                                                                <td>
                                                                    <a href="{{ route('admin.opening-balance.edit', $deposit->id) }}"
                                                                        class="btn btn-primary btn-sm">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0)"
                                                                        onclick="deleteData({{ $deposit->id }})"
                                                                        class="btn btn-danger btn-sm" data-toggle="modal"
                                                                        data-target="#deleteModal">
                                                                        <i class="fas fa-trash"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            {{-- @if (request()->get('par-page') !== 'all')
                                                <div class="float-right">
                                                    {{ $accounts->onEachSide(0)->links() }}
                                                </div>
                                            @endif --}}
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade" id="profile1">
                                        <div class="card-box">
                                            <h4 class="header-title m-t-0 m-b-30 mb-4">Withdraw History</h4>
                                            <div class="table-responsive table-invoice">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('SN') }}</th>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Note') }}</th>
                                                            <th>{{ __('Amount') }}</th>
                                                            <th>{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($withdraws as $withdraw)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $withdraw->date }}</td>
                                                                <td>{{ $withdraw->note }}</td>
                                                                <td>{{ $withdraw->amount }}</td>
                                                                <td>
                                                                    <a href="{{ route('admin.opening-balance.edit', $deposit->id) }}"
                                                                        class="btn btn-primary btn-sm">Edit</a>
                                                                    <a href="javascript:void(0)"
                                                                        onclick="deleteData({{ $withdraw->id }})"
                                                                        data-toggle="modal" data-target="#deleteModal"
                                                                        class="btn btn-danger btn-sm">Delete</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <x-admin.delete-modal />
@endsection
@push('js')
    <script>
        'use strict';
        $(document).ready(function() {

            let accounts = @json($accounts);
            $('select[name="payment_type"]').on('change', function() {
                const paymentType = $(this).val();
                console.log(paymentType);
                let html = `<label for="account_id">{{ __('Select Account') }}<span class="text-danger">*</span></label>
                    <select name="account_id" id="" class="form-control">`;
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
            $("#deleteForm").attr("action", '{{ route('admin.opening-balance.destroy', '') }}' + "/" + id)
        }
    </script>
@endpush
