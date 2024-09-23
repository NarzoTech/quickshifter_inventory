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
                                    <a href="{{ route('admin.employee.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i>
                                        {{ __('Add New Employee') }}</a>
                                </h4>
                            </div>
                            <div class="card-body"></div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Sl') }}</th>
                                            <th>{{ __('Employee Name') }}</th>
                                            <th>{{ __('Employee Picture') }}</th>
                                            <th>{{ __('Designation') }}</th>
                                            {{-- <th style="display: none;">Business Branch</th> --}}
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Salary') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Joining Date') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employees as $index => $employee)
                                            <tr>
                                                <td>{{ ++$index }}</td>
                                                <td>{{ $employee->name }}</td>
                                                <td>
                                                    <img src="{{ $employee->image ? asset('storage/uploads/employee/' . $employee->image) : asset('storage/uploads/employee/default.png') }}"
                                                        alt="" width="50px" height="50px">
                                                </td>
                                                <td>{{ $employee->designation }}</td>
                                                {{-- <td style="display: none;">{{ $employee->business_branch->name }}</td> --}}
                                                <td>{{ $employee->mobile }}</td>
                                                <td>{{ $employee->email }}</td>
                                                <td>{{ $employee->salary }}</td>
                                                <td>
                                                    @if ($employee->status == 1)
                                                        <span class="badge badge-success">
                                                            {{ __('Active') }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            {{ __('Inactive') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $employee->join_date }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop{{ $employee->id }}" type="button"
                                                            class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">{{ __('Action') }}</button>
                                                        <div class="dropdown-menu"
                                                            aria-labelledby="btnGroupDrop{{ $employee->id }}">
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.edit', $employee->id) }}">{{ __('Edit') }}</a>


                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.salary.create', $employee->id) }}?pay=1">{{ __('Pay Salary') }}</a>

                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.salary.create', $employee->id) }}?pay=2">{{ __('Pay Advance') }}</a>

                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.employee.status', $employee->id) }}">{{ $employee->status == 1 ? __('Inactive') : __('Active') }}</a>

                                                            <a href="javascript:;" data-toggle="modal"
                                                                data-target="#deleteModal" class="dropdown-item"
                                                                onclick="deleteData({{ $employee->id }})">{{ __('Delete') }}</a>
                                                        </div>
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
                                    {{ $employees->onEachSide(0)->links() }}
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
                let url = "{{ route('admin.customer.employee.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
            }
        </script>
    @endpush
@endsection
