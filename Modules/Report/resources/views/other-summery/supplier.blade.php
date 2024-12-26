@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Other Due') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-6 col-lg-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
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
                            <div class="col-xxl-2 col-md-6 col-lg-4">
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
                            <div class="col-xxl-3 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-label-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header">
            <div class="card-header-title">
                <h4 class="section_title"> Supplier Other Due List</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addSupplier" class="btn btn-primary"><i
                        class="fa fa-plus"></i>
                    {{ __('Add Supplier Other Due') }}</a>

                <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table">
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
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $summery->id }}" type="button"
                                            class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $summery->id }}">

                                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                                data-bs-target="#editCustomer-{{ $summery->id }}"
                                                class="dropdown-item">{{ __('Edit') }}</a>
                                            <a href="javascript:;" class="dropdown-item"
                                                onclick="deleteData({{ $summery->id }})">{{ __('Delete') }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if ($summeries->count() > 0)
                            <tr>
                                <td colspan="6" class="text-center">
                                    <b>Total</b>
                                </td>
                                <td>
                                    <b>{{ $data['total_amount'] }}</b>
                                </td>
                                <td>
                                    <b>{{ currency($data['total_paid']) }}</b>
                                </td>
                                <td colspan="2" class="text-left">
                                    <b>{{ currency($data['total_due']) }}</b>
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



    <div class="modal fade" id="addSupplier">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Supplier Other Due') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body py-0">
                    <form action="{{ route('admin.other-summery.supplier.store') }}" method="POST"
                        id="add-supplier-due">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
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
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="date">{{ __('Date') }}</label>
                                    <input type="text" class="form-control datepicker" id="date" name="date"
                                        value="{{ date('d-m-Y') }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="memo_number">{{ __('Memo No') }}</label>
                                    <input type="text" class="form-control" id="memo_number" name="memo_number">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="amount">{{ __('Total Amount') }}</label>
                                    <input type="text" class="form-control" id="amount" name="amount">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="paid">{{ __('Paid') }}</label>
                                    <input type="text" class="form-control" id="paid" name="paid">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="due">{{ __('Due') }}</label>
                                    <input type="text" class="form-control" id="due" name="due">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">{{ __('Description') }}</label>
                                    <textarea name="description" id="description" class="form-control height-80px" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-supplier-due">Save</button>
                </div>

            </div>
        </div>
    </div>


    @foreach ($summeries as $index => $summery)
        <div class="modal fade" id="editSupplier-{{ $summery->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Supplier Other Due') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.other-summery.supplier.update', $summery->id) }}" method="POST"
                            id="add-supplier-due-{{ $summery->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="supplier_id">{{ __('supplier Name') }}</label>
                                        <select name="supplier_id" id="supplier_id-{{ $summery->id }}"
                                            class="form-control select2" data-control="select2"
                                            data-dropdown-parent="#editSupplier-{{ $summery->id }}">
                                            <option value="">{{ __('Select Group') }}</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ $supplier->id == $summery->supplier_id ? 'selected' : '' }}>
                                                    {{ $supplier->name }} -
                                                    {{ $supplier->phone }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="date">{{ __('Date') }}</label>
                                        <input type="text" class="form-control datepicker" id="date"
                                            name="date" value="{{ now()->parse($summery->date)->format('d-m-Y') }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="memo_number">{{ __('Memo No') }}</label>
                                        <input type="text" class="form-control" id="memo_number" name="memo_number"
                                            value="{{ $summery->memo_number }}">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="amount">{{ __('Total Amount') }}</label>
                                        <input type="text" class="form-control" id="amount" name="amount"
                                            value="{{ $summery->amount }}">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="paid">{{ __('Paid') }}</label>
                                        <input type="text" class="form-control" id="paid" name="paid"
                                            value="{{ $summery->paid }}">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="due">{{ __('Due') }}</label>
                                        <input type="text" class="form-control" id="due" name="due"
                                            value="{{ $summery->due }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">{{ __('Description') }}</label>
                                        <textarea name="description" id="description" class="form-control height-80px" rows="3">{{ $summery->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
