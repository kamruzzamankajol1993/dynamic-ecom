@forelse($products as $product)
<div class="col">
    <div class="card product-card" role="button" data-product-id="{{ $product->id }}">
        @if(isset($product->main_image[0]) && !empty($product->main_image[0]))
            <img src="{{ asset('public/uploads/' . $product->main_image[0]) }}" class="card-img-top" alt="{{ $product->name }}">
        @else
            <div class="img-placeholder"><i class="fa-solid fa-camera"></i> NO IMAGE</div>
        @endif
        <div class="card-body">
            <h6 class="card-title">{{ $product->name }}</h6>
            <div>
                <p class="product-stock text-success">STOCK: {{ $product->variants->sum(function($v) { return collect($v->sizes)->sum('quantity'); }) }}</p>
                 {{-- Updated Price Display Logic --}}
                <div class="product-price">
                    @if($product->discount_price > 0 && $product->discount_price < $product->base_price)
                        <span>৳{{ number_format($product->discount_price) }}</span>
                        <del class="text-danger small ms-1">৳{{ number_format($product->base_price) }}</del>
                    @else
                        <span>৳{{ number_format($product->base_price) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="col-12 text-center py-5">
    <p class="text-muted">No products found.</p>
</div>
@endforelse