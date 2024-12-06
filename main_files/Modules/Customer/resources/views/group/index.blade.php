@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Customer Group List') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-1">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-3">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-3">
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
                            <div class="col-xxl-2 col-md-3">
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

                            <div class="col-xxl-1 col-md-3">
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
                <h4><i class="fas fa-list"></i> {{ __('Customer Group List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addgroup" class="btn btn-primary"><i
                        class="fa fa-plus"></i>
                    {{ __('Add Customer Group') }}</a>
            </div>
        </div>
        <div class="card-body">

            <table style="width: 100%;" class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('SN') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Discount') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customerGroups as $index => $group)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $group->name }}</td>
                            <td>{{ $group->discount }}</td>
                            <td>
                                @if ($group->status == 1)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                @endif
                            <td>
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop{{ $group->id }}" type="button"
                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $group->id }}">
                                        <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                            data-bs-target="#editGroup{{ $group->id }}">Edit</a>
                                        <a href="javascript:;" class="dropdown-item"
                                            onclick="deleteData({{ $group->id }})">
                                            Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-table :name="__('Customer Group')" route="" create="no" :message="__('No data found!')"
                            colspan="6"></x-empty-table>
                    @endforelse
                </tbody>
            </table>

            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $customerGroups->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>





    {{-- add group --}}

    <div class="modal fade" id="addgroup" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Customer Group') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.customerGroup.store') }}" method="POST" id="add-group-form">
                        @csrf
                        <div class="row">
                            <input type="hidden" class="form-control" id="type" name="type" value="customer">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="discount">{{ __('Discount') }}(%)</label>
                                <input type="number" class="form-control" id="discount" name="discount">
                            </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="status">{{ __('Status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                <label for="description">{{ __('Description') }}</label>
                                <input type="text" class="form-control" id="description" name="description">
                            </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-group-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit group --}}
    @foreach ($customerGroups as $index => $group)
        <div class="modal fade" id="editGroup{{ $group->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Customer Group') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.customerGroup.update', $group->id) }}" method="POST"
                            id="edit-group-form{{ $group->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <input type="hidden" class="form-control" id="type" name="type"
                                    value="customer">
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $group->name }}">
                                </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label for="discount">{{ __('Discount') }}(%)</label>
                                    <input type="number" class="form-control" id="discount" name="discount"
                                        value="{{ $group->discount }}">
                                </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" @if ($group->status == 1) selected @endif>
                                            {{ __('Active') }}</option>
                                        <option value="0" @if ($group->status == 0) selected @endif>
                                            {{ __('Inactive') }}</option>
                                    </select>
                                </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                    <label for="description">{{ __('Description') }}</label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="{{ $group->description }}">
                                </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-group-form{{ $group->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('js')
    <script>
        function deleteData(id) {
            let url = '{{ route('admin.customerGroup.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
