@extends('admin.master_layout')
@section('title')
    <title>{{ __('Current Stock') }}</title>
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
                <h1>{{ __('Stock') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <img src="{{ asset($product->single_image) }}" class="img-fluid"
                                            alt="Product Picture" width="100">
                                    </div>
                                    <div class="col-md-9">
                                        <h5>{{ __('Product Details') }}</h5>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 35%">{{ __('Name') }}</th>
                                                <th>{{ $product->name }}</th>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Category') }}</th>
                                                <td>{{ $product->category->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Brand') }}</th>
                                                <td>{{ $product->brand->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Unit') }}</th>
                                                <td>{{ $product->unit->name }}</td>
                                            </tr>
                                        </table>
                                        <table class="table table-bordered text-center">
                                            <tr>
                                                <th>{{ __('Barcode') }}</th>
                                                <th>{{ __('Purchase Price') }}</th>
                                                <th>{{ __('Selling Price') }}</th>
                                                <th>{{ __('Qty') }}</th>
                                            </tr>
                                            <tr>
                                                <th>{{ $product->barcode }}</th>
                                                <th>{{ currency($product->cost) }}</th>
                                                <th>{{ currency($product->selling_price) }}</th>
                                                <th>{{ $product->stock }}</th>
                                            </tr>
                                        </table>
                                    </div>
                                    {{-- <div class="col-md-12">
                                        <h5>Stock Details</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Sl</th>
                                                    <th>Branch</th>
                                                    <th>In Quantity</th>
                                                    <th>Out Quantity</th>
                                                    <th>Stock Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Quick Shifter</td>
                                                    <td style="vertical-align: top !important; ">
                                                        4 Piece
                                                    </td>
                                                    <td style="vertical-align: top !important; ">
                                                        0 Piece
                                                    </td>
                                                    <td style="vertical-align: top !important; ">
                                                        4 Piece

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div> --}}

                                    {{-- <div class="col-md-12 mt-4" style="display: none;">
                                        <h5>Stock Transfer Details</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Sl</th>
                                                    <th>Branch</th>
                                                    <th>Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
