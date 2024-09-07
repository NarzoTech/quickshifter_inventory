@extends('admin.master_layout')

@section('title')
    <title>{{ __('Unit List') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Unit List') }}</h1>
            </div>

            <div class="section-body">
                <div class="row mt-4">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.unit.store') }}" method="POST" enctype="multipart/form-data"
                                    id="form">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-12">
                                            <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" id="name" class="form-control" name="name">
                                        </div>
                                        <div class="form-group col-12">
                                            <label>{{ __('Short Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" id="ShortName" class="form-control" name="ShortName">
                                        </div>
                                        <div class="form-group col-12">
                                            <label>{{ __('Base Unit') }}</label>
                                            <select name="base_unit" id="base_unit" class="form-control">
                                                <option value="">{{ __('Select Base Unit') }}</option>
                                                @foreach ($parentUnits as $parentUnit)
                                                    <option value="{{ $parentUnit->id }}">{{ $parentUnit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-12 operator d-none">
                                            <label>{{ __('Operator') }}</label>
                                            <select name="operator" id="operator" class="form-control">
                                                <option value="*">{{ __('Multiply') }} (*)</option>
                                                <option value="/">{{ __('Divide') }} (/)</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-12 operator_value d-none">
                                            <label>{{ __('Operator Value') }} <span class="text-danger">*</span></label>
                                            <input type="text" id="operator_value" class="form-control"
                                                name="operator_value" value="1">
                                        </div>
                                        <div class="form-group col-12">
                                            <label>{{ __('Status') }} </label>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <input type="radio" name='status' value="1" checked />
                                                    <label>{{ __('Active') }} </label>
                                                </div>
                                                <div>
                                                    <input type="radio" name='status' value="0" />
                                                    <label>{{ __('Inactive') }} </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-admin.save-button :text="__('Save')" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Short Name') }}</th>
                                                <th>{{ __('Base Unit') }}</th>
                                                <th>{{ __('Operator') }}</th>
                                                <th>{{ __('Operator Value') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($units as $index => $unit)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>{{ $unit->name }}</td>
                                                    <td>{{ $unit->ShortName }}</td>
                                                    <td>{{ $unit->base_unit }}</td>
                                                    <td>{{ $unit->operator }}</td>
                                                    <td>{{ $unit->operator_value }}</td>
                                                    <td>
                                                        @if ($unit->status == 1)
                                                            <span class="badge badge-success">{{ __('Active') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.unit.edit', $unit->id) }}"
                                                            class="btn btn-primary btn-sm edit-btn"><i class="fa fa-edit"
                                                                aria-hidden="true"></i></a>
                                                        <a href="javascript:;" data-toggle="modal"
                                                            data-target="#deleteModal" class="btn btn-danger btn-sm"
                                                            onclick="deleteData({{ $unit->id }})"><i
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

    @include('components.admin.preloader')
@endsection

@push('js')
    <script>
        $(document).ready(function() {

            $('.edit-btn').click(function(e) {
                $('.preloader_area').removeClass('d-none');
                e.preventDefault();
                const url = $(this).attr('href');
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
