@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Account List') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-1">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
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
                            <div class="col-xxl-2 col-md-4">
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
                            <div class="col-xxl-1 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-label-danger form-reset"><i
                                            class='bx bx-rotate-right'></i></button>

                                    <button type="submit" class="btn bg-label-primary"><i
                                            class='bx bx-search'></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <div class="card mt-3 mb-3">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table mb-3">
                            <tbody>
                                <tr class="theme-primary">
                                    <th class="text-center">Cash Amount</th>
                                </tr>
                                <tr>
                                    <th class="text-center">
                                        <h4 class="header-title">
                                            {{ currency($cashAccount?->getBalanceBetween()) }}
                                        </h4>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card mt-3 mb-3">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table mb-3">
                            <tbody>
                                <tr class="theme-primary">
                                    <th class="text-center">Total Amount</th>
                                </tr>
                                <tr>
                                    <th class="text-center">
                                        <h4 class="header-title">{{ currency($accountBalance) }}</h4>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card mt-3 mb-3">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                        <h4 class="section_title"><i class="fas fa-list"></i> {{ __('Bank Accounts') }}</h4>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table mb-3">
                            <thead>
                                <tr>
                                    <th>{{ __('SN') }}</th>
                                    <th>{{ __('Bank Name') }}</th>
                                    <th>{{ __('Bank Account Type') }}</th>
                                    <th>{{ __('Bank Account Name') }}</th>
                                    <th>{{ __('Bank Account Number') }}</th>
                                    <th>{{ __('Bank Account Branch') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bankAccounts as $index => $account)
                                    <tr>
                                        <td>{{ $loop->first + $index }}</td>
                                        <td>{{ $account?->bank?->name }}</td>
                                        <td>{{ $account->bank_account_type }}</td>
                                        <td>{{ $account->bank_account_name }}</td>
                                        <td>{{ $account->bank_account_number }}</td>
                                        <td>{{ $account->bank_account_branch }}</td>
                                        <td>{{ currency($account->getBalanceBetween()) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button id="btnGroupDrop{{ $account->id }}" type="button"
                                                    class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu"
                                                    aria-labelledby="btnGroupDrop{{ $account->id }}">
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.accounts.edit', $account->id) }}">Edit</a>
                                                    <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal" class="dropdown-item"
                                                        onclick="deleteData({{ $account->id }})">
                                                        Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Bank')" route="" create="no" :message="__('No data found!')"
                                        colspan="8"></x-empty-table>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card mt-3 mb-3">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                        <h4 class="section_title"><i class="fas fa-list"></i> {{ __('Mobile Accounts') }}</h4>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table mb-3">
                            <thead>
                                <tr>
                                    <th>{{ __('SN') }}</th>
                                    <th>{{ __('Mobile Bank Name') }}</th>
                                    <th>{{ __('Mobile Number') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mobileAccounts as $index => $account)
                                    <tr>
                                        <td>{{ $loop->first + $index }}</td>
                                        <td>{{ $account->mobile_bank_name }}</td>
                                        <td>{{ $account->mobile_number }}</td>
                                        <td>{{ currency($account->getBalanceBetween()) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button id="btnGroupDrop{{ $account->id }}" type="button"
                                                    class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu"
                                                    aria-labelledby="btnGroupDrop{{ $account->id }}">
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editbank{{ $account->id }}">Edit</a>
                                                    <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal" class="dropdown-item"
                                                        onclick="deleteData({{ $account->id }})">
                                                        Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Mobile Account')" route="" create="no" :message="__('No data found!')"
                                        colspan="5"></x-empty-table>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card mt-3 mb-3">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                        <h4 class="section_title"><i class="fas fa-list"></i> {{ __('Bank Cards') }}</h4>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table mb-3">
                            <thead>
                                <tr>
                                    <th>{{ __('SN') }}</th>
                                    <th>{{ __('Card Type') }}</th>
                                    <th>{{ __('Bank Name') }}</th>
                                    <th>{{ __('Card Holder Name') }}</th>
                                    <th>{{ __('Card Number') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cardAccounts as $index => $account)
                                    <tr>
                                        <td>{{ $loop->first + $index }}</td>
                                        <td>{{ $account->card_type }}</td>
                                        <td>{{ $account->bank?->name }}</td>
                                        <td>{{ $account->card_holder_name }}</td>
                                        <td>{{ $account->card_number }}</td>
                                        <td>{{ currency($account->getBalanceBetween()) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button id="btnGroupDrop{{ $account->id }}" type="button"
                                                    class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu"
                                                    aria-labelledby="btnGroupDrop{{ $account->id }}">
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editbank{{ $account->id }}">Edit</a>
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $account->id }})">
                                                        Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Card')" route="" create="no" :message="__('No data found!')"
                                        colspan="7"></x-empty-table>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.accounts.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
