@extends('admin.master_layout')
@section('title')
    <title>{{ __('Pusher Configuration') }}</title>
@endsection
@section('admin-content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Pusher Configuration') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.update-chat-setting') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">{{ __('App Id') }}</label>
                                                @if (env('APP_MODE') == 'DEMO')
                                                    <input type="text" name="pusher_app_id"
                                                        value="pusher-test.93943-app-key" class="form-control">
                                                @else
                                                    <input type="text" name="pusher_app_id"
                                                        value="{{ $setting->pusher_app_id }}" class="form-control">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">{{ __('App Key') }}</label>
                                                @if (env('APP_MODE') == 'DEMO')
                                                    <input type="text" name="pusher_app_key" value="test.pusher-933-key"
                                                        class="form-control">
                                                @else
                                                    <input type="text" name="pusher_app_key"
                                                        value="{{ $setting->pusher_app_key }}" class="form-control">
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">{{ __('App Secret') }}</label>
                                                @if (env('APP_MODE') == 'DEMO')
                                                    <input type="text" name="pusher_app_secret"
                                                        value="test-app-393-secret" class="form-control">
                                                @else
                                                    <input type="text" name="pusher_app_secret"
                                                        value="{{ $setting->pusher_app_secret }}" class="form-control">
                                                @endif
                                            </div>

                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">{{ __('App Cluster') }}</label>
                                                @if (env('APP_MODE') == 'DEMO')
                                                    <input type="text" name="pusher_app_cluster" value="123"
                                                        class="form-control">
                                                @else
                                                    <input type="text" name="pusher_app_cluster"
                                                        value="{{ $setting->pusher_app_cluster }}" class="form-control">
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="pusher_status">{{ __('Status') }}</label>
                                                <select name="pusher_status" id="pusher_status" class="form-control">
                                                    <option {{ $setting->pusher_status == 'active' ? 'selected' : '' }}
                                                        value="active">{{ __('Enable') }}</option>
                                                    <option {{ $setting->pusher_status == 'inactive' ? 'selected' : '' }}
                                                        value="inactive">{{ __('Disable') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <button type="submit" class="btn btn-success">{{ __('Update') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
