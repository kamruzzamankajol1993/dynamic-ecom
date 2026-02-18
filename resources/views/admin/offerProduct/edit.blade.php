@extends('admin.master.master')
@section('title', 'Edit Product Deal')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Edit Product Deal</h2>
        <form action="{{ route('offer-product.update', $offerProduct->id) }}" method="POST" enctype="multipart/form-data">
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
                    <div class="mb-3">
    <label class="form-label">Deal Image</label>
    <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
    <div class="mt-2">
        @if($offerProduct->image)
            <img id="imagePreview" src="{{ asset($offerProduct->image) }}" alt="Preview" style="width: 150px; height: auto;" class="img-thumbnail">
        @else
            <img id="imagePreview" src="#" alt="Preview" style="display: none; width: 150px; height: auto;" class="img-thumbnail">
        @endif
    </div>
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
                     <hr>
                    <p class="text-muted">You can add products by selecting entire categories, choosing individual products, or both.</p>

                    {{-- ADDED: Category selection field --}}
                    <div class="mb-3">
                        <label class="form-label">Select Categories</label>
                        <select name="category_id[]" class="form-control category-select" multiple>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ in_array($id, $selectedCategoryIds) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
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
                    <div class="row mb-3 mt-3">
    <div class="col-md-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_custom" id="is_custom" value="1" {{ $offerProduct->is_custom ? 'checked' : '' }}>
            <label class="form-check-label" for="is_custom">Allow Custom Name & Number (Jersey Customization)</label>
        </div>
        <small class="text-muted d-block">Update whether this deal allows custom name and number.</small>
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
    // Image preview logic
    $('#imageInput').change(function() {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(event) {
                $('#imagePreview').attr('src', event.target.result).show();
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
<script>
$(document).ready(function() {
    // Initialize Select2 without selection limit
     const productSelect = $('.product-select').select2({
        placeholder: 'Search and select products...',
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

     // Initialize Category Select2
    $('.category-select').select2({
        placeholder: 'Search and select categories...',
    });

    // --- NEW SCRIPT TO AUTO-POPULATE PRODUCTS ---
    $('.category-select').on('change', function() {
        const categoryIds = $(this).val();
        
        if (categoryIds && categoryIds.length > 0) {
            $.ajax({
                url: "{{ route('ajax.products-by-categories') }}",
                type: 'GET',
                dataType: 'json',
                data: { category_ids: categoryIds },
                success: function(products) {
                    products.forEach(function(product) {
                        // Check if the option already exists
                        if (productSelect.find("option[value='" + product.id + "']").length) {
                            // If it exists, do nothing to avoid duplicates
                        } else {
                            // Create and append the new option
                            const newOption = new Option(product.text, product.id, true, true);
                            productSelect.append(newOption).trigger('change');
                        }
                    });
                },
                error: function() {
                    console.error('Failed to fetch products for the selected category.');
                }
            });
        }
    });
});
</script>
@endsection
