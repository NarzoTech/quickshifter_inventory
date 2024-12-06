@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Bank List') }}</title>
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
                                    <button type="submit" class="btn bg-label-danger"><i
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

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"><i class="fas fa-list"></i> {{ __('Bank List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addbank" class="btn btn-primary"><i
                        class="fa fa-plus"></i> {{ __('Add Bank') }}</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banks as $index => $bank)
                            <tr>
                                <td>{{ $loop->first + $index }}</td>
                                <td>{{ $bank->name }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $bank->id }}" type="button"
                                            class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $bank->id }}">
                                            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                data-bs-target="#editbank{{ $bank->id }}">Edit</a>
                                            <a href="javascript:;" class="dropdown-item"
                                                onclick="deleteData({{ $bank->id }})">
                                                Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-table :name="__('Bank')" route="" create="no" :message="__('No data found!')"
                                colspan="3"></x-empty-table>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $banks->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- add bank --}}
    <div class="modal fade" id="addbank">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Bank') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.bank.store') }}" method="POST" id="add-bank-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-bank-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit bank --}}
    @foreach ($banks as $index => $bank)
        <div class="modal fade" id="editbank{{ $bank->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Bank') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.bank.update', $bank->id) }}" method="POST"
                            id="edit-bank-form{{ $bank->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $bank->name }}">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-bank-form{{ $bank->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach



    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.bank.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
