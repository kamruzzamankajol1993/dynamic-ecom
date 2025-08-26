@extends('admin.master.master')
@section('title', 'Edit Bundle Offer')
@section('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Edit Bundle Offer</h2>
        <form action="{{ route('bundle-offer.update', $bundleOffer->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-7">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Offer Details</h5>
                            <div class="mb-3">
                                <label class="form-label">Offer Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $bundleOffer->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Offer Title</label>
                                <input type="text" name="title" class="form-control" value="{{ $bundleOffer->title }}" required>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="status" @if($bundleOffer->status) checked @endif>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="mb-0">Offer Tiers</h5>
                            <button type="button" id="add-tier-btn" class="btn btn-sm btn-success">Add Tier</button>
                        </div>
                        <div class="card-body">
                            <div id="tier-container">
                                @foreach($bundleOffer->tiers as $tierIndex => $tier)
                                <div class="row align-items-center mb-2 tier-row">
                                    <div class="col-md-3"><input type="number" name="tiers[{{$tierIndex}}][buy_quantity]" class="form-control" value="{{$tier->buy_quantity}}" required min="2"></div>
                                    <div class="col-md-4"><input type="number" name="tiers[{{$tierIndex}}][offer_price]" class="form-control" value="{{$tier->offer_price}}" required step="0.01"></div>
                                    <div class="col-md-3"><input type="number" name="tiers[{{$tierIndex}}][get_quantity]" class="form-control" value="{{$tier->get_quantity}}"></div>
                                    <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-tier-btn">&times;</button></div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Eligible Products</h5>
                        </div>
                        <div class="card-body">
                            <input type="text" id="productSearch" class="form-control mb-3" placeholder="Search for products or variants...">
                            <ul id="product-list" class="list-group">
                                @foreach($bundleOffer->products as $bundleProduct)
                                    @php
                                        $item = $bundleProduct->productVariant ?? $bundleProduct->product;
                                        $type = $bundleProduct->product_variant_id ? 'variant' : 'product';
                                        $name = $type === 'variant' ? ($item->product->name . ' - ' . $item->color->name) : $item->name;
                                        $uniqueId = $type . '-' . $item->id;
                                    @endphp
                                    <li class="list-group-item d-flex justify-content-between align-items-center" id="{{ $uniqueId }}">
                                        {{ $name }}
                                        <input type="hidden" name="products[{{ $uniqueId }}][id]" value="{{ $item->id }}">
                                        <input type="hidden" name="products[{{ $uniqueId }}][type]" value="{{ $type }}">
                                        <button type="button" class="btn-close remove-product-btn"></button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-4">Update Offer</button>
        </form>
    </div>
</main>
@endsection
@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
$(document).ready(function() {
    let tierIndex = {{ $bundleOffer->tiers->count() }};
    $('#add-tier-btn').on('click', function() {
        const tierHtml = `
            <div class="row align-items-center mb-2 tier-row">
                <div class="col-md-3"><input type="number" name="tiers[${tierIndex}][buy_quantity]" class="form-control" placeholder="Buy Qty" required min="2"></div>
                <div class="col-md-4"><input type="number" name="tiers[${tierIndex}][offer_price]" class="form-control" placeholder="Offer Price" required step="0.01"></div>
                <div class="col-md-3"><input type="number" name="tiers[${tierIndex}][get_quantity]" class="form-control" placeholder="Get Free Qty"></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-tier-btn">&times;</button></div>
            </div>`;
        $('#tier-container').append(tierHtml);
        tierIndex++;
    });
    $('#tier-container').on('click', '.remove-tier-btn', function() {
        $(this).closest('.tier-row').remove();
    });

    $("#productSearch").autocomplete({
        source: "{{ route('bundle-offer.search-products') }}",
        minLength: 2,
        select: function(event, ui) {
            addProductToList(ui.item);
            $(this).val('');
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return $("<li>").append(`<div>${item.label}</div>`).appendTo(ul);
    };

    function addProductToList(item) {
        const uniqueId = `${item.type}-${item.id}`;
        if ($(`#${uniqueId}`).length === 0) {
            const productHtml = `
                <li class="list-group-item d-flex justify-content-between align-items-center" id="${uniqueId}">
                    ${item.name}
                    <input type="hidden" name="products[${uniqueId}][id]" value="${item.id}">
                    <input type="hidden" name="products[${uniqueId}][type]" value="${item.type}">
                    <button type="button" class="btn-close remove-product-btn"></button>
                </li>`;
            $('#product-list').append(productHtml);
        }
    }
    $('#product-list').on('click', '.remove-product-btn', function() {
        $(this).closest('li').remove();
    });
});
</script>
@endsection
