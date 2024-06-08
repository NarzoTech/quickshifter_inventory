@extends('admin.master_layout')
@section('title')
    <title>{{ __('All Customers') }}</title>
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
                <h1>{{ __('All Customers') }}</h1>
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
                                    <a href="javascript:;" data-toggle="modal" data-target="#addCustomer"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Customer') }}</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">{{ __('SN') }}</th>
                                                <th rowspan="2">{{ __('Name') }}</th>
                                                <th rowspan="2">{{ __('Phone') }}</th>
                                                <th colspan="4">{{ __('Total Sale') }}</th>
                                                <th colspan="3">{{ __('Total Sale Return') }}</th>
                                                <th rowspan="2">{{ __('Total Due') }}</th>
                                                <th rowspan="2">{{ __('Action') }}</th>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Pay') }}</th>
                                                <th>{{ __('Due') }}</th>
                                                <th>{{ __('Advance') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Pay') }}</th>
                                                <th>{{ __('Due') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($users as $index => $user)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->phone }}</td>
                                                    <td>{{ currency($user->total_sale) }}</td>
                                                    <td>{{ currency($user->total_sale_pay) }}</td>
                                                    <td>{{ currency($user->total_sale_due) }}</td>
                                                    <td>{{ currency($user->total_sale_advance) }}</td>
                                                    <td>{{ currency($user->total_sale_return) }}</td>
                                                    <td>{{ currency($user->total_sale_return_pay) }}</td>
                                                    <td>{{ currency($user->total_sale_return_due) }}</td>
                                                    <td>{{ currency($user->total_due) }}</td>

                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button id="btnGroupDrop{{ $user->id }}" type="button"
                                                                class="btn btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu"
                                                                aria-labelledby="btnGroupDrop{{ $user->id }}">
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    data-toggle="modal"
                                                                    data-target="#showCustomer{{ $user->id }}">Show</a>
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    data-toggle="modal"
                                                                    data-target="#editCustomer{{ $user->id }}">Edit</a>
                                                                <a class="dropdown-item" href="#">Sales</a>
                                                                <a href="javascript:;" data-toggle="modal"
                                                                    data-target="#deleteModal" class="dropdown-item"
                                                                    onclick="deleteData({{ $user->id }})">
                                                                    Delete</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('Customer')" route="" create="no"
                                                    :message="__('No data found!')" colspan="6"></x-empty-table>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $users->onEachSide(0)->links() }}
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

    {{-- add customer --}}
    <div class="modal" id="addCustomer">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Customer') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.customers.store') }}" method="POST" id="add-customer-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">{{ __('Customer Name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="group_id">{{ __('Customer Group') }}</label>
                                <select name="group_id" id="group_id" class="form-control">
                                    <option value="">{{ __('Select Group') }}</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="form-group col-md-4 ">
                                <label for="email">{{ __('Email') }}</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="area_id">{{ __('Area') }}</label>
                                <select name="area_id" id="area_id" class="form-control">
                                    <option value="">{{ __('Select Area') }}</option>
                                    @foreach ($areaList as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 ">
                                <label for="vehicle_id">{{ __('Vehicle') }}</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-control">
                                    <option value="">{{ __('Select Vehicle') }}</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="membership">{{ __('Membership') }}</label>
                                <input type="text" class="form-control" id="membership" name="membership">
                            </div>
                            <div class="form-group col-md-4 ">
                                <label for="date">{{ __('Date') }}</label>
                                <input type="text" class="form-control datepicker" id="date" name="date">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="status">{{ __('Status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="address">{{ __('Address') }}</label>
                                <textarea name="address" id="address" class="form-control height-80px" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-customer-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit customer --}}
    @foreach ($users as $index => $user)
        <div class="modal" id="editCustomer{{ $user->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Add Customer') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.customers.update', $user->id) }}" method="POST"
                            id="edit-customer-form{{ $user->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('Customer Name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $user->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">{{ __('Phone') }}</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ $user->phone }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $user->email }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="city">{{ __('City') }}</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ $user->city }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="tax_number">{{ __('Tax Number') }}</label>
                                    <input type="text" class="form-control" id="tax_number" name="tax_number"
                                        value="{{ $user->tax_number }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" @if ($user->status == 1) selected @endif>
                                            {{ __('Active') }}</option>
                                        <option value="0" @if ($user->status == 0) selected @endif>
                                            {{ __('Inactive') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="address">{{ __('Address') }}</label>
                                    <textarea name="address" id="address" class="form-control height-80px" rows="3">{{ $user->address }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-customer-form{{ $user->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    {{-- Show customer --}}
    @foreach ($users as $index => $user)
        <div class="modal" id="showCustomer{{ $user->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Customer') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="row">
                            {{-- table --}}
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Phone') }}</th>
                                        <td>{{ $user->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('City') }}</th>
                                        <td>{{ $user->city }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Tax Number') }}</th>
                                        <td>{{ $user->tax_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Status') }}</th>
                                        <td>{{ $user->status == 1 ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Address') }}</th>
                                        <td>{{ $user->address }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    @push('js')
        <script>
            function deleteData(id) {
                $("#deleteForm").attr("action", '{{ route('admin.customers.destroy', '') }}' + "/" + id)
            }
        </script>
    @endpush
@endsection
