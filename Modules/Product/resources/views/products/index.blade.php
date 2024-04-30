@extends('admin.master_layout')
@section('title')
    <title>{{ __('Product') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Product') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="{{ route('admin.product.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i>
                                        {{ __('Add Product') }}</a>
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>

                                            <tr>
                                                <th width="5%">{{ __('SN') }}</th>
                                                <th width="30%">{{ __('Name') }}</th>
                                                <th width="10%">{{ __('Price') }}</th>
                                                <th width="15%">{{ __('Photo') }}</th>
                                                <th width="15%">{{ __('Type') }}</th>
                                                <th width="10%">{{ __('Status') }}</th>
                                                <th width="15%">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($products as $index => $product)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td>{{ $product->name }}
                                                    </td>
                                                    <td>{{ currency($product->actual_price) }}</td>
                                                    <td> <img class="rounded-circle" src="{{ asset($product->image_url) }}"
                                                            alt="" width="100px" height="100px"></td>
                                                    <td>
                                                        @if ($product->is_new == 1)
                                                            {{ __('New Arrival') }}
                                                        @elseif ($product->is_featured == 1)
                                                            {{ __('Featured Product') }}
                                                        @elseif ($product->is_bestseller == 1)
                                                            {{ __('Best Selling Product') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($product->status == 1)
                                                            <a href="javascript:;"
                                                                onclick="changeProductStatus({{ $product->id }})">
                                                                <input id="status_toggle" type="checkbox" checked
                                                                    data-toggle="toggle" data-on="{{ __('Active') }}"
                                                                    data-off="{{ __('InActive') }}" data-onstyle="success"
                                                                    data-offstyle="danger">
                                                            </a>
                                                        @else
                                                            <a href="javascript:;"
                                                                onclick="changeProductStatus({{ $product->id }})">
                                                                <input id="status_toggle" type="checkbox"
                                                                    data-toggle="toggle" data-on="{{ __('Active') }}"
                                                                    data-off="{{ __('InActive') }}" data-onstyle="success"
                                                                    data-offstyle="danger">
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.product.edit', ['product' => $product->id, 'code' => getSessionLanguage()]) }}"
                                                            class="btn btn-primary btn-sm"><i class="fa fa-edit"
                                                                aria-hidden="true"></i></a>

                                                        <div class="dropdown d-inline">
                                                            <button class="btn btn-primary btn-sm dropdown-toggle"
                                                                type="button" id="dropdownMenuButton2"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="fas fa-cog"></i>
                                                            </button>
                                                            <button type="button" data-toggle="modal"
                                                                @if ($product->orders->count() > 0) data-target="#canNotDeleteModal"
                                                                @else
                                                                data-target="#deleteModal" onclick="deleteData({{ $product->id }})" @endif
                                                                class="btn btn-danger btn-sm">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </button>

                                                            <div class="dropdown-menu" x-placement="top-start"
                                                                style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -131px, 0px);">
                                                                <a class="dropdown-item has-icon"
                                                                    href="{{ route('admin.product-gallery', $product->id) }}"><i
                                                                        class="far fa-image"></i>
                                                                    {{ __('Image Gallery') }}</a>

                                                                <a class="dropdown-item has-icon"
                                                                    href="{{ route('admin.related-products', $product->id) }}"><i
                                                                        class="fas fa-lightbulb"></i>
                                                                    {{ __('Related Products') }}</a>

                                                                <a class="dropdown-item has-icon"
                                                                    href="{{ route('admin.product-variant', $product->id) }}"><i
                                                                        class="fas fa-cog"></i>{{ __('Product Variant') }}</a>

                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    {{ __('You can not delete this product. Because there are one or more order has been created in this product.') }}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            'use strict';
        });

        function deleteData(id) {
            var id = id;
            var url = '{{ route('admin.product.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
        }
    </script>
@endpush
