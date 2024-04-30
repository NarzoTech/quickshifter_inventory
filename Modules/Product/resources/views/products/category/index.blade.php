@extends('admin.master_layout')
@section('title')
    <title>{{ __('Category') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Category') }}</h1>
            </div>

            <div class="section-body">
                <div class="row mt-4">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <form id="product_search_form" class="pos_pro_search_form w-100">
                                    <div class="row">
                                        <div class="col-md-5 d-flex align-items-center  mx-auto">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="{{ __('Search here..') }}" autocomplete="off"
                                                value="{{ request()->get('search') }}">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="{{ route('admin.category.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i>
                                        {{ __('Add Category') }}</a>
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SL.') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Parent Name') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($categories as $category)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $category->name }}</td>
                                                    <td>
                                                        @if ($category->parent_id)
                                                            {{ $category->parent->name }}
                                                        @else
                                                            {{ __('N/A') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.category.edit', $category->id) }}"
                                                            class="btn btn-primary btn-action mr-1" data-toggle="tooltip"
                                                            title="{{ __('Edit') }}"><i
                                                                class="fas fa-pencil-alt"></i></a>
                                                        <a href="javascript:void(0)"
                                                            class="btn btn-danger btn-action trigger--fire-modal-1 deleteForm"
                                                            data-toggle="modal" data-target="#deleteModal"
                                                            data-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-url="{{ route('admin.category.destroy', $category->id) }}"
                                                            data-form="deleteForm"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('Category')" route="admin.category.create"
                                                    create="yes" :message="__('No data found!')" colspan="5"></x-empty-table>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $categories->links() }}
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
            $('.deleteForm').on('click', function() {
                var url = $(this).data('url');
                $('#deleteForm').attr('action', url);
            });
            $('[name="search"]').on('change', function() {
                $('#product_search_form').submit();
            })
        });
    </script>
@endpush
