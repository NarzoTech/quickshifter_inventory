@extends('admin.master_layout')
@section('title')
    <title>{{ __('Service List') }}</title>
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
                <h1>{{ __('Service List') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.service.index') }}" method="GET" onchange="this.submit()"
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
                                    <a href="javascript:;" data-toggle="modal" data-target="#addService"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Service') }}</a>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($categories as $index => $category)
                                                <tr>
                                                    <td>{{ $loop->first + $index }}</td>
                                                    <td>{{ $category->name }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button id="btnGroupDrop{{ $category->id }}" type="button"
                                                                class="btn btn-primary dropdown-toggle"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu"
                                                                aria-labelledby="btnGroupDrop{{ $category->id }}">
                                                                <a class="dropdown-item" href="javascript:;"
                                                                    data-toggle="modal"
                                                                    data-target="#editService{{ $category->id }}">Edit</a>
                                                                <a href="javascript:;" data-toggle="modal"
                                                                    data-target="#deleteModal" class="dropdown-item"
                                                                    onclick="deleteData({{ $category->id }})">
                                                                    Delete</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('Category')" route="" create="no"
                                                    :message="__('No data found!')" colspan="6"></x-empty-table>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $categories->onEachSide(0)->links() }}
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

    {{-- add area --}}
    <div class="modal" id="addService">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Service') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.service.store') }}" method="POST" id="add-service-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group col-12">
                                <label for="name">{{ __('Status') }}<span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-service-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit area --}}
    @foreach ($categories as $index => $category)
        <div class="modal" id="editService{{ $category->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Service') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.service.update', $category->id) }}" method="POST"
                            id="edit-service-form{{ $category->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $category->name }}">
                                </div>
                                <div class="form-group col-12">
                                    <label for="status">{{ __('Status') }}<span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>
                                            {{ __('Active') }}
                                        </option>
                                        <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>
                                            {{ __('Inactive') }}
                                        </option>
                                    </select>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-service-form{{ $category->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

    @push('js')
        <script>
            function deleteData(id) {
                $("#deleteForm").attr("action", '{{ route('admin.service.destroy', '') }}' + "/" + id)
            }
        </script>
    @endpush
@endsection
