@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Purchases Return List') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="section_title">{{ __('Purchases Return List') }}</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                {{ __('SL') }}
                                            </th>
                                            <th>{{ __('Invoice') }}</th>
                                            <th>{{ __('Return Date') }}</th>
                                            <th>{{ __('Return Type') }}</th>
                                            <th>{{ __('Supplier') }}</th>
                                            <th>{{ __('Total Amount') }}</th>
                                            <th>{{ __('Total Received') }}</th>
                                            <th>{{ __('Return By') }}</th>
                                            <th>{{ __('Updated By') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($returns as $list)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $list->invoice_number }}</td>
                                                <td>{{ now()->parse($list->return_date)->format('d M, Y') }}</td>
                                                <td>{{ $list->returnType?->name }}</td>
                                                <td>{{ $list->purchase?->supplier?->name }}</td>
                                                <td>{{ currency($list->return_amount) }}</td>
                                                <td>{{ currency($list->received_amount) }}</td>
                                                <td>{{ $list->createdBy->name }}</td>
                                                <td>{{ $list->updatedBy->name }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop{{ $list->id }}" type="button"
                                                            class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu"
                                                            aria-labelledby="btnGroupDrop{{ $list->id }}">
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.purchase.return.edit', $list->id) }}">Edit</a>
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $list->id }})">
                                                                Delete</a>
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
    </div>
@endsection
@push('js')
    <script>
        function deleteData(id) {
            let url = "{{ route('admin.purchase.return.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
