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
                                    <input type="hidden" name="lang_code" value="{{ request()->code }}">
                                    <div class="row">
                                        <div class="col-md-8 row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">{{ __('Name') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" id="name"
                                                        required value="{{ old('name', $product->name) }}">
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
                                                        value="{{ old('slug', $product->slug) }}"
                                                        placeholder="{{ __('Slug') }}" class="form-control">
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
                                                        required value="{{ old('sku', $product->sku) }}">
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
                                                        required value="{{ old('price', $product->price) }}">
                                                    @error('price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount">{{ __('Discount') }}</label>
                                                    <input type="number" name="discount" class="form-control"
                                                        id="discount" value="{{ old('discount', $product->discount) }}">
                                                    @error('discount')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount_type">{{ __('Discount Type') }}</label>
                                                    <select name="discount_type" id="discount_type" class="form-control">
                                                        <option value="fixed"
                                                            @if ($product->discount_type == 'fixed') selected @endif>
                                                            {{ __('Fixed') }}</option>
                                                        <option value="percent"
                                                            @if ($product->discount_type == 'percent') selected @endif>
                                                            {{ __('Percent') }}</option>
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
                                                        <input class="form-control" type="number" name="min_delivery_time" value="{{$product->min_delivery_time}}">
                                                        @error('min_delivery_time')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="max_delivery_time">{{ __('Max Delivery Time') }} ({{__('Days')}})<span
                                                                class="text-danger">*</span></label>
                                                        <input class="form-control" type="number" name="max_delivery_time" value="{{$product->max_delivery_time}}">
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
                                                        value="{{ old('quantity', $product->stock) }}">
                                                    @error('quantity')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>{{ __('Video Link') }}</label>
                                                    <input type="text" class="form-control" name="video_link"
                                                        value="{{ old('video_link', $product->video_link) }}">
                                                    @error('video_link')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="short_description">{{ __('Short Description') }} <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="short_description" id="" cols="30" rows="10" class="summernote">{!! old('short_description', $product->short_description) !!}</textarea>
                                                    @error('short_description')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description">{{ __('Description') }} <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="description" id="" cols="30" rows="10" class="summernote">{!! old('description', $product->description) !!}</textarea>
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
                                                        class="summernote">{!! old('additional_information', $product->additional_information) !!}</textarea>
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
                                                    @php
                                                        $img=[$product->image]; 
                                                    @endphp
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <x-media::media-input name="image" :dataImages="$img" />
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="status">{{ __('Status') }}<span
                                                                    class="text-danger">*</span></label>
                                                            <select name="status" id="status" class="form-control">
                                                                <option value="1"
                                                                    @if ($product->status == 1) selected @endif>
                                                                    {{ __('Active') }}</option>
                                                                <option value="0"
                                                                    @if ($product->status == 0) selected @endif>
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
                                                                    <option value="{{ $cat->id }}"
                                                                        @if (in_array($cat->id, $productCategories)) selected @endif>
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
                                                                    <option value="{{ $brand->id }}"
                                                                        @if ($product->brand_id == $brand->id) selected @endif>
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
                                                                <option value="">{{ __('Select Unit Type') }}</option>
                                                                @foreach ($units as $unit)
                                                                    <option value="{{ $unit->id }}" @if ($product->unit_id == $unit->id) selected @endif>
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
                                                                <option value="0"
                                                                    @if ($product->is_warranty == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if ($product->is_warranty == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_warranty')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 d-none warranty_duration">
                                                        <div class="form-group">
                                                            <label>{{ __('Warranty Duration') }} ({{__('Month')}})<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="warranty_duration"
                                                                class="form-control"
                                                                @if (!$product->warranty_duration) disabled @endif
                                                                value="{{ $product->warranty_duration }}" />
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
                                                                <option value="0"
                                                                    @if ($product->is_return == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if ($product->is_return == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
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
                                                                <option value="0"
                                                                    @if ($product->is_featured == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if ($product->is_featured == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
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
                                                                <option value="0"
                                                                    @if ($product->is_bestseller == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if ($product->is_bestseller == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
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
                                                                <option value="0"
                                                                    @if ($product->is_new == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if ($product->is_new == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
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
                                                                name="badge" value="{{ $product->badge }}">
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
                                                                    @if (old('show_homepage') == 0|| $product->show_homepage == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option
                                                                    value="1"@if (old('show_homepage') == 1|| $product->show_homepage == 1) selected @endif>
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
                                                                    @if (old('is_flash_deal') == 0|| $product->is_flash_deal == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_flash_deal') == 1|| $product->is_flash_deal == 1) selected @endif>
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
                                                                    @if (old('is_top') == 0|| $product->is_top == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_top') == 1|| $product->is_top == 1) selected @endif>
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
                                                                    @if (old('is_verified') == 0|| $product->is_verified == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_verified') == 1|| $product->is_verified == 1) selected @endif>
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
                                                                    @if (old('is_cod') == 0|| $product->is_cod == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_cod') == 1|| $product->is_cod == 1) selected @endif>
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
                                                                    @if (old('is_pre_order') == 0|| $product->is_pre_order == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_pre_order') == 1|| $product->is_pre_order == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_pre_order')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 {{$product->is_pre_order? '' :'d-none'}} release_date">
                                                        <div class="form-group">
                                                            <label>{{ __('Release Date') }} </label>
                                                            <input type="text" name="release_date"
                                                                class="form-control datepicker" @if(!$product->is_pre_order) disabled @endif value="{{$product->release_date}}" />
                                                            @error('release_date')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 {{$product->is_pre_order? '' :'d-none'}} max_product">
                                                        <div class="form-group">
                                                            <label>{{ __('Max Quantity') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="number" name="max_product" class="form-control"
                                                                @if(!$product->is_pre_order) disabled @endif  value="{{$product->max_product}}"/>
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
                                                                    @if (old('is_partial') == 0 || $product->is_partial == 0) selected @endif>
                                                                    {{ __('No') }}</option>
                                                                <option value="1"
                                                                    @if (old('is_partial') == 1 || $product->is_partial == 1) selected @endif>
                                                                    {{ __('Yes') }}</option>
                                                            </select>
                                                            @error('is_partial')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 {{!$product->partial_amount? 'd-none' : ''}} partial_amount">
                                                        <div class="form-group">
                                                            <label>{{ __('Partial Amount') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" name="partial_amount"
                                                                class="form-control" @if(!$product->partial_amount) disabled @endif />
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
                                                                name="tags" value="{{ $product->tags }}">
                                                            @error('tags')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="meta_title">{{ __('Meta Title') }}</label>
                                                            <input type="text" class="form-control" name="meta_title" value="{{$product->meta_title}}">
                                                            @error('meta_title')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label
                                                                for="meta_description">{{ __('Meta Description') }}</label>
                                                            <textarea type="text" class="form-control" name="meta_description" style="height: 80px">{{ old('meta_description',$product->meta_description) }}</textarea>
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
