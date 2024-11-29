@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Other Due') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group search-wrapper">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="Name, phone, company , memo number...">
                                            <button type="submit">
                                                <i class="far fa-arrow-alt-circle-right"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="desc"
                                                    {{ request('order_by') == 'desc' ? 'selected' : '' }}>
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
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="From Date" name="from_date"
                                                value="{{ request()->get('from_date') }}" class="form-control datepicker">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="To Date" name="to_date"
                                                value="{{ request()->get('to_date') }}" class="form-control datepicker">
                                        </div>
                                    </div>
                                    {{-- excel  buttons --}}
                                    <div class="row">
                                        <div class="col-md-4 form-group mx-auto">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button type="button" class="btn btn-secondary export"><i
                                                        class="far fa-file-excel"></i>
                                                    Excel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>
                                    {{ __('Supplier Other Due') }}
                                </h4>
                                <div>
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addSupplier"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Supplier Other Due') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Sl') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Company') }}</th>
                                                <th>{{ __('Phone') }}</th>
                                                <th>{{ __('Memo') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Paid') }}</th>
                                                <th>{{ __('Due') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($summeries as $summery)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ now()->parse($summery->date)->format('d-m-Y') }}</td>
                                                    <td>{{ $summery->supplier->name }}</td>
                                                    <td>{{ $summery->supplier->company }}</td>
                                                    <td>{{ $summery->supplier->phone }}</td>
                                                    <td>{{ $summery->memo_number }}</td>
                                                    <td>{{ $summery->amount }}</td>
                                                    <td>{{ $summery->paid }}</td>
                                                    <td>{{ $summery->due }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-primary mr-2 btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#editSupplier-{{ $summery->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                                                onclick="deleteData({{ $summery->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($summeries->count() > 0)
                                                <tr>
                                                    <td colspan="6" class="text-right">
                                                        Total
                                                    </td>
                                                    <td>
                                                        {{ $data['total_amount'] }}
                                                    </td>
                                                    <td>
                                                        {{ currency($data['total_paid']) }}
                                                    </td>
                                                    <td colspan="2" class="text-left">
                                                        {{ currency($data['total_due']) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $summeries->onEachSide(0)->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>



    <div class="modal fade" id="addSupplier">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Supplier Other Due') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.other-summery.supplier.store') }}" method="POST" id="add-supplier-due">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="supplier_id">{{ __('Supplier Name') }}</label>
                                <select name="supplier_id" id="supplier_id" class="form-control select2"
                                    data-control="select2" data-dropdown-parent="#addSupplier">
                                    <option value="">{{ __('Select Group') }}</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }} -
                                            {{ $supplier->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="date">{{ __('Date') }}</label>
                                <input type="text" class="form-control datepicker" id="date" name="date"
                                    value="{{ date('d-m-Y') }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="memo_number">{{ __('Memo No') }}</label>
                                <input type="text" class="form-control" id="memo_number" name="memo_number">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="amount">{{ __('Total Amount') }}</label>
                                <input type="text" class="form-control" id="amount" name="amount">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="paid">{{ __('Paid') }}</label>
                                <input type="text" class="form-control" id="paid" name="paid">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="due">{{ __('Due') }}</label>
                                <input type="text" class="form-control" id="due" name="due">
                            </div>

                            <div class="form-group col-md-12">
                                <label for="description">{{ __('Description') }}</label>
                                <textarea name="description" id="description" class="form-control height-80px" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-supplier-due">Save</button>
                </div>

            </div>
        </div>
    </div>


    @foreach ($summeries as $summery)
        <div class="modal fade" id="editSupplier-{{ $summery->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Add Supplier Other Due') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.other-summery.supplier.update', $summery->id) }}" method="POST"
                            id="add-supplier-due-{{ $summery->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="supplier_id">{{ __('supplier Name') }}</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2"
                                        data-control="select2" data-dropdown-parent="#editsupplier-{{ $summery->id }}">
                                        <option value="">{{ __('Select Group') }}</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ $supplier->id == $summery->supplier_id ? 'selected' : '' }}>
                                                {{ $supplier->name }} -
                                                {{ $supplier->phone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="date">{{ __('Date') }}</label>
                                    <input type="text" class="form-control datepicker" id="date" name="date"
                                        value="{{ now()->parse($summery->date)->format('d-m-Y') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="memo_number">{{ __('Memo No') }}</label>
                                    <input type="text" class="form-control" id="memo_number" name="memo_number"
                                        value="{{ $summery->memo_number }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="amount">{{ __('Total Amount') }}</label>
                                    <input type="text" class="form-control" id="amount" name="amount"
                                        value="{{ $summery->amount }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="paid">{{ __('Paid') }}</label>
                                    <input type="text" class="form-control" id="paid" name="paid"
                                        value="{{ $summery->paid }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="due">{{ __('Due') }}</label>
                                    <input type="text" class="form-control" id="due" name="due"
                                        value="{{ $summery->due }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="description">{{ __('Description') }}</label>
                                    <textarea name="description" id="description" class="form-control height-80px" rows="3">{{ $summery->description }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="add-supplier-due-{{ $summery->id }}">Save</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection


@push('js')
    <script>
        function deleteData(id) {
            let url = '{{ route('admin.other-summery.supplier.delete', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
