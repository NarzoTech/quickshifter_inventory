{{-- @extends('admin.master_layout')
@section('title')
<title>{{ __('Create Page') }}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Create Post') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active"><a href="{{ route('admin.custom-pages.index') }}">{{ __('Customizeable Page List') }}</a>
                </div>
                <div class="breadcrumb-item">{{ __('Create Page') }}</div>
            </div>
        </div>
        <div class="section-body">
            <div class="mt-4 row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>{{ __('Create Post') }}</h4>
                            <div>
                                <a href="{{ route('admin.blogs.index') }}" class="btn btn-primary"><i
                                        class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">


                                    <div class="form-group col-md-8 offset-md-2">
                                        <label>{{ __('Title') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="title" class="form-control" name="title"
                                            value="{{ old('title') }}">
                                        @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-8 offset-md-2">
                                        <label>{{ __('Slug') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="slug" class="form-control" name="slug"
                                            value="{{ old('slug') }}">
                                        @error('slug')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-8 offset-md-2">
                                        <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                        <textarea name="description" id="" cols="30" rows="10"
                                            class="summernote">{{ old('description') }}</textarea>
                                        @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="text-center col-md-8 offset-md-2">
                                        <x-admin.save-button :text="__('Save')"></x-admin.save-button>
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
@endsection

@push('js')
<script>
    (function($) {
            "use strict";
            $(document).ready(function() {
                $("#title").on("keyup", function(e) {
                    $("#slug").val(convertToSlug($(this).val()));
                })
            });
        })(jQuery);

        function convertToSlug(Text) {
            return Text
                .toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
        }
</script>
@endpush --}}
