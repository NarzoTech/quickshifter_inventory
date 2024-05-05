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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">{{ __('Name') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" id="name"
                                                        required value="{{ old('name') }}">
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $barcode = [
                                                        'c128' => 'Code 128',
                                                        'c39' => 'Code 39',
                                                        'ean13' => 'EAN-13',
                                                        'upca' => 'UPC-A',
                                                        'upce' => 'UPC-E',
                                                        'ean8' => 'EAN-8',
                                                    ];
                                                @endphp
                                                <div class="form-group">
                                                    <label for="barcode">{{ __('Barcode') }}<span
                                                            class="text-danger">*</span></label>
                                                    <select name="barcode" id="barcode" class="form-control">
                                                        @foreach ($barcode as $key => $code)
                                                            <option value="{{ $key }}"> {{ $code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
                                                    <label for="category_id">{{ __('Category') }}<span
                                                            class="text-danger">*</span></label>
                                                    <select name="category_id" id="categories" class="form-control select2">
                                                        <option value="">{{ __('Select Categories') }}
                                                        </option>
                                                        @foreach ($categories as $cat)
                                                            <option value="{{ $cat->id }}">
                                                                {{ $cat->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="brand_id">{{ __('Brands') }}<span
                                                            class="text-danger">*</span></label>
                                                    <select name="brand_id" id="brand_id" class="form-control select2">
                                                        <option value="">{{ __('Select Brand') }}</option>
                                                        @foreach ($brands as $brand)
                                                            <option value="{{ $brand->id }}">
                                                                {{ $brand->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('brand_id')
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
                                                            id="sku" required value="{{ old('sku') }}">
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
                                                    <label for="price">{{ __('Price') }} ({{ currency_icon() }})<span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="price" class="form-control" id="price"
                                                        required value="{{ old('price') }}">
                                                    @error('price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tax">{{ __('Tax') }}</label>
                                                    <div class="input-group">

                                                        <input type="number" name="tax" class="form-control currency"
                                                            id="tax" required value="{{ old('tax') }}">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                %
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @error('tax')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tax_type">{{ __('Tax Type') }}</label>
                                                    <select name="tax_type" id="tax_type" class="form-control">
                                                        <option value="exclusive" selected>{{ __('Exclusive') }}</option>
                                                        <option value="inclusive">{{ __('Inclusive') }}</option>
                                                    </select>
                                                    @error('tax_type')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cost">{{ __('Cost') }}
                                                        ({{ currency_icon() }})<span class="text-danger">*</span></label>
                                                    <input type="number" name="cost" class="form-control"
                                                        id="cost" required value="{{ old('cost') }}">
                                                    @error('cost')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ __('Stock Quantity') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="quantity"
                                                        value="{{ old('quantity') }}">
                                                    @error('quantity')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ __('Stock alert') }} <span
                                                            class="text-danger">*</span></label>
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
                                                                <x-media::media-input name="image" />
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
                                                            <label for="unit_id">{{ __('Unit Type') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="unit_id" id="unit_id"
                                                                class="form-control select2">
                                                                <option value="">{{ __('Select Unit Type') }}
                                                                </option>
                                                                @foreach ($units as $unit)
                                                                    <option value="{{ $unit->id }}">
                                                                        {{ $unit->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('unit_id')
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
