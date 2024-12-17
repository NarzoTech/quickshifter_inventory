<div class="pos_product_area">
    <div class="row">
        @foreach ($services as $index => $service)
            <div class="col-6 col-md-4 col-lg-4 col-xxl-3">
                <div class="card produt_card cursor-pointer" onclick="singleAddToCart({{ $service->id }}, 'service')">
                    <div class="w-100 produt_card_img">
                        <img src="{{ $service->singleImage }}" class="card-img-top" alt="Product">
                    </div>
                    <div class="card-body">
                        <p class="card-title">
                            {{ $service->name }} <br>
                        </p>
                        <p class="price">{{ currency($service->price) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{ $services->links('pos::ajax_pagination') }}
