@extends('admin.master_layout')
@section('title')
    <title>{{ __('Customer Group List') }}</title>
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
                <h1>{{ __('All Vehicles') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.customerGroup.index') }}" method="GET"
                                    onchange="this.submit()" class="card-body">
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
                                    <a href="javascript:;" data-toggle="modal" data-target="#addVehicle"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Customer Group') }}</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
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
                                                                class="btn btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu"
                                                                aria-labelledby="btnGroupDrop{{ $group->id }}">
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    data-toggle="modal"
                                                                    data-target="#editVehicle{{ $group->id }}">Edit</a>
                                                                <a href="javascript:;" data-toggle="modal"
                                                                    data-target="#deleteModal" class="dropdown-item"
                                                                    onclick="deleteData({{ $group->id }})">
                                                                    Delete</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('Customer Group')" route="" create="no"
                                                    :message="__('No data found!')" colspan="6"></x-empty-table>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $groups->onEachSide(0)->links() }}
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

    {{-- add Vehicle --}}
    <div class="modal" id="addVehicle">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Customer Group') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.customerGroup.store') }}" method="POST" id="add-Vehicle-form">
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
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-Vehicle-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit Vehicle --}}
    @foreach ($groups as $index => $group)
        <div class="modal" id="editVehicle{{ $group->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Customer Group') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.customerGroup.update', $group->id) }}" method="POST"
                            id="edit-Vehicle-form{{ $group->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('Vehicle Name') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $group->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="model">{{ __('Model') }}</label>
                                    <input type="text" class="form-control" id="model" name="model"
                                        value="{{ $group->model }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="plate_number">{{ __('Plate Number') }}</label>
                                    <input type="text" class="form-control" id="plate_number" name="plate_number"
                                        value="{{ $group->plate_number }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="color">{{ __('Color') }}</label>
                                    <input type="text" class="form-control" id="color" name="color"
                                        value="{{ $group->color }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="year">{{ __('Year') }}</label>
                                    <input type="text" class="form-control" id="year" name="year"
                                        value="{{ $group->year }}">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-Vehicle-form{{ $group->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    @push('js')
        <script>
            function deleteData(id) {
                $("#deleteForm").attr("action", '{{ route('admin.customerGroup.destroy', '') }}' + "/" + id)
            }
        </script>
    @endpush
@endsection
