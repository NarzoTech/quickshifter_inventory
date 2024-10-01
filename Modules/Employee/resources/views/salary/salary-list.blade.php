@extends('admin.master_layout')
@section('title')
    <title>{{ __('All Paid Salary') }}</title>
@endsection

@push('css')
    <style>
        thead tr:nth-child(odd) {
            background-color: lightskyblue;

        }


        thead tr:nth-child(even) {
            background-color: lightpink;
        }

        thead>tr>th {
            /* background-color: lightseagreen; */
            color: white !important;
        }
    </style>
@endpush
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Employee List') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.accounts.index') }}" method="GET" class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group search-wrapper">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="{{ __('Search') }}">
                                            <button type="submit">
                                                <i class="far fa-arrow-alt-circle-right"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="1" {{ request('order_by') == '1' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="0" {{ request('order_by') == '0' ? 'selected' : '' }}>
                                                    {{ __('DESC') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="par-page" id="par-page" class="form-control">
                                                <option value="">{{ __('Per Page') }}</option>
                                                <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('10') }}
                                                </option>
                                                <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('50') }}
                                                </option>
                                                <option value="100"
                                                    {{ '100' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('100') }}
                                                </option>
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                </h4>
                            </div>
                            <div class="card-body"></div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Sl') }}</th>
                                            <th>{{ __('Employee') }}</th>
                                            <th>{{ __('Paid') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            {{-- <th style="display: none;">Business Branch</th> --}}
                                            <th>{{ __('Note') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($payments as $index => $payment)
                                            <tr>
                                                <td>{{ $payments->firstItem() + $index }}</td>
                                                <td>{{ $payment->employee?->name }}</td>
                                                <td>{{ currency($payment->amount) }}</td>
                                                <td>{{ now()->parse($payment->date)->format('d-m-Y') }}</td>
                                                <td>{{ $payment->note }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.employee.salary.edit', $payment->id) }}"
                                                            class="btn btn-primary btn-sm mr-2"><i
                                                                class="fa fa-edit"></i></a>
                                                        <a href="javascript:;" class="btn btn-danger btn-sm"
                                                            onclick="deleteData({{ $payment->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <x-empty-table :name="__('Bank')" route="" create="no" :message="__('No data found!')"
                                                colspan="10"></x-empty-table>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if (request()->get('par-page') !== 'all')
                                <div class="float-right">
                                    {{ $payments->onEachSide(0)->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.employee.salary.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
