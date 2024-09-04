@extends('admin.master_layout')
@section('title')
    <title>{{ __('Purchase List') }}</title>
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
                <h1>{{ __('Purchase List') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.purchase.index') }}" method="GET" onchange="this.submit()"
                                    class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control"
                                                placeholder="{{ __('Supplier Name, Invoice Or Model No') }}">
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
                                        <div class="col-md-2 form-group">
                                            <select class="form-control select2" name="product_id">
                                                <option value="" selected disabled>{{ __('Product') }}
                                                </option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                        ({{ $product->sku }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="From Date" name="from_date"
                                                value="{{ request()->get('from_date') }}" class="form-control datepicker">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="To Date" name="to_date"
                                                value="{{ request()->get('to_date') }}" class="form-control datepicker">
                                        </div>
                                        <div class="col-md-1 form-group">
                                            <a href="{{ route('admin.purchase.index') }}"
                                                class="btn btn-danger">{{ __('Reset') }}</a>
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
                                    <a href="{{ route('admin.purchase.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i>
                                        {{ __('Add Purchase') }}</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Invoice Number') }}</th>
                                                <th>{{ __('Supplier') }}</th>
                                                <th>{{ __('Total Amount') }}</th>
                                                <th>{{ __('Total Pay') }}</th>
                                                <th>{{ __('Total Due') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchases as $index => $purchase)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>{{ $purchase->purchase_date }}</td>
                                                    <td>{{ $purchase->invoice_number }}</td>
                                                    <td>{{ $purchase->supplier?->name }}</td>
                                                    <td>{{ currency($purchase->total_amount) }}</td>
                                                    <td>{{ currency($purchase->paid_amount) }}</td>
                                                    <td>{{ currency($purchase->due_amount) }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button id="btnGroupDrop{{ $purchase->id }}" type="button"
                                                                class="btn btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu"
                                                                aria-labelledby="btnGroupDrop{{ $purchase->id }}">
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    data-toggle="modal"
                                                                    data-target="#showCustomer{{ $purchase->id }}">Show</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.purchase.invoice', $purchase->id) }}">Invoice</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.purchase.edit', $purchase->id) }}">Edit</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.purchase.return', $purchase->id) }}">Purchase
                                                                    Return</a>
                                                                <a class="dropdown-item" href="#">Sales</a>
                                                                <a href="javascript:;" data-toggle="modal"
                                                                    data-target="#deleteModal" class="dropdown-item"
                                                                    onclick="deleteData({{ $purchase->id }})">
                                                                    Delete</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $purchases->onEachSide(0)->links() }}
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


    {{-- edit customer --}}
    @foreach ($purchases as $index => $purchase)
        <div class="modal" id="editCustomer{{ $purchase->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Add Customer') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.customers.update', $purchase->id) }}" method="POST"
                            id="edit-customer-form{{ $purchase->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('Customer Name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $purchase->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">{{ __('Phone') }}</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ $purchase->phone }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $purchase->email }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="city">{{ __('City') }}</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ $purchase->city }}">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="tax_number">{{ __('Tax Number') }}</label>
                                    <input type="text" class="form-control" id="tax_number" name="tax_number"
                                        value="{{ $purchase->tax_number }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" @if ($purchase->status == 1) selected @endif>
                                            {{ __('Active') }}</option>
                                        <option value="0" @if ($purchase->status == 0) selected @endif>
                                            {{ __('Inactive') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="address">{{ __('Address') }}</label>
                                    <textarea name="address" id="address" class="form-control height-80px" rows="3">{{ $purchase->address }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-customer-form{{ $purchase->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    {{-- Show customer --}}
    @foreach ($purchases as $index => $purchase)
        <div class="modal" id="showCustomer{{ $purchase->id }}">
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
                                        <td>{{ $purchase->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Phone') }}</th>
                                        <td>{{ $purchase->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>{{ $purchase->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('City') }}</th>
                                        <td>{{ $purchase->city }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Tax Number') }}</th>
                                        <td>{{ $purchase->tax_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Status') }}</th>
                                        <td>{{ $purchase->status == 1 ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Address') }}</th>
                                        <td>{{ $purchase->address }}</td>
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
                $("#deleteForm").attr("action", '{{ route('admin.purchase.destroy', '') }}' + "/" + id)
            }
        </script>
    @endpush
@endsection
