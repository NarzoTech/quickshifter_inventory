@extends('admin.master_layout')
@section('title')
    <title>{{ __('Product List') }}</title>
@endsection

@push('css')
    <style>
        .tagify.form-control.tags {
            height: auto;
        }

        tag {
            padding-top: 5px;
        }
    </style>
@endpush
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Product List') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.product.index') }}">{{ __('Product List') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Add Product') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Add Product') }}</h4>
                                <div>
                                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.product.store') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8 row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">{{ __('Name') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" id="name"
                                                        value="{{ old('name') }}">
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    // generate random barcode
                                                    $barcode = rand(10000000, 99999999);

                                                @endphp
                                                <div class="form-group">
                                                    <label for="barcode">{{ __('Barcode') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="barcode" class="form-control" id="barcode"
                                                        value="{{ old('barcode', $barcode) }}">
                                                    @error('barcode')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sku">{{ __('SKU') }}<span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="sku" class="form-control currency"
                                                            id="sku" value="{{ old('sku') }}">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text generate_sku cursor-pointer">
                                                                <i class="fas fa-barcode"></i>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    @error('sku')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="category_id">{{ __('Category') }}<span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <select name="category_id" id="categories"
                                                            class="form-control select2">
                                                            <option value="">{{ __('Select Categories') }}
                                                            </option>
                                                            @foreach ($categories as $cat)
                                                                <option value="{{ $cat->id }}">
                                                                    {{ $cat->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <a href="javascript:;" data-bs-toggle="modal"
                                                                data-bs-target="#categoryModal" class="btn btn-primary"><i
                                                                    class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>
                                                    @error('category_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="brand_id">{{ __('Brand') }}</label>
                                                    <div class="input-group">
                                                        <select name="brand_id" id="brand_id" class="form-control select2">
                                                            <option value="">{{ __('Select Brand') }}</option>
                                                            @foreach ($brands as $brand)
                                                                <option value="{{ $brand->id }}">
                                                                    {{ $brand->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-group-append">
                                                            <a href="javascript:;" data-bs-toggle="modal"
                                                                data-bs-target="#brandModal" class="btn btn-primary"><i
                                                                    class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>
                                                    @error('brand_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cost">{{ __('Purchase Price') }}
                                                        ({{ currency_icon() }})</label>
                                                    <input type="number" name="cost" class="form-control" id="cost"
                                                        value="{{ old('cost') }}">
                                                    @error('cost')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="price">{{ __('Selling Price') }}
                                                        ({{ currency_icon() }})</label>
                                                    <input type="number" name="price" class="form-control"
                                                        id="price" value="{{ old('price') }}">
                                                    @error('price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>



                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Opening Stock') }}</label>
                                                    <input type="number" class="form-control" name="stock"
                                                        value="{{ old('stock', 0) }}">
                                                    @error('stock')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __('Stock alert') }}</label>
                                                    <input type="number" class="form-control" name="stock_alert"
                                                        value="{{ old('stock_alert') }}">
                                                    @error('stock_alert')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="short_description">{{ __('Short Description') }}</label>
                                                    <textarea name="short_description" id="" cols="30" rows="10" class="form-control height-80px">{!! old('short_description') !!}</textarea>
                                                    @error('short_description')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 row">
                                            <div class="card">
                                                <div class="card-body">
                                                    @if (Module::isEnabled('Media'))
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <x-media::media-input label_text="Images" name="images[]"
                                                                    multiple="yes" />
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="status">{{ __('Status') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="status" id="status" class="form-control">
                                                                <option value="1">
                                                                    {{ __('Active') }}</option>
                                                                <option value="0">
                                                                    {{ __('Inactive') }}</option>
                                                            </select>
                                                            @error('status')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="unit_id">{{ __('Unit') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="unit_id" id="unit_id"
                                                                class="form-control select2">
                                                                <option value="">{{ __('Select Unit') }}
                                                                </option>
                                                                @foreach ($units as $unit)
                                                                    <option value="{{ $unit->id }}">
                                                                        {{ $unit->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="input-group-append">
                                                                <a href="javascript:;" data-bs-toggle="modal"
                                                                    data-bs-target="#unitModal" class="btn btn-primary"><i
                                                                        class="fa fa-plus"></i></a>
                                                            </div>
                                                            @error('unit_id')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="unit_sale_id">{{ __('Sale Unit') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="unit_sale_id" id="unit_sale_id"
                                                                class="form-control select2">
                                                                <option value="">{{ __('Select Sale Unit') }}
                                                                </option>
                                                            </select>
                                                            @error('unit_sale_id')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="unit_purchase_id">{{ __('Purchase Unit') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="unit_purchase_id" id="unit_purchase_id"
                                                                class="form-control select2">
                                                                <option value="">{{ __('Select Purchase Unit') }}
                                                                </option>
                                                            </select>
                                                            @error('unit_purchase_id')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text-center offset-md-2 col-md-8">
                                            <x-admin.save-button :text="__('Save')">
                                            </x-admin.save-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- category create modal --}}
    @include('product::products.category.create-modal')
    @include('product::products.brand.create-modal')
    @include('product::unit-types.unit-modal')

    {{-- Media Modal Show --}}
    @if (Module::isEnabled('Media'))
        @stack('media_list_html')
    @endif
@endsection

@push('js')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('[name="name"]').on('input', function() {
                    var name = $(this).val();
                    var slug = convertToSlug(name);
                    $("[name='slug']").val(slug);
                });

                $('.generate_sku').on('click', function() {
                    var sku = Math.floor(10000000 + Math.random() * 90000000);
                    $("[name='sku']").val(sku);
                });

                $('#unit_id').on('change', function() {
                    // admin.unit.parent

                    const id = $(this).val();

                    $.ajax({
                        url: "{{ route('admin.unit.parent', '') }}/" + id,
                        success: function(response) {
                            console.log(response);
                            let html =
                                `<option value="${response.id}">${response.name} (${response.ShortName})</option>`

                            if (response.children) {
                                $.each(response.children, function(index, data) {
                                    html +=
                                        `<option value="${data?.id}">${data?.name} (${data?.ShortName})</option>`
                                })
                            }

                            $('[name="unit_sale_id"],[name="unit_purchase_id"]').html(html)
                        }
                    });
                })

                $('#categoryForm').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('admin.category.store') }}",
                        type: 'POST',
                        data: $('#categoryForm').serialize(),
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#categoryModal').modal('hide');
                                $('#categoryForm').trigger('reset');

                                let html =
                                    `<option value="${response.categories.id}">${response.categories.name}</option>`
                                $('#categories').append(html)
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(error) {
                            handleError(error)
                        }
                    })
                })
                $('#brandForm').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('admin.brand.store') }}",
                        type: 'POST',
                        data: $('#brandForm').serialize(),
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#brandModal').modal('hide');
                                $('#brandForm').trigger('reset');

                                let html =
                                    `<option value="${response.brand.id}">${response.brand.name}</option>`
                                $('#brand_id').append(html)
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(error) {
                            handleError(error)
                        }
                    })
                })
                $('#unitForm').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('admin.unit.store') }}",
                        type: 'POST',
                        data: $('#unitForm').serialize(),
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#unitModal').modal('hide');
                                $('#unitForm').trigger('reset');

                                let html =
                                    `<option value="${response.unit.id}">${response.unit.name}</option>`
                                $('#unit_id').append(html)
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(error) {
                            handleError(error)
                        }
                    })
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

            function changeAttr(val, selectorName) {
                if (val == 1) {
                    $(`[name="${selectorName}"]`).attr('required', true);
                    $(`.${selectorName}`).removeClass('d-none')
                    $(`[name="${selectorName}"]`).removeAttr('disabled');
                } else {
                    $(`[name="${selectorName}"]`).removeAttr('required');
                    $(`[name="${selectorName}"]`).attr('disabled');
                    $(`.${selectorName}`).addClass('d-none')
                }
            }
        })(jQuery);
    </script>

    @if (Module::isEnabled('Media'))
        @stack('media_libary_js')
    @endif
@endpush

{{-- Media Css --}}
@push('css')
    @if (Module::isEnabled('Media'))
        @stack('media_libary_css')
    @endif
@endpush
