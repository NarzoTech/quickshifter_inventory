@extends('admin.master_layout')
@section('title')
    <title>{{ __('Brand Edit') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Brand Edit') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.brand.index') }}">{{ __('Brand List') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Edit Brand') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Edit Brand') }}</h4>
                                <div>
                                    <a href="{{ route('admin.brand.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.brand.update', $brand) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="lang_code" value="{{request()->lang_code}}">
                                    <div class="row">
                                        <div class="col-md-8 offset-md-2">
                                            <div class="form-group">
                                                <label for="name">{{ __('Name') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" id="name"
                                                    required value="{{ old('name', $brand->name) }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-8 offset-md-2">
                                            <div class="form-group">
                                                <label for="slug">{{ __('Slug') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="slug" name="slug"
                                                    value="{{ old('slug', $brand->slug) }}"
                                                    placeholder="{{ __('Slug') }}" class="form-control">
                                                @error('slug')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-8 offset-md-2">
                                            <div class="form-group">
                                                <label for="slug">{{ __('Status') }}<span
                                                        class="text-danger">*</span></label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1"
                                                        @if ($brand->status == 1) selected @endif>
                                                        {{ __('Active') }}</option>
                                                    <option value="0"
                                                        @if ($brand->status == 0) selected @endif>
                                                        {{ __('Inactive') }}</option>
                                                </select>
                                                @error('status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-8 offset-md-2">
                                            <div class="form-group">
                                                <label for="description">{{ __('Short Description') }}</label>
                                                <textarea name="description" id="description" cols="30" rows="10"
                                                    placeholder="{{ __('Enter Short Description') }}" class="summernote">{!! $brand->description !!}</textarea>
                                                @error('description')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        @if (Module::isEnabled('Media'))
                                            @php
                                                $image = [$brand->image ?? $brand->image];
                                            @endphp
                                            <div class="form-group col-md-8 offset-md-2">
                                                <x-media::media-input name="image" label_text="Image" :dataImages="$image" />
                                            </div>
                                        @endif
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
            });
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
