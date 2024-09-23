@extends('admin.master_layout')
@section('title')
    <title>{{ __('Customer Due Receive') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Customer Due Receive') }}</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.purchase.index') }}">{{ __('Customer Due Receive') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Customer Due Receive') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="">{{ __('Customer Due Receive') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        {{ __('SL') }}
                                                    </th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Invoice No') }}</th>
                                                    <th>{{ __('Customer') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Receive By') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($payments as $payment)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ now()->parse($payment->payment_date)->format('d M , Y') }}
                                                        </td>
                                                        <td>{{ $payment->sale?->invoice }}</td>
                                                        <td>{{ $payment->customer->name }}</td>
                                                        <td>{{ $payment->amount }}</td>
                                                        <td>{{ $payment->createdBy->name }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('admin.customer.due-receive.edit', $payment->id) }}"
                                                                    class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>

                                                                <a href="javascript:;" class="btn btn-danger btn-sm"
                                                                    onclick="deleteData({{ $payment->id }})">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
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
            </div>
        </section>
    </div>
@endsection
@push('js')
    <script>
        function deleteData(id) {
            let url = "{{ route('admin.customer.due-receive.delete', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
