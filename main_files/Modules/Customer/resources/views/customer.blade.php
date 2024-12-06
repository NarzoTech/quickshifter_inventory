@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Customers List') }}</title>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-1">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class="fa fa-search"></i>
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
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="From Date" name="from_date"
                                        value="{{ request()->get('from_date') }}" class="form-control datepicker"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="To Date" name="to_date"
                                        value="{{ request()->get('to_date') }}" class="form-control datepicker"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xxl-1 col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 mb-3">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4><i class="fas fa-list"></i> {{ __('Customers List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.customers.import') }}" class="btn btn-primary"><i class="fa fa-upload"></i>
                    {{ __('Import Customers') }}</a>
                <a href="javascript:;" class="btn btn-danger" onclick="deleteAllCustomers()" data-bs-toggle="modal"
                    data-bs-target="#deleteAllCustomers">{{ __('Delete All Customer') }}</a>
                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addCustomer" class="btn btn-primary"> <i
                        class="fa fa-plus"></i>
                    {{ __('Add Customer') }}</a>
                <button type="button" class="btn btn-primary export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn btn-success export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-hover">
                    <thead>
                        <tr>
                            <th rowspan="2">{{ __('SN') }}</th>
                            <th rowspan="2">{{ __('Name') }}</th>
                            <th rowspan="2">{{ __('Phone') }}</th>
                            <th rowspan="2">{{ __('Area') }}</th>
                            <th colspan="4">{{ __('Sale') }}</th>
                            <th colspan="3">{{ __('Sale Return') }}</th>
                            <th rowspan="2">{{ __('Total Due') }}</th>
                            <th rowspan="2">{{ __('Action') }}</th>
                        </tr>
                        <tr>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Pay') }}</th>
                            <th>{{ __('Due') }}</th>
                            {{-- <th>{{ __('Dismiss') }}</th> --}}
                            <th>{{ __('Advance') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Pay') }}</th>
                            <th>{{ __('Due') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr>
                                <td>{{ ++$index }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->area->name }}</td>
                                <td>{{ currency($user->sales->sum('grand_total')) }}</td>
                                <td>{{ currency($user->total_paid) }}</td>
                                <td>{{ currency($user->total_due) }}</td>
                                <td>{{ currency($user->advances()) }}</td>
                                <td>{{ currency($user->saleReturn->sum('return_amount')) }}</td>
                                <td>{{ currency($user->total_sale_return_pay) }}</td>
                                <td>{{ currency($user->total_sale_return_due) }}</td>
                                <td>{{ currency($user->total_due - $user->total_sale_return_due) }}</td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $user->id }}" type="button"
                                            class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $user->id }}">

                                            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                data-bs-target="#showCustomer{{ $user->id }}">Show</a>

                                            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                data-bs-target="#editCustomer{{ $user->id }}">Edit</a>

                                            @if ($user->total_due)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.customer.due-receive') }}?customer={{ $user->id }}">Due
                                                    Receive</a>
                                            @endif

                                            <a class="dropdown-item"
                                                href="{{ route('admin.customer.due-receive') }}?customer={{ $user->id }}">Dismiss</a>


                                            <a class="dropdown-item" href="javascript:;"
                                                onclick="status('{{ $user->id }}')"
                                                data-status="{{ $user->id }}">
                                                {{ $user->status == 1 ? 'Deactivated' : 'Activate' }}
                                            </a>

                                            <a class="dropdown-item"
                                                href="{{ route('admin.sales.index') }}?customer={{ $user->id }}">Sales</a>

                                            <a class="dropdown-item"
                                                href="{{ route('admin.customers.ledger', $user->id) }}">{{ __('Ledger') }}</a>

                                            <a class="dropdown-item"
                                                href="{{ route('admin.customers.advance', $user->id) }}">{{ __('Advance') }}</a>

                                            <a href="javascript:;" class="dropdown-item"
                                                onclick="deleteData({{ $user->id }})">
                                                Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="4" class="text-center fw-bold">
                                {{ __('Total') }}
                            </td>
                            <td>
                                {{ currency($data['totalSale']) }}
                            </td>
                            <td>
                                {{ currency($data['pay']) }}
                            </td>
                            <td>
                                {{ currency($data['total_due']) }}
                            </td>
                            <td>
                                {{ currency($data['total_advance']) }}
                            </td>
                            <td>
                                {{ currency($data['total_return']) }}
                            </td>
                            <td>
                                {{ currency($data['total_return_pay']) }}
                            </td>
                            <td>
                                {{ currency($data['total_return_due']) }}
                            </td>
                            <td colspan="2">
                                {{ currency($data['total_due'] - $data['total_return_due']) }}
                            </td>
                        </tr>
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


    {{-- add customer --}}
    @include('customer::customer-modal')


    {{-- edit customer --}}
    @foreach ($users as $index => $user)
        <div class="modal fade" id="editCustomer{{ $user->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Add Customer') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.customers.update', $user->id) }}" method="POST"
                            id="edit-customer-form{{ $user->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Customer Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $user->name }}">

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_id">{{ __('Customer Group') }}</label>
                                        <select name="group_id" id="group_id" class="form-control">
                                            <option value="">{{ __('Select Group') }}</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}"
                                                    {{ $user->group_id == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone') }}</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ $user->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group">
                                        <label for="email">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $user->email }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="area_id">{{ __('Area') }}</label>
                                        <select name="area_id" id="area_id" class="form-control">
                                            <option value="">{{ __('Select Area') }}</option>
                                            @foreach ($areaList as $list)
                                                <option value="{{ $list->id }}"
                                                    {{ $user->area_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vehicle_id">{{ __('Vehicle') }}</label>
                                        <select name="vehicle_id" id="vehicle_id" class="form-control"
                                            data-dropdown-parent="#addCustomer" data-control="select2">
                                            <option value="">{{ __('Select Vehicle') }}</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    {{ $user->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                                    {{ $vehicle->name }} - {{ $vehicle->model }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="plate_number">{{ __('Plate Number') }}</label>
                                        <input type="text" class="form-control" id="plate_number" name="plate_number"
                                            value="{{ $user->plate_number }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="membership">{{ __('Membership') }}</label>
                                        <input type="text" class="form-control" id="membership" name="membership"
                                            value="{{ $user->membership }}">
                                    </div>
                                </div>

                                <div class="col-md-4 ">
                                    <div class="form-group">
                                        <label for="date">{{ __('Date') }}</label>
                                        <input type="text" class="form-control datepicker" id="date"
                                            name="date" value="{{ $user->date }}" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>
                                                {{ __('Active') }}</option>
                                            <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>
                                                {{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">

                                    <div class="form-group mb-0">
                                        <div class="guest_customer_check">
                                            <label class="switch switch-square">
                                                <input type="checkbox" name="guest" class="switch-input"
                                                    value="1" @if ($user->guest) checked @endif />
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on"><i class="bx bx-check"></i></span>
                                                    <span class="switch-off"><i class="bx bx-x"></i></span>
                                                </span>
                                                <span class="switch-label">{{ __('Guest Customer') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-customer-form{{ $user->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    {{-- Show customer --}}
    @foreach ($users as $index => $user)
        <div class="modal fade" id="showCustomer{{ $user->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Customer') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach



    <div tabindex="-1" role="dialog" id="deleteAllCustomers" class ='modal fade'>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Item Delete Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are You sure want to delete all Customers ?') }}</p>
                    <form id="allDeleteForm" action="{{ route('admin.delete.all-customers') }}" method="POST">

                        @csrf
                        @method('DELETE')
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="password" id="password"
                                        placeholder="Enter Password *">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-whitesmoke br">

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary"
                        form="allDeleteForm">{{ __('Yes, Delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $('.export').on('click', function() {
                // get full url including query string
                var fullUrl = window.location.href;
                if (fullUrl.includes('?')) {
                    fullUrl += '&export=true';
                } else {
                    fullUrl += '?export=true';
                }
                window.location.href = fullUrl;
            })

            function deleteData(id) {
                let url = '{{ route('admin.customers.destroy', ':id') }}';
                url = url.replace(':id', id);
                $("#deleteForm").attr('action', url);
                $('#deleteModal').modal('show');
            }

            function deleteAllCustomers() {
                $("#deleteAllCustomers").attr("action", '{{ route('admin.delete.all-customers') }}')
            }

            function status(id) {
                handleStatus("{{ route('admin.customers.status', '') }}/" + id)

                let status = $('[data-status=' + id + ']').text()
                // remove whitespaces using regex
                status = status.replaceAll(/\s/g, '');
                $('[data-status=' + id + ']').text(status != 'Deactivated' ? 'Deactivated' :
                    'Activate')
            }
        </script>
    @endpush
@endsection
