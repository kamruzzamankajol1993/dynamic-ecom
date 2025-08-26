@extends('admin.master.master')
@section('title', 'Edit Product Deal')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Edit Product Deal</h2>
        <form action="{{ route('offer-product.update', $offerProduct->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Select Offer Name</label>
                        <select name="bundle_offer_id" class="form-control" required>
                            <option value="">-- Select an Offer --</option>
                            @foreach($bundleOffers as $id => $name)
                                <option value="{{ $id }}" {{ $offerProduct->bundle_offer_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deal Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $offerProduct->title }}" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Buy Quantity</label>
                            <input type="number" id="buy_quantity" name="buy_quantity" class="form-control quantity-input" value="{{ $offerProduct->buy_quantity }}" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Get Quantity</label>
                            <input type="number" id="get_quantity" name="get_quantity" class="form-control quantity-input" value="{{ $offerProduct->get_quantity }}" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Products</label>
                        <select name="product_id[]" class="form-control product-select" multiple required>
                            @foreach($selectedProducts as $product)
                                <option value="{{ $product->id }}" selected>{{ $product->name }} ({{ $product->product_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Discount Price</label>
                            <input type="number" step="0.01" name="discount_price" class="form-control" value="{{ $offerProduct->discount_price }}" placeholder="">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">Update Deal</button>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    function initializeSelect2(element, maxSelection) {
        $(element).select2({
            placeholder: 'Search and select products...',
            maximumSelectionLength: maxSelection,
            ajax: {
                url: "{{ route('ajax.bundle-offer.search-products') }}",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.id, text: `${item.name} (${item.product_code})` }
                        })
                    };
                },
                cache: true
            }
        });
    }

    // Initial setup for the edit page
    const initialBuyQty = parseInt($('#buy_quantity').val(), 10) || 0;
    const initialGetQty = parseInt($('#get_quantity').val(), 10) || 0;
    initializeSelect2('.product-select', initialBuyQty + initialGetQty);

    // Listen for changes on quantity inputs
    $('.quantity-input').on('input', function() {
        const buyQty = parseInt($('#buy_quantity').val(), 10) || 0;
        const getQty = parseInt($('#get_quantity').val(), 10) || 0;
        const productSelect = $('.product-select');
        
        const maxSelection = buyQty + getQty;
        
        let currentSelection = productSelect.val();

        // Re-initialize Select2 with the new maximum selection length
        productSelect.select2('destroy');
        initializeSelect2(productSelect, maxSelection);
        
        // If current selection exceeds the new max, trim it
        if (currentSelection && currentSelection.length > maxSelection) {
            currentSelection = currentSelection.slice(0, maxSelection);
        }
        
        // Set the potentially trimmed selection back
        productSelect.val(currentSelection).trigger('change');
    });
});
</script>
@endsection
