@extends('admin.master_layout')
@section('title')
    <title>{{ __('Attribute') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Attribute') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="{{ route('admin.attribute.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i>
                                        {{ __('Add Attribute') }}</a>
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SL.') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Values') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($attributes as $attribute)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $attribute->name }}</td>
                                                    <td>
                                                        @foreach ($attribute->values as $val)
                                                            {{ $val->name }}@if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.attribute.edit', $attribute->id) }}"
                                                            class="btn btn-primary btn-action mr-1" data-toggle="tooltip"
                                                            title="{{ __('Edit') }}"><i
                                                                class="fas fa-pencil-alt"></i></a>
                                                        <a href="javascript:void(0)"
                                                            class="btn btn-danger btn-action trigger--fire-modal-1 deleteForm"
                                                            data-toggle="modal" title="{{ __('Delete') }}"
                                                            data-url="{{ route('admin.attribute.destroy', $attribute->id) }}"
                                                            data-form="deleteForm" data-id="{{ $attribute->id }}"><i
                                                                class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                                @empty
                                                    <x-empty-table :name="__('attribute')" route="admin.attribute.create"
                                                        create="yes" :message="__('No data found!')" colspan="5"></x-empty-table>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="float-right">
                                        {{ $attributes->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="confirm-availibility">
            <div class="modal-dialog" role="document">
                <form action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="attribute_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-danger">
                                {{ __('Attribute has values. Sure to Delete?') }}
                            </p>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('Yes, Delete') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @include('components.admin.preloader')
    @endsection


    @push('js')
        <script>
            $(document).ready(function() {
                'use strict';
                $('.deleteForm').on('click', function() {
                    $('.preloader_area').removeClass('d-none')
                    // var url = $(this).data('url');
                    // $('#deleteForm').attr('action', url);

                    const id = $(this).data('id');

                    const route = "{{ route('admin.attribute.destroy', '') }}/" + id;
                    $.ajax({
                        url: "{{ route('admin.attribute.has-value') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            attribute_id: id
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                $('#confirm-availibility').modal('show');
                                $('#confirm-availibility').find('form').attr('action', route);

                                $('[name="attribute_id"]').val(id);
                            } else {
                                $('#deleteForm').attr('action', route);
                            }

                            $('.preloader_area').addClass('d-none')
                        }
                    });
                });
            });
        </script>
    @endpush
