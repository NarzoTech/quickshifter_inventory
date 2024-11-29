@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Manage Admin') }}</title>
@endsection
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.settings') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a href="{{ route('admin.settings') }}">{{ __('Settings') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Manage Admin') }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Manage Admin') }}</h4>
                                <div>
                                    @adminCan('admin.create')
                                        <a href="{{ route('admin.admin.create') }}" class="btn btn-primary"><i
                                                class="fa fa-plus"></i> {{ __('Add New') }}</a>
                                    @endadminCan
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('Roles') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                @adminCan('admin.delete')
                                                    <th>{{ __('Action') }}</th>
                                                @endadminCan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($admins as $index => $admin)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>{{ $admin->name }}</td>
                                                    <td>{{ $admin->email }}</td>
                                                    <td>
                                                        {{ $admin->getRoleNames() }}
                                                    </td>
                                                    <td>
                                                        @if ($admin->status == 'active')
                                                            <a href="javascript:;"
                                                                onclick="changeAdminStatus({{ $admin->id }})">
                                                                <input id="status_toggle" type="checkbox" checked
                                                                    data-bs-toggle="toggle" data-on="{{ __('Active') }}"
                                                                    data-off="{{ __('Inactive') }}" data-onstyle="success"
                                                                    data-offstyle="danger">
                                                            </a>
                                                        @else
                                                            <a href="javascript:;"
                                                                onclick="changeAdminStatus({{ $admin->id }})">
                                                                <input id="status_toggle" type="checkbox"
                                                                    data-bs-toggle="toggle" data-on="{{ __('Active') }}"
                                                                    data-off="{{ __('Inactive') }}" data-onstyle="success"
                                                                    data-offstyle="danger">
                                                            </a>
                                                        @endif
                                                    </td>
                                                    @adminCan('admin.delete')
                                                        <td>
                                                            @adminCan('admin.edit')
                                                                <a href="{{ route('admin.admin.edit', $admin->id) }}"
                                                                    class="btn btn-warning btn-sm"><i class="fa fa-edit"
                                                                        aria-hidden="true"></i></a>
                                                            @endadminCan
                                                            @adminCan('admin.delete')
                                                                <a href="javascript:;" class="btn btn-danger btn-sm"
                                                                    onclick="deleteData({{ $admin->id }})"><i
                                                                        class="fa fa-trash" aria-hidden="true"></i></a>
                                                            @endadminCan
                                                        </td>
                                                    @endadminCan
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="float-right">
                                        {{ $admins->links() }}
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
            let url = "{{ route('admin.admin.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }

        function changeAdminStatus(id) {
            var isDemo = "{{ env('PROJECT_MODE') ?? 1 }}"
            if (isDemo == 0) {
                toastr.error('This Is Demo Version. You Can Not Change Anything');
                return;
            }
            $.ajax({
                type: "put",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                url: "{{ url('/admin/admin-status/') }}" + "/" + id,
                success: function(response) {
                    toastr.success(response.message)
                },
                error: function(err) {
                    console.log(err);
                }
            })
        }
    </script>
@endpush
