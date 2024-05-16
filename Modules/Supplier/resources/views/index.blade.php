@extends('admin.master_layout')
@section('title')
    <title>{{ __('All suppliers') }}</title>
@endsection

@push('css')
    <style>
        thead tr:nth-child(odd) {
            background-color: lightskyblue;

        }


        thead tr:nth-child(even) {
            background-color: lightpink;
        }

        thead > tr > th {
            /* background-color: lightseagreen; */
            color: white !important;
        }
    </style>
@endpush
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('All suppliers') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.suppliers.index') }}" method="GET" onchange="this.submit()"
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
                                    <a href="javascript:;" data-toggle="modal" data-target="#addSupplier"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Supplier') }}</a>
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
                                                <th colspan="2">{{ __('Purchase') }}</th>
                                                <th colspan="2">{{ __('Purchase Return') }}</th>
                                                <th rowspan="2">{{ __('Total Due') }}</th>
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
                                            @forelse ($suppliers as $index => $supplier)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>{{ $supplier->name }}</td>
                                                    <td>{{ $supplier->phone }}</td>
                                                    <td>{{ currency($supplier->total_sale) }}</td>
                                                    <td>{{ currency($supplier->total_sale_pay) }}</td>
                                                    <td>{{ currency($supplier->total_sale_due) }}</td>
                                                    <td>{{ currency($supplier->total_sale_advance) }}</td>
                                                    <td>{{ currency($supplier->total_sale_return) }}</td>
                                                    <td>{{ currency($supplier->total_sale_return_pay) }}</td>
                                                    <td>{{ currency($supplier->total_sale_return_due) }}</td>
                                                    <td>{{ currency($supplier->total_due) }}</td>

                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button id="btnGroupDrop{{ $supplier->id }}" type="button"
                                                                class="btn btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $supplier->id }}">
                                                                <a class="dropdown-item" href="javascript:;" data-toggle="modal" data-target="#showSupplier{{ $supplier->id }}">Show</a>
                                                                <a class="dropdown-item" href="javascript:;" data-toggle="modal" data-target="#editSupplier{{ $supplier->id }}">Edit</a>
                                                                <a class="dropdown-item" href="#">Sales</a>
                                                                <a href="javascript:;" data-toggle="modal"
                                                            data-target="#deleteModal" class="dropdown-item"
                                                            onclick="deleteData({{ $supplier->id }})">
                                                                Delete</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('Supplier')" route="" create="no"
                                                    :message="__('No data found!')" colspan="6"></x-empty-table>
                                            @endforelse
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
                    </div>
                </div>
            </div>
        </section>
    </div>

    <x-admin.delete-modal />

    {{-- add Supplier --}}
    <div class="modal" id="addSupplier">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Supplier') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.suppliers.store') }}" method="POST" id="add-supplier-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">{{ __('Supplier Name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="form-group col-md-6 ">
                                <label for="email">{{ __('Email') }}</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group col-md-6 ">
                                <label for="city">{{ __('City') }}</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                            <div class="form-group col-md-6 ">
                                <label for="tax_number">{{ __('Tax Number') }}</label>
                                <input type="text" class="form-control" id="tax_number" name="tax_number">
                            </div>
                            <div class="form-group col-md-6">
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
                    <button type="submit" class="btn btn-primary" form="add-supplier-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit Supplier --}}
    @foreach ($suppliers as $index => $supplier)
        <div class="modal" id="editSupplier{{ $supplier->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Add Supplier') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
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

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-supplier-form{{ $supplier->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    {{-- Show Supplier --}}
    @foreach ($suppliers as $index => $supplier)
        <div class="modal" id="showSupplier{{ $supplier->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Supplier') }}</h4>
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
                                        <td>{{ $supplier->name }}</td>
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
                                        <th>{{ __('Tax Number') }}</th>
                                        <td>{{ $supplier->tax_number }}</td>
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
                $("#deleteForm").attr("action", '{{ route('admin.suppliers.destroy', '') }}' + "/" + id)
            }
        </script>
    @endpush
@endsection
