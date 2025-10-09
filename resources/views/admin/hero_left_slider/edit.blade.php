@extends('admin.master.master')
@section('title', 'Edit Hero Slider')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { height: calc(2.25rem + 2px); padding: .375rem .75rem; border: 1px solid #ced4da; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 2.25rem; }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4"><h2>Edit Hero Slider (Left Side)</h2></div>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('hero-left-slider.update', $heroLeftSlider->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3"><label for="title" class="form-label">Title*</label><input type="text" name="title" id="title" class="form-control" value="{{ $heroLeftSlider->title }}" required></div>
                    <div class="mb-3"><label for="subtitle" class="form-label">Subtitle</label><input type="text" name="subtitle" id="subtitle" class="form-control" value="{{ $heroLeftSlider->subtitle }}"></div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" accept="image/webp" id="image" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Recommended dimensions: 680px width and 695px height. (Only upload to change)</small>
                        <div class="mt-2">
                            <p class="mb-1">Current Image:</p>
                            <img src="{{ asset('public/'.$heroLeftSlider->image) }}" alt="Current Image" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                        <div class="mt-2" id="image-preview-div" style="display: none;">
                            <p class="mb-1">New Image Preview:</p>
                            <img id="image-preview" src="#" alt="New Image Preview" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                    
                    @php
                        $isProduct = $heroLeftSlider->linkable_type === App\Models\Product::class;
                        $isCategory = $heroLeftSlider->linkable_type === App\Models\Category::class;
                        $isBundleOffer = $heroLeftSlider->linkable_type === App\Models\BundleOffer::class;
                    @endphp

                    <div class="mb-3">
                        <label class="form-label d-block">Link Type*</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="link_type" id="type_category" value="category" required @if($isCategory) checked @endif>
                            <label class="form-check-label" for="type_category">Category</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="link_type" id="type_product" value="product" @if($isProduct) checked @endif>
                            <label class="form-check-label" for="type_product">Product</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="link_type" id="type_bundle" value="bundle_offer" @if($isBundleOffer) checked @endif>
                            <label class="form-check-label" for="type_bundle">Bundle Offer</label>
                        </div>
                    </div>

                    <div id="category-select-div" class="mb-3" style="{{ $isCategory ? '' : 'display:none;' }}">
                        <label for="category_id" class="form-label d-block">Select Category</label>
                        <select name="category_id" id="category_id" class="form-control select2" style="width: 100%">
                            <option value="">Select a category...</option>
                            @foreach($categories as $category)<option value="{{ $category->id }}" @if($isCategory && $heroLeftSlider->linkable_id == $category->id) selected @endif>{{ $category->name }}</option>@endforeach
                        </select>
                    </div>

                    <div id="product-select-div" class="mb-3" style="{{ $isProduct ? '' : 'display:none;' }}">
                        <label for="product_id" class="form-label d-block">Select Product</label>
                        <select name="product_id" id="product_id" class="form-control select2" style="width: 100%">
                            <option value="">Select a product...</option>
                            @foreach($products as $product)<option value="{{ $product->id }}" @if($isProduct && $heroLeftSlider->linkable_id == $product->id) selected @endif>{{ $product->name }}</option>@endforeach
                        </select>
                    </div>

                    <div id="bundle-select-div" class="mb-3" style="{{ $isBundleOffer ? '' : 'display:none;' }}">
                        <label for="bundle_offer_id" class="form-label d-block">Select Bundle Offer</label>
                        <select name="bundle_offer_id" id="bundle_offer_id" class="form-control select2" style="width: 100%">
                            <option value="">Select a bundle offer...</option>
                            @foreach($bundleOffers as $bundle)
                                <option value="{{ $bundle->id }}" @if($isBundleOffer && $heroLeftSlider->linkable_id == $bundle->id) selected @endif>{{ $bundle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3"><label for="status" class="form-label">Status</label><select name="status" id="status" class="form-control"><option value="1" {{ $heroLeftSlider->status ? 'selected' : '' }}>Active</option><option value="0" {{ !$heroLeftSlider->status ? 'selected' : '' }}>Inactive</option></select></div>
                    <button type="submit" class="btn btn-dark"><i class="fas fa-save"></i>  Update Slider</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();

    $('input[name="link_type"]').on('change', function() {
        $('#category-select-div').hide();
        $('#product-select-div').hide();
        $('#bundle-select-div').hide();

        if ($(this).val() === 'category') {
            $('#category-select-div').show();
        } else if ($(this).val() === 'product') {
            $('#product-select-div').show();
        } else if ($(this).val() === 'bundle_offer') {
            $('#bundle-select-div').show();
        }
    });

    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
            }
            $('#image-preview-div').show();
            reader.readAsDataURL(file);
        } else {
            $('#image-preview-div').hide();
        }
    });
});
</script>
@endsection