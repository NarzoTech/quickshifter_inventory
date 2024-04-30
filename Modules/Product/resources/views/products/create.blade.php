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
                                                        required value="{{ old('name') }}">
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="slug">{{ __('Slug') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="slug" name="slug"
                                                        value="{{ old('slug') }}" placeholder="{{ __('Slug') }}"
                                                        class="form-control">
                                                    @error('slug')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="sku">{{ __('SKU') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="sku" class="form-control" id="sku"
                                                        required value="{{ old('sku') }}">
                                                    @error('sku')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price">{{ __('Price') }} ({{ currency() }})<span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="price" class="form-control" id="price"
                                                        required value="{{ old('price') }}">
                                                    @error('price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount">{{ __('Discount') }}</label>
                                                    <input type="number" name="discount" class="form-control"
                                                        id="discount" value="{{ old('discount') }}">
                                                    @error('discount')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount_type">{{ __('Discount Type') }}</label>
                                                    <select name="discount_type" id="discount_type" class="form-control">
                                                        <option value="fixed">{{ __('Fixed') }}</option>
                                                        <option value="percent">{{ __('Percent') }}</option>
                                                    </select>
                                                    @error('discount')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12 row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="min_delivery_time">{{ __('Min Delivery Time') }} ({{__('Days')}})<span
                                                                class="text-danger">*</span></label>
                                                        <input class="form-control" type="number" name="min_delivery_time">
                                                        @error('min_delivery_time')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="max_delivery_time">{{ __('Max Delivery Time') }} ({{__('Days')}})<span
                                                                class="text-danger">*</span></label>
                                                        <input class="form-control" type="number" name="max_delivery_time">
                                                        @error('max_delivery_time')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
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

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{ __('Video Link') }}</label>
                                                    <input type="text" class="form-control" name="video_link"
                                                        value="{{ old('video_link') }}">
                                                    @error('video_link')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="short_description">{{ __('Short Description') }} <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="short_description" id="" cols="30" rows="10" class="summernote">{!! old('short_description') !!}</textarea>
                                                    @error('short_description')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description">{{ __('Description') }} <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="description" id="" cols="30" rows="10" class="summernote">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label
                                                        for="additional_information">{{ __('Additional Information') }}</label>
                                                    <textarea name="additional_information" id="additional_information" cols="30" rows="10"
                                                        class="summernote">{{ old('additional_information') }}</textarea>
                                                    @error('additional_information')
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
                                                            <label for="categories">{{ __('Categories') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="categories[]" id="categories"
                                                                class="form-control select2" multiple>
                                                                <option value="">{{ __('Select Categories') }}
                                                                </option>
                                                                @foreach ($categories as $cat)
                                                                    <option value="{{ $cat->id }}">
                                                                        {{ $cat->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('categories')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="brand_id">{{ __('Brands') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="brand_id" id="brand_id"
                                                                class="form-control select2">
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
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Warranty Available ?') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="is_warranty" class="form-control">
                                                                <option value="0">{{ __('No') }}</option>
                                                                <option value="1">{{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_warranty')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 d-none warranty_duration">
                                                        <div class="form-group">
                                                            <label>{{ __('Warranty Duration') }}
                                                                ({{ __('Month') }})<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="warranty_duration"
                                                                class="form-control" disabled />
                                                            @error('warranty_duration')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Product Return Availabe ?') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="is_returnable" class="form-control"
                                                                id="is_returnable">
                                                                <option value="0">{{ __('No') }}</option>
                                                                <option value="1">{{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_returnable')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Is Featured ?') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="is_featured" class="form-control"
                                                                id="is_featured">
                                                                <option value="0">{{ __('No') }}</option>
                                                                <option value="1">{{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_featured')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Best Seller') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="is_bestseller" class="form-control"
                                                                id="is_bestseller">
                                                                <option value="0">{{ __('No') }}</option>
                                                                <option value="1">{{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_bestseller')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Is New') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="is_new" class="form-control" id="is_new">
                                                                <option value="0">{{ __('No') }}</option>
                                                                <option value="1">{{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_new')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="badge">{{ __('Badge') }}</label>
                                                            <input type="text" class="form-control inputtags"
                                                                name="badge">
                                                            @error('badge')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Show Home Page ?') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <select name="show_homepage" class="form-control"
                                                                id="show_homepage">
                                                                <option value="0"
                                                                    @if (old('show_homepage') == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option
                                                                    value="1"@if (old('show_homepage') == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('show_homepage')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Is Flash Deal ?') }} </label>
                                                            <select name="is_flash_deal" class="form-control"
                                                                id="is_flash_deal">
                                                                <option value="0"
                                                                    @if (old('is_flash_deal') == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_flash_deal') == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_flash_deal')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Is Top Selling ?') }} </label>
                                                            <select name="is_top" class="form-control" id="is_top">
                                                                <option value="0"
                                                                    @if (old('is_top') == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_top') == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_top')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Is Verified') }} </label>
                                                            <select name="is_verified" class="form-control"
                                                                id="is_verified">
                                                                <option value="0"
                                                                    @if (old('is_verified') == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_verified') == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_verified')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Allow Cash On Delivery?') }} </span></label>
                                                            <select name="is_cod" class="form-control" id="is_cod">
                                                                <option value="0"
                                                                    @if (old('is_cod') == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_cod') == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_cod')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Take Pre Order?') }} </span></label>
                                                            <select name="is_pre_order" class="form-control"
                                                                id="is_pre_order">
                                                                <option value="0"
                                                                    @if (old('is_pre_order') == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_pre_order') == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_pre_order')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 d-none release_date">
                                                        <div class="form-group">
                                                            <label>{{ __('Release Date') }} </label>
                                                            <input type="text" name="release_date"
                                                                class="form-control datepicker" disabled />
                                                            @error('release_date')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 d-none max_product">
                                                        <div class="form-group">
                                                            <label>{{ __('Max Quantity') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="max_product" class="form-control"
                                                                disabled />
                                                            @error('max_product')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>{{ __('Has Partial Payment?') }} </label>
                                                            <select name="is_partial" class="form-control"
                                                                id="is_partial">
                                                                <option value="0"
                                                                    @if (old('is_partial') == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_partial') == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_partial')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 d-none partial_amount">
                                                        <div class="form-group">
                                                            <label>{{ __('Partial Amount') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="partial_amount"
                                                                class="form-control" disabled />
                                                            @error('partial_amount')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="tags">{{ __('Tags') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control tags"
                                                                name="tags">
                                                            @error('tags')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="meta_title">{{ __('Meta Title') }}</label>
                                                            <input type="text" class="form-control" name="meta_title">
                                                            @error('meta_title')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label
                                                                for="meta_description">{{ __('Meta Description') }}</label>
                                                            <textarea type="text" class="form-control" name="meta_description" style="height: 80px">{{ old('meta_description') }}</textarea>
                                                            @error('meta_description')
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
                $('[name="is_warranty"]').on('change', function() {
                    var is_warranty = $(this).val();
                    changeAttr(is_warranty, 'warranty_duration')
                })
                $('[name="is_partial"]').on('change', function() {
                    var is_partial = $(this).val();
                    changeAttr(is_partial, 'partial_amount')
                })
                $('[name="is_pre_order"]').on('change', function() {
                    var is_partial = $(this).val();
                    changeAttr(is_partial, 'release_date')
                    changeAttr(is_partial, 'max_product')
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
