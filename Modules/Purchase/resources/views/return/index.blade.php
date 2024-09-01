@extends('admin.master_layout')
@section('title')
    <title>{{ __('Purchases Return') }}</title>
@endsection

@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Purchases Return') }}</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.purchase.index') }}">{{ __('Purchases Return') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Purchases Return') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
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
                                                    {{-- @dd($list) --}}
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
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    Action
                                                                </button>
                                                                <div class="dropdown-menu"
                                                                    aria-labelledby="btnGroupDrop{{ $list->id }}">
                                                                    <a class="dropdown-item" href="javascript:;"
                                                                        data-toggle="modal"
                                                                        data-target="#editType{{ $list->id }}">Edit</a>
                                                                    <a href="javascript:;" data-toggle="modal"
                                                                        data-target="#deleteModal" class="dropdown-item"
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
        </section>
    </div>
@endsection
@push('js')
    <script>
        function deleteData(id) {
            $("#deleteForm").attr("action", '{{ route('admin.purchase.return.type.destroy', '') }}' + "/" + id)
        }
    </script>
@endpush
