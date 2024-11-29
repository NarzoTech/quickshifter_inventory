@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Vehicles List') }}</title>
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
                                    <button type="submit" class="btn btn-primary w-100">Search</button>
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
                <h4><i class="fas fa-list"></i> {{ __('Vehicles List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addVehicle" class="btn btn-primary"><i
                        class="fa fa-plus"></i>
                    {{ __('Add Vehicle') }}</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Model') }}</th>
                            <th>{{ __('Plate Number') }}</th>
                            <th>{{ __('Color') }}</th>
                            <th>{{ __('Year') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehicles as $index => $vehicle)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $vehicle->name }}</td>
                                <td>{{ $vehicle->model }}</td>
                                <td>{{ $vehicle->plate_number }}</td>
                                <td>{{ $vehicle->color }}</td>
                                <td>{{ $vehicle->year }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $vehicle->id }}" type="button"
                                            class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $vehicle->id }}">
                                            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                data-bs-target="#editVehicle{{ $vehicle->id }}">Edit</a>
                                            <a href="javascript:;" class="dropdown-item"
                                                onclick="deleteData({{ $vehicle->id }})">
                                                Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-table :name="__('Vehicle')" route="" create="no" :message="__('No data found!')"
                                colspan="6"></x-empty-table>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $vehicles->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>


    {{-- add Vehicle --}}
    <div class="modal" id="addVehicle">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Vehicle') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.vehicle.store') }}" method="POST" id="add-Vehicle-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">{{ __('Vehicle Name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="model">{{ __('Model') }}</label>
                                <input type="text" class="form-control" id="model" name="model">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="plate_number">{{ __('Plate Number') }}</label>
                                <input type="text" class="form-control" id="plate_number" name="plate_number">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="color">{{ __('Color') }}</label>
                                <input type="text" class="form-control" id="color" name="color">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="year">{{ __('Year') }}</label>
                                <input type="text" class="form-control" id="year" name="year">
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-Vehicle-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit Vehicle --}}
    @foreach ($vehicles as $index => $vehicle)
        <div class="modal" id="editVehicle{{ $vehicle->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Add Vehicle') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.vehicle.update', $vehicle->id) }}" method="POST"
                            id="edit-Vehicle-form{{ $vehicle->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('Vehicle Name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $vehicle->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="model">{{ __('Model') }}</label>
                                    <input type="text" class="form-control" id="model" name="model"
                                        value="{{ $vehicle->model }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="plate_number">{{ __('Plate Number') }}</label>
                                    <input type="text" class="form-control" id="plate_number" name="plate_number"
                                        value="{{ $vehicle->plate_number }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="color">{{ __('Color') }}</label>
                                    <input type="text" class="form-control" id="color" name="color"
                                        value="{{ $vehicle->color }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="year">{{ __('Year') }}</label>
                                    <input type="text" class="form-control" id="year" name="year"
                                        value="{{ $vehicle->year }}">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-Vehicle-form{{ $vehicle->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection


@push('js')
    <script>
        function deleteData(id) {
            let url = '{{ route('admin.vehicle.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
