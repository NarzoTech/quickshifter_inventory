@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Profile') }}</title>
@endsection
@section('content')
    {{-- edit profile area  --}}
    <div class="card profile-widget">
        <div class="profile-widget-header">
            <img alt="image" id="profileImgPreview" src="{{ $admin->image ? asset($admin->image) : '' }}"
                class="rounded-circle profile-widget-picture">
        </div>

        <div class="profile-widget-description">

            <form @adminCan('admin.profile.edit') action="{{ route('admin.profile-update') }}" @endadminCan
                enctype="multipart/form-data" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('New Image') }}</label>
                            <input id="profileImgInput" type="file" class="form-control-file" name="image">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $admin->name }}" name="name">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" value="{{ $admin->email }}" name="email">
                        </div>
                    </div>
                </div>
                @adminCan('admin.profile.edit')
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </div>
                @endadminCan
            </form>
        </div>

    </div>
    {{-- edit profile area  --}}

    {{-- edit password area --}}

    <div class="card ">
        <div class="card-body">
            <form @adminCan('admin.profile.edit') action="{{ route('admin.update-password') }}" @endadminCan
                enctype="multipart/form-data" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Current Password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="current_password">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>

                </div>
                @adminCan('admin.profile.edit')
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </div>
                @endadminCan
            </form>
        </div>
    </div>

    {{-- edit password area --}}
@endsection
@push('js')
    <script>
        //input image preview function
        "use strict";
        $(document).ready(function() {
            setupImagePreview('profileImgInput', 'profileImgPreview');
        });
    </script>
@endpush
