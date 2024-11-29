<div class="modal-body">
    <div class="row">
        <div class="col-sm-9 col-md-9">
            <div class="row">
                <div class="col-sm-6 invoice-col">
                    <b>Product Name: </b> {{ $product->name }}<br>
                    <b>Barcode:</b>{{ $product->barcode }}<br>
                    <b>Brand: </b>{{ $product->brand->name }}<br>
                    <b>Unit: </b>{{ $product->unit->name }}<br>
                    <span style="display: none;">
                        <strong>Available in locations:</strong>
                        Quick Shifter,
                    </span>
                    <br>
                    <b>Created At:
                    </b>{{ $product->created_at->format('d F, Y') }}
                </div>

                <div class="col-sm-6 invoice-col">
                    <b>Category:
                    </b>{{ $product->category->name }}<br>
                    <b>Manage Stock: </b>{{ $product->stock_alert ? 'Yes' : 'No' }}<br>
                    <b>Alert quantity: </b>{{ $product->stock_alert }} <br>
                    <b>Has IMEI/Model No: </b>{{ $product->hasVariant ? 'Yes' : 'No' }} <br>
                    <b>Updated At: </b>{{ $product->updated_at->format('d F, Y') }}
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="col-sm-12">
                </div>
            </div>
        </div>

        <div class="col-sm-3 col-md-3">
            <div class="thumbnail">
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
                        <table class="table table-condensed bg-gray">
                            <thead>
                                <tr class="bg-success">
                                    <th>Barcode</th>
                                    <th>Unit price</th>
                                    <th>stock</th>
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
    <button type="button" class="btn btn-danger" data-dismiss="modal fade">{{ __('Close') }}</button>
</div>
