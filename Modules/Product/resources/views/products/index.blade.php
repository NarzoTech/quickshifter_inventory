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
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Photo') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Barcode') }}</th>
                                                <th>{{ __('Stock Qty') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('After Disc. P.') }}</th>
                                                <th>{{ __('Brand') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                {{-- <th>{{ __('Sub Category') }}</th> --}}
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($products as $index => $product)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td> <img class="rounded-circle" src="{{ $product->singleImage }}"
                                                            alt="" width="100px" height="100px"></td>
                                                    <td>{{ $product->name }} </td>
                                                    <td>{{ $product->barcode }}</td>
                                                    <td>{{ $product->stock }}{{ $product->unit->ShortName }}</td>
                                                    <td>{{ $product->current_price }}</td>
                                                    <td>{{ $product->current_price }}</td>
                                                    <td>{{ $product->brand->name }}</td>
                                                    <td>{{ $product->category->name }}</td>
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
                                                    <td class="d-flex justify-content-center align-items-center">


                                                        <button type="button" data-toggle="modal"
                                                            @if ($product->orders->count() > 0) data-target="#canNotDeleteModal"
                                                                @else
                                                                data-target="#deleteModal" onclick="deleteData({{ $product->id }})" @endif
                                                            class="btn btn-danger btn-sm mr-2">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                        <div class="dropdown d-inline">
                                                            <button class="btn btn-primary btn-sm dropdown-toggle"
                                                                type="button" id="dropdownMenuButton2"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="fas fa-cog"></i>
                                                            </button>

                                                            <div class="dropdown-menu" x-placement="top-start"
                                                                style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -131px, 0px);">
                                                                <a href="javascript:;" class="dropdown-item">
                                                                    {{ __('View') }}</a>

                                                                <a href="javascript:;" class="dropdown-item"></i>
                                                                    {{ __('Details') }}</a>

                                                                <a href="{{ route('admin.product.edit', ['product' => $product->id]) }}"
                                                                    class="dropdown-item">

                                                                    {{ __('Edit') }}</a>

                                                                <a class="dropdown-item" href="javascript:;"
                                                                    onclick="status('{{ $product->id }}')"
                                                                    data-status="{{ $product->id }}">
                                                                    {{ $product->status == 1 ? 'Disable' : 'Enable' }}
                                                                </a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.product-variant', $product->id) }}">{{ __('Product Variant') }}</a>
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

        function status(id) {
            handleStatus("{{ route('admin.product.status', '') }}/" + id)

            let status = $('[data-status=' + id + ']').text()
            // remove whitespaces using regex
            status = status.replaceAll(/\s/g, '');
            $('[data-status=' + id + ']').text(status != 'Disable' ? 'Disable' :
                'Enable')
        }
    </script>
@endpush
