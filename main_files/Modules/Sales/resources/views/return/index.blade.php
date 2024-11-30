@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Sales Return List') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Sl') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Invoice No') }}</th>
                                                {{-- <th style="display: none;">Business Branch</th> --}}
                                                <th>{{ __('Customer') }}</th>
                                                <th>{{ __('Total Amount') }}</th>
                                                <th>{{ __('Paying Amount') }}</th>
                                                <th>{{ __('Payment Status') }}</th>
                                                <th>{{ __('Due') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lists as $key => $sale)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $sale->return_date }}</td>
                                                    <td>{{ $sale->sale->invoice }}</td>
                                                    <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                                                    <td>{{ $sale->return_amount }}</td>
                                                    <td>{{ $sale->return_amount - $sale->return_due }}</td>
                                                    <td>
                                                        @if ($sale->return_due == 0)
                                                            <span class="badge badge-success">{{ __('Paid') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('Due') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $sale->return_due }}</td>
                                                    <td>
                                                        <div class="btn-group mb-2">
                                                            <button class="btn btn-info btn-sm dropdown-toggle"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-haspopup="true"
                                                                aria-expanded="false">{{ __('Action') }}</button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item view-sale" href="javascript:;"
                                                                    data-id="{{ $sale->id }}">{{ __('View') }}</a>

                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.sales.edit', $sale->id) }}">{{ __('Edit') }}</a>


                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    onclick="deleteData({{ $sale->id }})">{{ __('Delete') }}</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('components.admin.preloader')

    <div class="modal fade bd-example-modal-xl" id="salemodal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent" style="width: 100%">

            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        'use strict'

        $(document).ready(function() {
            $(document).on('click', '.view-sale', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.sales.show', '') }}/" + id,
                    success: function(data) {
                        $('#modalcontent').html(data);
                        $('#salemodal').modal('show');
                    }
                });
            })
        })

        function deleteData(id) {
            const modal = $('#deleteModal');
            $('#deleteForm').attr('action', "{{ route('admin.sales.destroy', '') }}/" + id);
            modal.modal('show');
        }
    </script>
@endpush
