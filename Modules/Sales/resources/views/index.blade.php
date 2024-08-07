@extends('admin.master_layout')
@section('title')
    <title>{{ $title }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Invoice No') }}</th>
                                                <th>{{ __('Customer') }}</th>
                                                <th>{{ __('Sale By') }}</th>
                                                <th>{{ __('Sale Amount') }}</th>
                                                <th>{{ __('Total Amount') }}</th>
                                                <th>{{ __('Paid Amount') }}</th>
                                                <th>{{ __('Due') }}</th>
                                                <th>{{ __('Payment Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sales as $key => $sale)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $sale->order_date }}</td>
                                                    <td>{{ $sale->invoice }}</td>
                                                    <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                                                    <td>{{ $sale->user->name }}</td>
                                                    <td>{{ $sale->grand_total }}</td>
                                                    <td>{{ $sale->grand_total }}</td>
                                                    <td>{{ $sale->paid_amount }}</td>
                                                    <td>{{ $sale->grand_total - $sale->paid_amount }}</td>
                                                    <td>
                                                        @if ($sale->payment->sum('amount') == $sale->grand_total)
                                                            <span class="badge badge-success">Paid</span>
                                                        @elseif ($sale->payment->sum('amount') > 0)
                                                            <span class="badge badge-danger">Partial Due</span>
                                                        @else
                                                            <span class="badge badge-danger">Due</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group mb-2">
                                                            <button class="btn btn-info btn-sm dropdown-toggle"
                                                                type="button" data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item view-sale" href="javascript:;"
                                                                    data-id="{{ $sale->id }}">View</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ url('admin/order-edit/' . $sale->id) }}">Edit</a>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                    onclick="deleteData({{ $sale->id }})">Delete</a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.sales.return.create', $sale->id) }}">Sale
                                                                    Return</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
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
            $('#deleteForm').attr('action', "{{ url('admin/order-delete') }}/" + id);
            modal.modal('show');
        }
    </script>
@endpush
