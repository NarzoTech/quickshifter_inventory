@extends('admin.master_layout')

@section('title')
    <title>{{ __('Warehouse') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Warehouse') }}</h1>
            </div>

            <div class="section-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="javascript:;" class="btn btn-primary" data-toggle="modal"
                                        data-target="#addWarehouse"><i class="fa fa-plus"></i>
                                        {{ __('Add Warehouse') }}</a>
                                </h4>
                                <div class="card-header-form">
                                    <form id="product_search_form">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="{{ __('Search here..') }}" autocomplete="off"
                                                value="{{ request()->get('search') }}">
                                            <div class="input-group-btn">
                                                <button class="btn btn-primary" style="padding:9px"><i
                                                        class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Phone') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('City') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($warehouses as $index => $house)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>{{ $house->name }}</td>
                                                    <td>{{ $house->phone }}</td>
                                                    <td>{{ $house->email }}</td>
                                                    <td>{{ $house->city }}</td>
                                                    <td>
                                                        @if ($house->status == 1)
                                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="javascript:;" data-toggle="modal" data-target="#editWarehouse{{ $house->id }}"
                                                            class="btn btn-primary btn-sm edit-btn"><i class="fa fa-edit"
                                                                aria-hidden="true"></i></a>
                                                        <a href="javascript:;" data-toggle="modal"
                                                            data-target="#deleteModal" class="btn btn-danger btn-sm"
                                                            onclick="deleteData({{ $house->id }})"><i
                                                                class="fa fa-trash" aria-hidden="true"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>

    <!-- Add Warehouse modal -->
    <div class="modal" id="addWarehouse">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Add Warehouse</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form action="{{ route('admin.warehouse.store') }}" method="POST" id="add-warehouse-form">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
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
                                <label for="status">{{ __('Status') }}</label>
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
                    <button type="submit" class="btn btn-primary" form="add-warehouse-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit warehouse modal --}}

    @foreach ($warehouses as $index => $house)
        <div class="modal" id="editWarehouse{{ $house->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Warehouse</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.warehouse.update',$house->id) }}" method="POST" id="update-warehouse-form{{ $house->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $house->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">{{ __('Phone') }}</label>
                                    <input type="text" class="form-control" id="phone" value="{{ $house->phone }}" name="phone">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" id="email" value="{{ $house->email }}" name="email">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="city">{{ __('City') }}</label>
                                    <input type="text" class="form-control" id="city" value="{{ $house->city }}" name="city">
                                </div>
                                <div class="form-group col-md-6 ">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select  name="status" id="status" class="form-control">
                                        <option value="1" @if ($house->status == 1)
                                            selected
                                        @endif>{{ __('Active') }}</option>
                                        <option value="0" @if ($house->status == 0)
                                            selected
                                        @endif>{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" form="update-warehouse-form{{ $house->id }}">Update</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

    @include('components.admin.preloader')
@endsection

@push('js')
    <script>
        $(document).ready(function() {

            $('.edit-btn').click(function(e) {
                $('.preloader_area').removeClass('d-none');
                e.preventDefault();
                const url = $(this).attr('href');
                console.log(url);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#name').val(response.name);
                        $('#ShortName').val(response.ShortName);
                        $('#base_unit').val(response.base_unit);
                        $('#operator').val(response.operator);
                        $('#operator_value').val(response.operator_value);

                        if (response.base_unit) {
                            $('.operator').removeClass('d-none');
                            $('.operator_value').removeClass('d-none');
                        } else {
                            $('.operator').addClass('d-none');
                            $('.operator_value').addClass('d-none');
                        }
                        $('input[name="status"][value="' + response.status + '"]').prop(
                            'checked', true);
                        let url = "{{ route('admin.unit.update', ':id') }}";
                        url = url.replace(':id', response.id);
                        $('#form').attr('action', url);
                        const unitId = "<input type='hidden' name='unit_id' value='" +
                            response.id + "'>";
                        const method = "<input type='hidden' name='_method' value='PUT'>";
                        $('#form').append(unitId);
                        $('#form').append(method);
                        $('.preloader_area').addClass('d-none');
                    },
                    error: function(error) {
                        console.log(error);
                        $('.preloader_area').addClass('d-none');
                    }
                });
            })

            $('#base_unit').on("change", function() {
                const baseUnit = $(this).val();
                if (baseUnit) {
                    $('.operator').removeClass('d-none');
                    $('.operator_value').removeClass('d-none');
                } else {
                    $('.operator').addClass('d-none');
                    $('.operator_value').addClass('d-none');
                }
            });
        });

        function deleteData(id) {
            let url = '{{ route('admin.unit.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
        }
    </script>
@endpush
