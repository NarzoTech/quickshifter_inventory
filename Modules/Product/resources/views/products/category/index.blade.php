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
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="{{ route('admin.category.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i>
                                        {{ __('Add Category') }}</a>
                                </h4>
                                <div class="card-header-form">
                                    <form id="product_search_form">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="{{ __('Search here..') }}" autocomplete="off"
                                                value="{{ request()->get('search') }}">
                                            <div class="input-group-btn">
                                                <button class="btn btn-primary" style="padding:9px"><i
                                                        class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="alert alert-danger d-none justify-content-between delete-section danger-bg">
                                    <span><span class="number">0 </span> rows selected</span>
                                    <button class="btn btn-danger delete-button">Delete</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr>
                                            <th>
                                                <div class="custom-checkbox custom-control">
                                                    <input type="checkbox" data-checkboxes="checkgroup"
                                                        data-checkbox-role="dad" class="custom-control-input"
                                                        id="checkbox-all">
                                                    <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                                </div>
                                            </th>
                                            <th>{{ __('SL.') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Parent Name') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        <tr>
                                            @foreach ($categories as $index => $category)
                                        <tr>
                                            <td>
                                                <div class="custom-checkbox custom-control">
                                                    <input type="checkbox" data-checkboxes="checkgroup"
                                                        class="custom-control-input" id="checkbox-{{ $category->id }}"
                                                        name="select">
                                                    <label for="checkbox-{{ $category->id }}"
                                                        class="custom-control-label">&nbsp;</label>
                                                </div>
                                            </td>
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
                                                    class="btn btn-primary btn-sm mr-1" data-toggle="tooltip"
                                                    title="{{ __('Edit') }}"><i class="fas fa-pencil-alt"></i></a>

                                                <a href="javascript:void(0)"
                                                    class="btn btn-danger trigger--fire-modal-1 deleteForm btn-sm"
                                                    data-toggle="modal" data-target="#deleteModal" data-toggle="tooltip"
                                                    title="{{ __('Delete') }}"
                                                    data-url="{{ route('admin.category.destroy', $category->id) }}"
                                                    data-form="deleteForm"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
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


            //check all checkboxes
            $('#checkbox-all').on('click', function() {
                var $this = $(this);
                var check = $this.prop('checked');
                $('input[name="select"]').each(function() {
                    $(this).prop('checked', check);

                    // change the count number
                    if (check) {
                        $('.number').text($('input[name="select"]').length);
                        $('.delete-section').removeClass('d-none');
                        $('.delete-section').addClass('d-flex');

                    } else {
                        $('.number').text(0);
                        $('.delete-section').addClass('d-none');
                        $('.delete-section').removeClass('d-flex');
                    }
                });
            });

            $('input[name="select"]').on('click', function() {
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }
                $('.number').text(number);

                if (number > 0) {
                    $('.delete-section').removeClass('d-none');
                    $('.delete-section').addClass('d-flex');
                } else {
                    $('.delete-section').addClass('d-none');
                    $('.delete-section').removeClass('d-flex');
                }
            });

            // delete all selected
            $('.delete-button').on('click', function() {
                var ids = [];
                $('input[name="select"]:checked').each(function() {
                    ids.push($(this).attr('id').split('-')[1]);
                });


                // fire swal
                swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this data!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.category.deleteSelected') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: ids
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    toastr.error(response.message);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
