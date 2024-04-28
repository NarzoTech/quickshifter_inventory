@extends('admin.master_layout')
@section('title')
    <title>{{ __('Update Page') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Update Page') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.custom-pages.index') }}">{{ __('Customizeable Page List') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Update Page') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Update Page') }}</h4>
                                <div>
                                    <a href="{{ route('admin.custom-pages.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.custom-pages.update', $page->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="form-group col-md-8 offset-md-2">
                                            <label>{{ __('Title') }} <span class="text-danger">*</span></label>
                                            <input type="text" id="title" class="form-control" name="title"
                                                value="{{ old('title', $page->title) }}">
                                            @error('title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-8 offset-md-2">
                                            <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                            <textarea name="description" id="" cols="30" rows="10" class="summernote">{{ old('description', $page->description) }}</textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="text-center col-md-8 offset-md-2">
                                            <x-admin.update-button :text="__('Update')"></x-admin.update-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h3>{{ __('Manage Items') }}</h3>
                                <div class="text-right">
                                    <form @adminCan('page.component.add') action="{{ route('admin.custom-pages.items.store') }}"
                                        @endadminCan method="post">
                                        @csrf
                                        <input type="hidden" name="page_id" value="{{ $page->id }}">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <select name="component_name" class="form-control" id="">
                                                    <option value="">{{ __('Add New Component') }}</option>
                                                    @foreach ($components as $component)
                                                        <option value="{{ $component->file }}">{{ $component->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <button type="submit"
                                                    class="btn btn-success">{{ __('Add Component') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="sortable-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    {{ __('Reposition') }}
                                                </th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Component') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ui-sortable">
                                            @foreach ($page->items as $item)
                                                <tr id="id-{{ $item->id }}">
                                                    <td class="text-center align-middle">
                                                        <div class="sort-handler ui-sortable-handle">
                                                            <i class="fas fa-hand-rock"></i>
                                                        </div>
                                                    </td>

                                                    <td>{{ $item->title ?? '' }}</td>

                                                    <td>
                                                        {{ $item->component_name ?? '' }}
                                                    </td>

                                                    <td>
                                                        <input @adminCan('page.update')
                                                                onchange="changeStatus({{ $item->id }})"
                                                            @endadminCan id="status_toggle" type="checkbox"
                                                            {{ $item->status ? 'checked' : '' }} data-toggle="toggle"
                                                            data-on="{{ __('Active') }}" data-off="{{ __('Inactive') }}"
                                                            data-onstyle="success" data-offstyle="danger">
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
@endsection

@push('js')
    <script src="{{ asset('backend/js/jquery-ui.min.js') }}"></script>
    <script>
        "use strict";

        function deleteData(id) {
            $("#deleteForm").attr("action", "{{ url('/admin/custom-pages/items/') }}" + "/" + id)
        }
        "use strict";

        function changeStatus(id) {
            var isDemo = "{{ env('PROJECT_MODE') ?? 1 }}"
            if (isDemo == 0) {
                toastr.error("{{ __('This Is Demo Version. You Can Not Change Anything') }}");
                return;
            }
            $.ajax({
                type: "put",
                data: {
                    _token: '{{ csrf_token() }}',
                },
                url: "{{ url('/admin/custom-pages/items/status-update') }}" + "/" + id,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
    </script>
    @adminCan('page.update')
        <script>
            (function($) {
                "use strict";
                $("#sortable-table tbody").sortable({
                    handle: '.sort-handler',
                    update: function(e, u) {
                        var data = $(this).sortable('serialize');
                        $.ajax({
                            url: "{{ route('admin.custom-pages.items.sortPageItems', $page->id) }}",
                            type: 'get',
                            data: data,
                            success: function(response) {
                                if (response.status) {
                                    toastr.success(response.message);
                                } else {
                                    toastr.warning(response.message);
                                }
                            },
                        });
                    }
                });
            })(jQuery);
        </script>
    @endadminCan
@endpush
