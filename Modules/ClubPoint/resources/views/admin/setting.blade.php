@extends('admin.master_layout')
@section('title')
<title>{{ __('Club Point Configuration') }}</title>
@endsection
@section('admin-content')

<div class="main-content">

    <section class="section">
        <div class="section-header">
            <h1>{{ __('Club Point Configuration') }}</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ route('admin.update-clubpoint-setting') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="name">{{ __('1 USD = Clubpoint ?') }}</label>
                                            <input type="number" name="club_point_rate" value="{{ $setting->club_point_rate }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="club_point_status">{{ __('Status') }}</label>
                                            <select name="club_point_status" id="club_point_status" class="form-control">
                                                <option {{ $setting->club_point_status=='active' ? 'selected' :'' }} value="active">{{ __('Enable') }}</option>
                                                <option {{ $setting->club_point_status=='inactive' ? 'selected' :'' }} value="inactive">{{ __('Disable') }}</option>
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
