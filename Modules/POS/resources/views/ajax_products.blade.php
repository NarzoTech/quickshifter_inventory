<div class="pos_product_area">
    <div class="row">
        @foreach ($products as $product_index => $product)
            <div class="col-md-4 d-flex align-items-stretch">
                <div class="card produt_card cursor-pointer" @if($product->has_variant) onclick="load_product_model({{ $product->id }})" @else onclick="singleAddToCart({{$product->id}})" @endif>
                    <div class="w-100 produt_card_img">
                        <img src="{{ asset($product->image_url) }}" class="card-img-top" alt="Product">
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">
                            {{ $product->name }} <br>
                        </h6>
                        <h6 class="price">{{ currency($product->actual_price) }}</h6>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{ $products->links('pos::ajax_pagination') }}
