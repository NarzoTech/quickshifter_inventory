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
                                        <tr>
                                            <th>{{ __('SN') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Invoice No') }}</th>
                                            <th>{{ __('Customer') }}</th>
                                            <th>{{ __('Sale By') }}</th>
                                            <th>{{ __('Sale') }}</th>
                                            <th>{{ __('Total Amount') }}</th>
                                            <th>{{ __('Paid Amount') }}</th>
                                            <th>{{ __('Due') }}</th>
                                            <th>{{ __('Payment Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
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

    @push('js')
        <script>
            'use strict'

            $(document).ready(function() {
            })

            function deleteData(id) {
                const modal = $('#deleteModal');
                $('#deleteForm').attr('action', "{{ url('admin/order-delete') }}/" + id);
                modal.modal('show');
            }
        </script>
    @endpush
@endsection
