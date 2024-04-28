@extends('admin.master_layout')
@section('title')
    <title>{{ __('Create Plan') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Create Plan') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">

                                <a href="{{ route('admin.subscription-plan.index') }}" class="btn btn-primary"><i
                                        class="fa fa-arrow-left"></i> {{ __('Go Back') }}</a>

                            </div>

                            <div class="card-body">

                                <form action="{{ route('admin.subscription-plan.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <label for="">{{ __('Plan Name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="plan_name" class="form-control form_control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">{{ __('Plan Price') }} <span data-toggle="tooltip"
                                                    data-placement="top" class="fa fa-info-circle text--primary"
                                                    title="For free plan use(0.00), price should be USD"> <span
                                                        class="text-danger">*</span></label>
                                            <input type="text" name="plan_price" class="form-control form_control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">{{ __('Expiration Date') }} <span
                                                    class="text-danger">*</span></label>

                                            <select name="expiration_date" id="" class="form-control">
                                                <option value="monthly">{{ __('Monthly') }}</option>
                                                <option value="yearly">{{ __('Yearly') }}</option>
                                                <option value="lifetime">{{ __('Lifetime') }}</option>
                                            </select>

                                        </div>

                                        {{-- write your extra input field here depand on your project requirement --}}

                                        <div class="form-group col-md-6">
                                            <label for="">{{ __('Serial') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="serial" class="form-control form_control">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="">{{ __('Status') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="status" id="" class="form-control">
                                                <option value="active">{{ __('Active') }}</option>
                                                <option value="inactive">{{ __('Inactive') }}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
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
