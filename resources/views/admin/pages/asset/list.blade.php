@extends('admin.master_layout')
@section('title')
    <title>{{ __('Asset') }}</title>
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
                <h1>{{ __('Asset') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.assets.index') }}" method="GET"
                                    class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group search-wrapper">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="{{ __('Search') }}">

                                                <button type="submit">
                                                <i class="far fa-arrow-alt-circle-right"></i>
                                            </button>
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
                                    <a href="javascript:;" data-toggle="modal" data-target="#addAssetType"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Asset') }}</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th title="Sl">{{ __('Sl') }}</th>
                                                <th title="Date">{{ __('Name') }}</th>
                                                <th title="Date">{{ __('Date') }}</th>
                                                <th title="Category">{{ __('Type') }}</th>
                                                <th title="Pay By">{{ __('Pay By') }}</th>
                                                <th title="Note">{{ __('Note') }}</th>
                                                <th title="Amount">{{ __('Amount') }}</th>
                                                <th title="Action">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($lists as $index => $type)
                                                <tr>
                                                    <td>{{ $loop->first + $index }}</td>
                                                    <td>{{ $type->name }}</td>
                                                    <td>
                                                        {{ date('d-m-Y', strtotime($type->date)) }}
                                                    </td>
                                                    <td>{{ $type->type->name }}</td>
                                                    <td>{{ $type->account->account_type }}</td>
                                                    <td>
                                                        {{ $type->note }}
                                                    </td>
                                                    <td>
                                                        {{ currency($type->amount) }}
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button id="btnGroupDrop{{ $type->id }}" type="button"
                                                                class="btn btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">{{ __('Action') }}</button>
                                                            <div class="dropdown-menu"
                                                                aria-labelledby="btnGroupDrop{{ $type->id }}">
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    data-toggle="modal"
                                                                    data-target="#editType{{ $type->id }}">{{ __('Edit') }}</a>
                                                                <a href="javascript:;" class="dropdown-item"
                                                                    onclick="deleteData({{ $type->id }})">{{ __('Delete') }}</a>
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
                                        {{ $lists->onEachSide(0)->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- add Asset --}}
    <div class="modal" id="addAssetType">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Asset') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">{{ __('×') }}</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.assets.store') }}" method="POST" id="add-asset-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>

                            <div class="form-group col-12">
                                <label for="">{{ __('Date') }}</label>
                                <input type="text" name="date" value="{{ now()->format('d-m-Y') }}"
                                    class="form-control datepicker" required>
                            </div>

                            <div class="form-group col-12">
                                <label for="">{{ __('Asset Category') }}</label>
                                <select name="type_id" class="form-control" required>
                                    <option value="">{{ __('select') }}</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12" style="display: none;">
                                <label for="">{{ __('Branch') }}</label>
                                <select name="branch_id" class="form-control" id="branch_id">
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <div>
                                    <label for="" class="mt-2">{{ __('Payment Type') }}</label>
                                </div>
                                <div>
                                    <select name="payment_type" id="" class="form-control">
                                        <option value="">{{ __('Payment Type') }}</option>
                                        @foreach (accountList() as $key => $list)
                                            <option value="{{ $key }}">
                                                {{ $list }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-12 accounts">

                            </div>

                            <div class="form-group col-12">
                                <label for="">{{ __('Amount') }}</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>

                            <div class="form-group col-12">
                                <label for="">{{ __('Note') }}</label>
                                <textarea name="note" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary" form="add-asset-form">{{ __('Save') }}</button>
                </div>

            </div>
        </div>
    </div>
    {{-- edit Asset --}}
    @foreach ($lists as $index => $type)
        <div class="modal" id="editType{{ $type->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Asset') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">{{ __('×') }}</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.assets.update', $type->id) }}" method="POST"
                            id="edit-type-form{{ $type->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $type->name }}">
                                </div>

                                <div class="form-group col-12">
                                    <label for="">{{ __('Date') }}</label>
                                    <input type="text" name="date" value="{{ $type->date }}"
                                        class="form-control datepicker" required>
                                </div>

                                <div class="form-group col-12">
                                    <label for="">{{ __('Asset Category') }}</label>
                                    <select name="type_id" class="form-control" required>
                                        <option value="">{{ __('select') }}</option>
                                        @foreach ($types as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ $cat->id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-12">
                                    <label for="name">{{ __('Payment Type') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="payment_type" id="" class="form-control">
                                        <option value="">{{ __('Payment Type') }}</option>
                                        @foreach (accountList() as $key => $list)
                                            <option value="{{ $key }}"
                                                {{ $key == $type->payment_type ? 'selected' : '' }}>{{ $list }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-12 accounts">
                                    <input type="hidden" name="account_id" value="{{ $type->account_id }}">
                                </div>
                                <div class="form-group col-12">
                                    <label for="">{{ __('Amount') }}</label>
                                    <input type="number" name="amount" class="form-control" required
                                        value="{{ $type->amount }}">
                                </div>

                                <div class="form-group col-12">
                                    <label for="">{{ __('Note') }}</label>
                                    <textarea name="note" rows="3" class="form-control">{{ $type->note }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-type-form{{ $type->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('js')
    <script>
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
            let url = "{{ route('admin.assets.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
        }
    </script>
@endpush
