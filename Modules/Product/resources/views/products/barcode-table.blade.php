@extends('admin.master_layout')
@section('title')
    <title>{{ __('Print Barcode') }}</title>
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
    <style>
        .custom-checkbox {
            margin-right: 15px;
        }
    </style>
@endpush
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Product List') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active"><a
                            href="{{ route('admin.product.index') }}">{{ __('Product List') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Print Barcode') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Print Barcode') }}</h4>
                                <div>
                                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control"
                                                    placeholder="Enter Product Name/Sku/scan barcode" id="searchProduct">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('admin.product.barcode.print') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table
                                                    class="table table-condensed table-bordered text-center table-striped"
                                                    id="purchase_entry_table">
                                                    <thead>
                                                        <tr class="bg-success text-white">
                                                            <th>Product Name</th>
                                                            <th>Barcode</th>
                                                            <th>Quantity</th>
                                                            <th><i class="fa fa-trash" aria-hidden="true"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="mytab1">

                                                    </tbody>
                                                </table>
                                            </div>
                                            <hr />
                                        </div>


                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="mt-3 d-flex align-items-center ">
                                                    <label class="custom-control pl-0">
                                                        <span class=""><b>Print : </b></span>
                                                    </label>

                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" id="shopname" name="action[]"
                                                            class="custom-control-input" value="shopname" checked>
                                                        <label class="custom-control-label" for="shopname">Shop Name</label>
                                                    </div>

                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" id="productname" name="action[]"
                                                            class="custom-control-input" value="productname" checked>
                                                        <label class="custom-control-label" for="productname">Product
                                                            Name</label>
                                                    </div>

                                                    <label class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="size_color" checked>
                                                        <span class="custom-control-label">Size Color</span>
                                                    </label>
                                                    <label class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="category">

                                                        <span class="custom-control-label">Category Name</span>
                                                    </label>
                                                    <label class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="selling_price">

                                                        <span class="custom-control-label">Selling Price</span>
                                                    </label>

                                                    <label class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="sku">

                                                        <span class="custom-control-label">Sku</span>
                                                    </label>
                                                    <label class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="include_vat">

                                                        <span class="custom-control-label">Include Vat</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <div class="fas fa-print"></div> Save
                                        </button>
                                    </div>
                                </form>
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
        $(document).ready(function() {
            $("#searchProduct").on("input", function() {
                var value = $(this).val().toLowerCase();
                $.ajax({
                    url: "{{ route('admin.product.search') }}",
                    type: "GET",
                    data: {
                        search: value
                    },
                    success: function(res) {
                        if (res.status = true) {
                            let html = `
                            <tr>
                                <td>
                                    <input type="hidden" name="product_id[]" value="${res.data.id}">
                                    ${res.data.name}
                                    Barcode : ${res.data.barcode}
                                </td>

                                <td>
                                    <input type="hidden" name="barcode_id[]" value="${res.data.barcode}">
                                    ${res.data.barcode}
                                </td>

                                <td>
                                    <input type="text" name="qty[]" value="1" class="form-control">
                                </td>

                                <td>
                                    <a href="javascript:0" class="btn btn-sm btn-danger remove-product">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            `;

                            // check if barcode already exist

                            if ($("#mytab1 tr").length > 0) {
                                $("#mytab1 tr").each(function() {
                                    let code = $(this).find("td").eq(1).text()

                                    // write a regex for remove white space
                                    code = code.replaceAll(/\s/g, '');

                                    console.log(code, res.data.barcode);
                                    if (code != res.data
                                        .barcode) {
                                        $("#mytab1").append(html);
                                    }
                                })
                            } else {
                                $("#mytab1").append(html);
                            }
                        }
                    }
                })
            });

            $(document).on('click', '.remove-product', function(e) {
                $(this).closest('tr').remove();
            })
        });
    </script>
@endpush
