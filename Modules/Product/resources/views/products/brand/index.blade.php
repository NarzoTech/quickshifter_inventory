@extends('admin.master_layout')
@section('title')
    <title>{{ __('Brands') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Brands') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="{{ route('admin.brand.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i>
                                        {{ __('Add Brand') }}</a>
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SL.') }}</th>
                                                <th>{{ __('Image') }}</th>
                                                <th class="text-left">{{ __('Name') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($brands as $index => $brand)
                                                <tr>
                                                    <td>{{ $index + $brands->firstItem() }}</td>
                                                    <td><img src="{{ $brand->image_url }}" alt="" class="img-fluid"
                                                            style="width: 80px"></td>
                                                    <td class="text-left">{{ $brand->name }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.brand.edit', ['brand' => $brand->id, 'lang_code' => getSessionLanguage()]) }}"
                                                            class="btn btn-primary btn-action mr-1 btn-sm" data-toggle="tooltip"
                                                            title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                                        <a href="javascript:;" data-target="#deleteModal" data-toggle="modal"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="deleteData({{ $brand->id }})"><i
                                                                class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $brands->links() }}
                                </div>
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
        $(document).ready(function() {
            'use strict';
        });

        function deleteData(id) {
            $("#deleteForm").attr("action", '{{ route('admin.brand.destroy', '') }}' + "/" + id)
        }
    </script>
@endpush
