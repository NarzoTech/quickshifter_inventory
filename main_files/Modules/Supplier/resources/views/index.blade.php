@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Suppliers List') }}</title>
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
                                        class="form-control" placeholder="Search...">
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
                                        value="{{ request()->get('from_date') }}" class="form-control datepicker">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-4">
                                <div class="form-group">
                                    <input type="text" placeholder="To Date" name="to_date"
                                        value="{{ request()->get('to_date') }}" class="form-control datepicker">
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
                <h4><i class="fas fa-list"></i> Suppliers List</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addSupplier" class="btn btn-primary"> <i
                        class="fa fa-plus"></i>
                    {{ __('Add Supplier') }}</a>
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
                            <th colspan="2">{{ __('Purchase') }}</th>
                            <th colspan="2">{{ __('Purchase Return') }}</th>
                            <th rowspan="2">{{ __('Total Due') }}</th>
                            <th rowspan="2">{{ __('Advance') }}</th>
                            <th rowspan="2">{{ __('Due Dismiss') }}</th>
                            <th rowspan="2">{{ __('Action') }}</th>
                        </tr>
                        <tr>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Pay') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Pay') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $index => $supplier)
                            @php
                                $totalReturn = $supplier->purchaseReturn->sum('return_amount');
                                $totalReturnPaid = $supplier->purchaseReturn->sum('received_amount');
                            @endphp
                            <tr>
                                <td>{{ ++$index }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->area->name }}</td>
                                <td>{{ currency($supplier->purchases->sum('total_amount')) }}</td>
                                <td>{{ currency($supplier->payments->sum('amount')) }}</td>
                                <td>{{ currency($totalReturn) }}</td>
                                <td>{{ currency($totalReturnPaid) }}</td>
                                <td>{{ currency($supplier->total_due - $totalReturn) }}</td>
                                <td>{{ currency($supplier->advance) }}</td>
                                <td>{{ currency($supplier->total_due_dismiss) }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $supplier->id }}" type="button"
                                            class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $supplier->id }}">
                                            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                data-bs-target="#showSupplier{{ $supplier->id }}">Show</a>
                                            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                data-bs-target="#editSupplier{{ $supplier->id }}">Edit</a>

                                            <a class="dropdown-item"
                                                href="{{ route('admin.suppliers.advance', $supplier->id) }}">{{ __('Advance') }}</a>

                                            <a class="dropdown-item"
                                                href="{{ route('admin.suppliers.ledger', $supplier->id) }}">{{ __('Ledger') }}</a>

                                            <a class="dropdown-item" href="javascript:;"
                                                onclick="status('{{ $supplier->id }}')"
                                                data-status="{{ $supplier->id }}">
                                                {{ $supplier->status == 1 ? 'Deactivated' : 'Activate' }}
                                            </a>

                                            @if ($supplier->total_due - $totalReturn)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.suppliers.due-pay', $supplier->id) }}">{{ __('Pay') }}</a>
                                            @endif
                                            <a class="dropdown-item" href="#">{{ __('Sales') }}</a>
                                            <a href="javascript:;" class="dropdown-item"
                                                onclick="deleteData({{ $supplier->id }})">
                                                Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="4" class="text-right font-weight-bold">
                                Total
                            </td>
                            <td colspan="1">
                                {{ currency($data['totalPurchase']) }}
                            </td>
                            <td colspan="1">
                                {{ currency($data['pay']) }}
                            </td>
                            <td colspan="1">
                                {{ currency($data['total_return']) }}
                            </td>
                            <td colspan="1">
                                {{ currency($data['total_return_pay']) }}
                            </td>
                            <td colspan="1">
                                {{ currency($data['total_due']) }}
                            </td>
                            <td colspan="1">
                                {{ currency($data['total_advance']) }}
                            </td>
                            <td colspan="1">
                                {{ currency($data['total_due_dismiss']) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $suppliers->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- add Supplier --}}

    <div class="modal fade" id="addSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">{{ __('Add Supplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.suppliers.store') }}" method="POST" id="add-supplier-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">{{ __('Supplier Name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company">{{ __('Company') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon11"><i
                                            class="fa fa-briefcase"></i></span>
                                    <input type="text" class="form-control" id="company" name="company">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="phone">{{ __('Phone') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon11"><i
                                            class="fas fa-phone-alt"></i></span>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">{{ __('Email') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon11"><i
                                            class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="group_id">{{ __('Supplier Group') }}</label>
                                <select name="group_id" id="group_id" class="form-select">
                                    <option value="">{{ __('Select Group') }}</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="area_id">{{ __('Area') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon11"><i
                                            class="fas fa-map-marker-alt"></i></span>

                                    <select name="area_id" id="area_id" class="form-control">
                                        <option value="">{{ __('Select Area') }}</option>
                                        @foreach ($areaList as $list)
                                            <option value="{{ $list->id }}">{{ $list->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
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
                            <div class="form-group col-md-4 d-flex justify-content-center align-items-center">
                                <label class="custom-switch mt-2">
                                    <input type="checkbox" name="guest" class="custom-switch-input" value="1">
                                    <span class="custom-switch-indicator"></span>
                                    <label for="guest" class="ml-2">{{ __('Guest Supplier') }}</label>
                                </label>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="address">{{ __('Address') }}</label>
                                <textarea name="address" id="address" class="form-control height-80px" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" form="add-supplier-form">Save</button>
                </div>
            </div>
        </div>
    </div>

    {{-- edit Supplier --}}
    @foreach ($suppliers as $index => $supplier)
        <div class="modal fade" id="editSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Edit Supplier') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST"
                            id="edit-supplier-form{{ $supplier->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('Supplier Name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $supplier->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="company">{{ __('Company') }}</label>
                                    <input type="text" class="form-control" id="company" name="company"
                                        value="{{ $supplier->company }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">{{ __('Phone') }}</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ $supplier->phone }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $supplier->email }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="city">{{ __('City') }}</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ $supplier->city }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="state">{{ __('State') }}</label>
                                    <input type="text" class="form-control" id="state" name="state"
                                        value="{{ $supplier->state }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="country">{{ __('Country') }}</label>
                                    <input type="text" class="form-control" id="country" name="country"
                                        value="{{ $supplier->country }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="tax_number">{{ __('Tax Number') }}</label>
                                    <input type="text" class="form-control" id="tax_number" name="tax_number"
                                        value="{{ $supplier->tax_number }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" @if ($supplier->status == 1) selected @endif>
                                            {{ __('Active') }}</option>
                                        <option value="0" @if ($supplier->status == 0) selected @endif>
                                            {{ __('Inactive') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="address">{{ __('Address') }}</label>
                                    <textarea name="address" id="address" class="form-control height-80px" rows="3">{{ $supplier->address }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success"
                            form="edit-supplier-form{{ $supplier->id }}">{{ __('Update') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    {{-- Show Supplier --}}
    @foreach ($suppliers as $index => $supplier)
        <div class="modal fade" id="showSupplier{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Supplier') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{-- table --}}
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <td>{{ $supplier->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Company') }}</th>
                                        <td>{{ $supplier->company }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Phone') }}</th>
                                        <td>{{ $supplier->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>{{ $supplier->email }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ __('City') }}</th>
                                        <td>{{ $supplier->city }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('State') }}</th>
                                        <td>{{ $supplier->state }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Country') }}</th>
                                        <td>{{ $supplier->country }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Status') }}</th>
                                        <td>{{ $supplier->status == 1 ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Address') }}</th>
                                        <td>{{ $supplier->address }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection


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
            let url = "{{ route('admin.suppliers.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }

        function status(id) {
            handleStatus("{{ route('admin.suppliers.status', '') }}/" + id)

            let status = $('[data-status=' + id + ']').text()
            // remove whitespaces using regex
            status = status.replaceAll(/\s/g, '');
            $('[data-status=' + id + ']').text(status != 'Deactivated' ? 'Deactivated' :
                'Activate')
        }
    </script>
@endpush
