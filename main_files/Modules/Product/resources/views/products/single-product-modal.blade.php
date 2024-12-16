<div class="modal-header mb-0">
    <h4 class="modal-title">View Product</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-9 col-md-9">
            <div class="row">
                <div class="col-sm-6 invoice-col">
                    <p class="mb-2"><b>Product Name: </b> {{ $product->name }}</p>
                    <p class="mb-2"><b>Barcode:</b>{{ $product->barcode }}</p>
                    <p class="mb-2"><b>Brand: </b>{{ $product->brand->name }}</p>
                    <p class="mb-2"><b>Unit: </b>{{ $product->unit->name }}</p>
                    <p class="mb-2"> <b class="d-none">Available in locations: </b> Quick Shifter</p>
                    <p class="mb-2"><b>Created At: </b>{{ $product->created_at->format('d F, Y') }}</p>
                </div>

                <div class="col-sm-6 invoice-col">
                    <p class="mb-2"><b>Category: </b>{{ $product->category->name }}</p>
                    <p class="mb-2"><b>Manage Stock: </b>{{ $product->stock_alert ? 'Yes' : 'No' }}</p>
                    <p class="mb-2"><b>Alert quantity: </b>{{ $product->stock_alert }} </p>
                    <p class="mb-2"><b>Has IMEI/Model No: </b>{{ $product->hasVariant ? 'Yes' : 'No' }} </p>
                    <p class="mb-2"><b>Updated At: </b>{{ $product->updated_at->format('d F, Y') }}</p>
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-sm-12"> </div>
            </div>
        </div>

        <div class="col-sm-3 col-md-3">
            <div class="thumbnail view_product">
                <img src="{{ $product->singleImage }}" class="img-fluid" alt="Product image">
            </div>
        </div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-12">
            <strong>Product Stock Details</strong>
        </div>
        <div class="col-md-12" id="view_product_stock_details" data-product_id="82">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table mb-0 table-condensed">
                            <thead>
                                <tr>
                                    <th><b>Barcode</b></th>
                                    <th><b>Unit price</b></th>
                                    <th><b>stock</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $product->barcode }}</td>
                                    <td>{{ $product->current_price }}</td>
                                    <td>{{ $product->stock }} {{ $product->unit->ShortName }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>
