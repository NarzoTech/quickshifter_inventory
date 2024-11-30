@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Supplier Due Pay') }}</title>
@endsection

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4><i class="fas fa-list"></i>Supplier Due Pay</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn btn-primary export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn btn-success export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                {{ __('SL') }}
                            </th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Paid By') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ now()->parse($payment->payment_date)->format('d M , Y') }}
                                </td>
                                <td>{{ $payment->purchase?->invoice_number }}</td>
                                <td>{{ $payment->supplier->name }}</td>
                                <td>{{ $payment->amount }}</td>
                                <td>{{ $payment->createdBy->name }}</td>
                                <td>
                                    <div class="btn-group">
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
@endsection


@push('js')
    <script>
        function deleteData(id) {
            let url = "{{ route('admin.supplier.due-receive.delete', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
