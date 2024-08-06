@extends('admin.master_layout')
@section('title')
    <title>{{ __('Edit Product') }}</title>
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
                <h1>{{ __('Product Edit') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.product.index') }}">{{ __('Product List') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Edit Product') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Edit Product') }}</h4>
                                <div>
                                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.product.update', $product) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-8 row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">{{ __('Name') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" id="name"
                                                        value="{{ old('name', $product->name) }}">
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="barcode">{{ __('Barcode') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="barcode" class="form-control" id="barcode"
                                                        value="{{ old('barcode', $product->barcode) }}">
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
                                                            <option value="{{ $cat->id }}"
                                                                @if (old('category_id', $product->category_id) == $cat->id) selected @endif>
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
                                                    <label for="brand_id">{{ __('Brand') }}</label>
                                                    <select name="brand_id" id="brand_id" class="form-control select2">
                                                        <option value="">{{ __('Select Brand') }}</option>
                                                        @foreach ($brands as $brand)
                                                            <option value="{{ $brand->id }}"
                                                                @if (old('brand_id', $product->brand_id) == $brand->id) selected @endif>
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
                                                            id="sku" value="{{ old('sku', $product->sku) }}">
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
                                                    <label for="price">{{ __('Price') }}
                                                        ({{ currency_icon() }})</label>
                                                    <input type="number" name="price" class="form-control" id="price"
                                                        value="{{ old('price', $product->price) }}">
                                                    @error('price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tax">{{ __('Tax') }}</label>
                                                    <div class="input-group">
                                                        <input type="number" name="tax" class="form-control currency"
                                                            id="tax" value="{{ old('tax', $product->tax) }}">
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
                                                        <option value="exclusive"
                                                            @if (old('tax_type', $product->tax_type) == 'exclusive') selected @endif>
                                                            {{ __('Exclusive') }}</option>
                                                        <option value="inclusive"
                                                            @if (old('tax_type', $product->tax_type) == 'inclusive') selected @endif>
                                                            {{ __('Inclusive') }}</option>
                                                    </select>
                                                    @error('tax_type')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div> --}}


                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cost">{{ __('Cost') }}
                                                        ({{ currency_icon() }})</label>
                                                    <input type="number" name="cost" class="form-control" id="cost"
                                                        value="{{ old('cost', $product->cost) }}">
                                                    @error('cost')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ __('Stock Quantity') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="stock"
                                                        value="{{ old('stock', $product->stock) }}">
                                                    @error('stock')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>{{ __('Stock alert') }}</label>
                                                    <input type="number" class="form-control" name="stock_alert"
                                                        value="{{ old('stock_alert', $product->stock_alert) }}">
                                                    @error('stock_alert')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="short_description">{{ __('Short Description') }}</label>
                                                    <textarea name="short_description" id="" cols="30" rows="10" class="form-control height-80px">{!! old('short_description', $product->short_description) !!}</textarea>
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
                                                        @php
                                                            $images = $product->images;

                                                            // explode images
                                                            $images = explode(',', $images[0]);
                                                        @endphp
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <x-media::media-input label_text="Images" name="images[]"
                                                                    multiple="yes" :dataImages="$images" />
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="status">{{ __('Status') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="status" id="status" class="form-control">
                                                                <option value="1"
                                                                    @if (old('status', $product->status) == 1) selected @endif>
                                                                    {{ __('Active') }}</option>
                                                                <option value="0"
                                                                    @if (old('status', $product->status) == 0) selected @endif>
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
                                                                    <option value="{{ $unit->id }}"
                                                                        @if (old('unit_id', $product->unit_id) == $unit->id) selected @endif>
                                                                        {{ $unit->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
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
                                                                <option value="{{ $product->unit->id }}"
                                                                    @if (old('unit_sale_id', $product->unit_sale_id) == $product->unit->id) selected @endif>
                                                                    {{ $product->unit->name }}
                                                                </option>
                                                                @foreach ($product->unit->children as $unit)
                                                                    <option value="{{ $unit->id }}"
                                                                        @if (old('unit_sale_id', $product->unit_sale_id) == $unit->id) selected @endif>
                                                                        {{ $unit->name }}
                                                                    </option>
                                                                @endforeach
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
                                                                <option value="{{ $product->unit->id }}"
                                                                    @if (old('unit_purchase_id', $product->unit_purchase_id) == $product->unit->id) selected @endif>
                                                                    {{ $product->unit->name }}
                                                                </option>
                                                                @foreach ($product->unit->children as $unit)
                                                                    <option value="{{ $unit->id }}"
                                                                        @if (old('unit_purchase_id', $product->unit_purchase_id) == $unit->id) selected @endif>
                                                                        {{ $unit->name }}
                                                                    </option>
                                                                @endforeach
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
                                            <x-admin.update-button :text="__('Update')">
                                            </x-admin.update-button>
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
                $('[name="is_warranty"]').on('change', function() {
                    var is_warranty = $(this).val();
                    changeAttr(is_warranty, 'warranty_duration')
                })
                $('[name="is_partial"]').on('change', function() {
                    var is_partial = $(this).val();
                    changeAttr(is_partial, 'partial_amount')
                })
                $('[name="is_pre_order"]').on('change', function() {
                    var is_pre_order = $(this).val();
                    changeAttr(is_pre_order, 'release_date')
                    changeAttr(is_pre_order, 'max_product')
                })

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
