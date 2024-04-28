@extends('admin.master_layout')
@section('title')
    <title>{{ __('Manage Menu') }}</title>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/jquery-ui.min.css') }}" />
@endpush
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Manage Menu') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Manage Menu') }}</div>
                </div>
            </div>
            <div class="section-body row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-3 service_card">{{ __('Available Translations') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="lang_list_top">
                                <ul class="lang_list">
                                    @foreach (allLanguages() as $language)
                                        <li><a id="{{ request('code') == $language->code ? 'selected-language' : '' }}"
                                                href="{{ route('admin.menus.index', $language->code) }}"><i
                                                    class="fas {{ request('code') == $language->code ? 'fa-eye' : 'fa-edit' }}"></i>
                                                {{ $language->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mt-2 alert alert-danger" role="alert">
                                @php
                                    $current_language = allLanguages()->where('code', $code)->first();
                                @endphp
                                <p>{{ __('Your editing mode') }} :
                                    <b>{{ $current_language?->name }}</b>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Manage Menu') }}</h4>
                                <div>
                                    @adminCan('menu.create')
                                        <button id="add-menu" class="btn btn-primary">
                                            <i class="pr-1 fa fa-plus"></i>{{ __('Add New') }}
                                        </button>
                                    @endadminCan
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row sort" id="menu-items">
                                    @foreach ($items as $item)
                                        <div class="mb-4 col-md-2" id="id-{{ $item->id }}">
                                            <div class="mb-2 text-white btn bg-primary w-100 menu-item"
                                                id="id-{{ $item->id }}" data-title="{{ $item->title }}"
                                                data-link="{{ $item->link }}">
                                                {{ $item->title }}
                                            </div>
                                            <div id="sub-menu" class="sort">
                                                @foreach ($item->sub_menus as $subitem)
                                                    <div class="mb-2 bg-white btn w-100 menu-item"
                                                        id="id-{{ $subitem->id }}" data-title="{{ $subitem->title }}"
                                                        data-link="{{ $subitem->link }}">
                                                        {{ $subitem->title }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            @adminCan('menu.create')
                                                <button onclick="addSubmenu({{ $item->id }})"
                                                    class="btn btn-icon btn-light w-100">
                                                    <i class="fas fa-plus-circle"></i> {{ __('Sub Menu') }}
                                                </button>
                                            @endadminCan
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="add-menu-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Menu Item') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-menu-form" action="{{ route('admin.menus.store') }}" method="post">
                        <div class="form-group ">
                            <label>{{ __('Title') }}</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group ">
                            <label>{{ __('Link') }}</label>
                            <input type="text" class="form-control" data-link="addmenu" name="link">
                        </div>
                        <button type="submit" class="float-right btn btn-primary">{{ __('Save') }}</button>
                        <button type="button" class="float-right mr-2 btn btn-danger"
                            data-dismiss="modal">{{ __('Close') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="add-sub-menu-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Sub Manu Item') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-sub-menu-form" action="{{ route('admin.menus.store') }}" method="get">
                        <input type="hidden" name="parent_id">

                        <div class="form-group">
                            <label>{{ __('Title') }}</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="form-group ">
                            <label>{{ __('Link') }}</label>
                            <input type="text" class="form-control" data-link="addmenu" name="link">
                        </div>

                        <button type="submit" class="float-right btn btn-primary">{{ __('Save') }}</button>
                        <button type="button" class="float-right mr-2 btn btn-danger"
                            data-dismiss="modal">{{ __('Close') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->

    <div class="modal fade" id="edit-menu-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Menu') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-menu-form" action="{{ route('admin.menus.store') }}" method="get">
                        <input type="hidden" name="id" required>
                        <div class="form-group ">
                            <label>{{ __('Title') }}</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="form-group ">
                            <label>{{ __('Link') }}</label>
                            <input type="text" class="form-control" data-link="addmenu" name="link">
                        </div>
                        <button type="submit" class="float-right btn btn-primary">{{ __('Save') }}</button>
                        <button class="float-right mr-2 btn btn-danger" data-dismiss="modal"
                            onclick="deleteMenu()">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('backend/js/jquery-ui.min.js') }}"></script>
    <script>
        "use strict";
        $(document).on('click', '.menu-item', function(e) {
            $('[data-link="addmenu"]').empty();

            e.stopImmediatePropagation();

            $("#edit-menu-form input[name=id]").val($(this).attr("id").replace("id-", ""));
            $("#edit-menu-form input[name=title]").val($(this).attr("data-title"));
            $("#edit-menu-form input[name=link]").val($(this).attr("data-link"));
            @adminCan('menu.edit')
            $('#edit-menu-modal').modal('show');
            @endadminCan
        });

        $("#edit-menu-form").submit(function(e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            var formData = $(`#edit-menu-form`).serializeArray();

            formData.push({
                name: '_token',
                value: '{{ csrf_token() }}'
            });
            formData.push({
                name: 'lang_code',
                value: '{{ $code }}'
            });
            $.ajax("{{ route('admin.menus.update') }}", {
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                    }
                    $('#edit-menu-modal').modal('hide');
                    setTimeout(() => {
                        location.reload(true);
                    }, 2000);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        });

        $('#add-menu').click(function(e) {
            $('[data-link="addmenu"]').empty();

            e.stopImmediatePropagation();
            $('#add-menu-modal').modal('show');
        });

        $("#add-menu-form").submit(function(e) {
            e.stopImmediatePropagation();
            e.preventDefault();

            var formData = $(`#add-menu-form`).serializeArray();
            formData.push({
                name: '_token',
                value: '{{ csrf_token() }}'
            });

            formData.push({
                name: 'lang_code',
                value: '{{ $code }}'
            });

            $.ajax("{{ route('admin.menus.store') }}", {
                type: 'POST',
                data: formData,
                success: function(data) {
                    $("#add-menu-form")[0].reset();
                    toastr.success("{{ __('Create Successfully') }}");
                    $('#menu-items').append(`
                                <div class="mb-4 col-md-2 ui-sortable-handle" id="id-${data.id}">
                                    <div class="mb-2 text-white btn bg-primary w-100 menu-item" id="id-${data.id}" data-title="${data.title}" data-link="${data.link}">
                                    ${data.title}</div>
                                    <div id="sub-menu" class="sort ui-sortable"></div>
                                    <button onclick="addSubmenu(${data.id})" class="btn btn-icon btn-light w-100">
                                        <i class="fas fa-plus-circle"></i> Sub menu
                                    </button>
                                </div>
                            `).sortable("refresh");

                    $('#add-menu-modal').modal('hide');
                }
            });
        });

        function addSubmenu(menu) {
            $('[data-link="addmenu"]').empty();

            $('#add-sub-menu-form input[name=parent_id]').val(menu);
            $('#add-sub-menu-modal').modal('show');
        }

        function deleteMenu() {
            var formData = $(`#edit-menu-form`).serializeArray();

            formData.push({
                name: '_token',
                value: '{{ csrf_token() }}'
            });

            formData.push({
                name: 'lang_code',
                value: '{{ $code }}'
            });

            $.ajax("{{ route('admin.menus.destroy') }}", {
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                    } else {
                        toastr.warning(response.message);
                    }
                    $('#edit-menu-modal').modal('hide');
                    setTimeout(() => {
                        location.reload(true);
                    }, 2000);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }

        $("#add-sub-menu-form").submit(function(e) {
            e.stopImmediatePropagation();
            e.preventDefault();

            var formData = $(`#add-sub-menu-form`).serializeArray();
            formData.push({
                name: '_token',
                value: '{{ csrf_token() }}'
            });

            formData.push({
                name: 'lang_code',
                value: '{{ $code }}'
            });

            $.ajax("{{ route('admin.menus.store') }}", {
                type: 'POST',
                data: formData,
                success: function(data) {
                    console.log(data.id);
                    $('#id-' + data.parent_id + ' #sub-menu').append(`
                    <div class="mb-2 bg-white btn w-100 menu-item ui-sortable-handle" id="id-${data.id}" data-title="${data.title}"
                        data-link="${data.link}">
                        ${data.title}
                    </div>
                    `);

                    $('#add-sub-menu-modal').modal('hide');
                    $("#add-sub-menu-form")[0].reset();
                }
            });
        });
    </script>

    @adminCan('menu.edit')
        <script>
            "use strict";
            $(".sort").sortable({
                update: function(e, u) {
                    var data = $(this).sortable('serialize');
                    $.ajax({
                        url: "{{ route('admin.sortmenu') }}",
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
        </script>
    @endadminCan
@endpush
