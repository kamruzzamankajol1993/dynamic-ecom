@extends('admin.master.master')
@section('title', 'Create Product')
@section('css')
    <style>
    .custom-select-container { position: relative; }
    .custom-select-display { display: block; width: 100%; padding: 0.375rem 0.75rem; font-size: 1rem; font-weight: 400; line-height: 1.5; color: #212529; background-color: #fff; border: 1px solid #ced4da; border-radius: 0.25rem; cursor: pointer; position: relative; }
    .custom-select-display::after { content: ''; position: absolute; top: 50%; right: 15px; width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #333; transform: translateY(-50%); }
    .custom-select-options { display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ced4da; border-top: 0; z-index: 1051; max-height: 200px; overflow-y: auto; }
    .custom-select-search-input { width: 100%; padding: 8px; border: none; border-bottom: 1px solid #ddd; }
    .custom-select-option { padding: 10px; cursor: pointer; }
    .custom-select-option:hover { background-color: #f0f0f0; }
    .custom-select-option.is-hidden { display: none; }
</style>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Create New Product</h2>
        </div>
        <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    {{-- Main Product Fields --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Main Information</h5>
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                             <div class="mb-3">
                                <label class="form-label">Product Code</label>
                                <input type="text" name="product_code" id="product_code" class="form-control" value="{{ old('product_code') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="summernote" class="form-control" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Size Chart Section --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Size Chart</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Select Default Size Chart (Optional)</label>
                                <select name="size_chart_id" id="sizeChartSelect" class="form-select">
                                    <option value="">None</option>
                                    @foreach($size_charts as $chart)
                                    <option value="{{ $chart->id }}">{{ $chart->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="size-chart-entries-container">
                                <!-- Entries will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>

                    {{-- Product Variations --}}
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Product Variations (Color & Size)</h5>
                            {{-- <button type="button" id="add-variant-btn" class="btn btn-sm btn-success">Add Color Variation</button> --}}
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Add color-wise variations. You can specify different images, prices, and stock quantities for each color and size.</p>
                            <div id="variant-container">
                                <!-- Dynamic variant sections will be added here -->
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                             <h5 class="mb-0"></h5>
                            <button type="button" id="add-variant-btn" class="btn btn-sm btn-success">Add Color Variation</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    {{-- Pricing & Organization --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Pricing & Organization</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Purchase Price</label>
                                    <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price') }}" required step="0.01">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Base Price</label>
                                    <input type="number" name="base_price" class="form-control" value="{{ old('base_price') }}" required step="0.01">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Discount Price</label>
                                <input type="number" name="discount_price" class="form-control" value="{{ old('discount_price') }}" step="0.01">
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" id="categoryId" class="form-select select2-like" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subcategory</label>
                                <select name="subcategory_id" id="subcategoryId" class="form-select select2-like"></select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sub-Subcategory</label>
                                <select name="sub_subcategory_id" id="subSubcategoryId" class="form-select select2-like"></select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-select ">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{$brand->id == 1 ? 'selected' : ''}}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Matrial</label>
                                <select name="fabric_id" class="form-select ">
                                     <option value="">Select Matrial</option>
                                    @foreach($fabrics as $fabric)
                                    <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <select name="unit_id" class="form-select " required>
                                     @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                      {{-- Animation Category --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Animation Category</h5>
                            @foreach($animation_categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="animation_category_ids[]" value="{{ $category->id }}" id="anim_cat_{{ $category->id }}">
                                <label class="form-check-label" for="anim_cat_{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Other Category --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Other Category</h5>
                            @foreach(['New', 'Trending', 'Discount'] as $item)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="other_categories[]" value="{{ strtolower($item) }}" id="other_cat_{{ strtolower($item) }}">
                                <label class="form-check-label" for="other_cat_{{ strtolower($item) }}">
                                    {{ $item }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                             <h5 class="card-title mb-4">Media</h5>
                             <div class="mb-3">
                                <label class="form-label">Thumbnail Images</label>
                                <input type="file" name="thumbnail_image[]" class="form-control" id="thumbnailInput" multiple>
                                <div id="thumbnail-preview-container" class="mt-2 d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Product</button>
        </form>
    </div>
</main>
@endsection
@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {

     // --- Custom Searchable Select Plugin ---
    function createSearchableSelect(originalSelect) {
        const $originalSelect = $(originalSelect);
        if ($originalSelect.next('.custom-select-container').length) return;
        $originalSelect.hide();
        const $container = $('<div class="custom-select-container" />');
        const $display = $('<div class="custom-select-display" />').text($originalSelect.find('option:selected').text() || 'Select an option');
        const $optionsContainer = $('<div class="custom-select-options" />');
        const $searchInput = $('<input type="text" class="custom-select-search-input" placeholder="Search...">');
        $optionsContainer.append($searchInput);
        $originalSelect.find('option').each(function() {
            const $option = $(this);
            const $customOption = $('<div class="custom-select-option" />').data('value', $option.val()).text($option.text());
            if ($option.val() === '') $customOption.addClass('is-hidden');
            $optionsContainer.append($customOption);
        });
        $originalSelect.after($container.append($display).append($optionsContainer));
        $display.on('click', e => { e.stopPropagation(); $('.custom-select-options').not($optionsContainer).hide(); $optionsContainer.toggle(); });
        $searchInput.on('click', e => e.stopPropagation());
        $searchInput.on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $optionsContainer.find('.custom-select-option').each(function() {
                $(this).toggleClass('is-hidden', $(this).text().toLowerCase().indexOf(searchTerm) === -1);
            });
        });
        $optionsContainer.on('click', '.custom-select-option', function() {
            $originalSelect.val($(this).data('value')).trigger('change');
            $display.text($(this).text());
            $optionsContainer.hide();
        });
    }
    $('.select2-like').each(function() { createSearchableSelect(this); });
    $(document).on('click', () => $('.custom-select-options').hide());

    var routes = {
        getSubcategories: id => `{{ route('get_subcategories', ':id') }}`.replace(':id', id),
        getSubSubcategories: id => `{{ url('get-sub-subcategories') }}/${id}`, // Assuming you have this route
        getSizeChartEntries: id => `{{ route('get.size-chart.entries', ':id') }}`.replace(':id', id)
    };

  // --- Auto-generate Product Code ---
    $('input[name="name"]').on('keyup change', function() {
        const productName = $(this).val();
        const productCodeInput = $('#product_code'); // Make sure your product code input has id="product_code"
        
        if (productName.length > 2) {
            const prefix = productName.substring(0, 4).toUpperCase().replace(/\s+/g, '');
            const timestamp = Date.now().toString().slice(-5);
            productCodeInput.val(`${prefix}-${timestamp}`);
        }
    });

    // --- Dependent Category Dropdowns ---
    $('#categoryId').on('change', function() {
        let categoryId = $(this).val();
        $('#subcategoryId, #subSubcategoryId').empty().append('<option value=""></option>').next('.custom-select-container').remove();
        createSearchableSelect($('#subcategoryId'));
        createSearchableSelect($('#subSubcategoryId'));
        if (categoryId) {
            $.get(routes.getSubcategories(categoryId), function(data) {
                let options = '<option value="">Select Subcategory</option>';
                data.forEach(sub => options += `<option value="${sub.id}">${sub.name}</option>`);
                $('#subcategoryId').html(options).next('.custom-select-container').remove();
                createSearchableSelect($('#subcategoryId'));
            });
        }
    });
    
    $('#subcategoryId').on('change', function() {
        let subcategoryId = $(this).val();
        $('#subSubcategoryId').empty().append('<option value=""></option>').next('.custom-select-container').remove();
        createSearchableSelect($('#subSubcategoryId'));
        if (subcategoryId) {
            $.get(routes.getSubSubcategories(subcategoryId), function(data) {
                let options = '<option value="">Select Sub-Subcategory</option>';
                data.forEach(sub => options += `<option value="${sub.id}">${sub.name}</option>`);
                $('#subSubcategoryId').html(options).next('.custom-select-container').remove();
                createSearchableSelect($('#subSubcategoryId'));
            });
        }
    });

    // --- Size Chart Logic ---
    $('#sizeChartSelect').on('change', function() {
        let chartId = $(this).val();
        const container = $('#size-chart-entries-container');
        container.empty();
        if (chartId) {
            $.get(routes.getSizeChartEntries(chartId), function(data) {
                if(data.entries) {
                    let table = `<h6 class="mt-3">Edit Entries for this product:</h6><table class="table table-bordered table-sm"><thead><tr><th>Size</th><th>Length</th><th>Width</th><th>Shoulder</th><th>Sleeve</th></tr></thead><tbody>`;
                    data.entries.forEach((entry, index) => {
                        table += `<tr>
                            <td><input type="text" name="chart_entries[${index}][size]" class="form-control form-control-sm" value="${entry.size}"></td>
                            <td><input type="text" name="chart_entries[${index}][length]" class="form-control form-control-sm" value="${entry.length || ''}"></td>
                            <td><input type="text" name="chart_entries[${index}][width]" class="form-control form-control-sm" value="${entry.width || ''}"></td>
                            <td><input type="text" name="chart_entries[${index}][shoulder]" class="form-control form-control-sm" value="${entry.shoulder || ''}"></td>
                            <td><input type="text" name="chart_entries[${index}][sleeve]" class="form-control form-control-sm" value="${entry.sleeve || ''}"></td>
                        </tr>`;
                    });
                    table += `</tbody></table>`;
                    container.html(table);
                }
            });
        }
    });

    // --- Variation Logic ---
    let variantIndex = 0;
    const colors = @json($colors);
    const sizes = @json($sizes);
    $('#add-variant-btn').on('click', function() {
        const container = $('#variant-container');
        let colorOptions = colors.map(color => `<option value="${color.id}">${color.name}</option>`).join('');
        let sizeFields = sizes.map((size, sizeIndex) => `
            <div class="row align-items-center mb-2">
                <div class="col-5"><label class="form-label-sm">${size.code}</label></div>
                <div class="col-7">
                    <input type="hidden" name="variants[${variantIndex}][sizes][${sizeIndex}][size_id]" value="${size.id}">
                    <input type="number" name="variants[${variantIndex}][sizes][${sizeIndex}][quantity]" class="form-control form-control-sm" placeholder="Quantity">
                </div>
            </div>
        `).join('');
        const variantHtml = `
            <div class="variant-section border rounded p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">New Color Variation</h6>
                    <button type="button" class="btn-close remove-variant-btn"></button>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Color</label>
                        <select name="variants[${variantIndex}][color_id]" class="form-select variant-color-select"><option>select color</option>${colorOptions}</select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Variant Image</label>
                        <input type="file" name="variants[${variantIndex}][image]" class="form-control variant-image-input">
                        <img class="variant-image-preview img-thumbnail mt-2" style="display: none; height: 80px; width: 80px; object-fit: cover;">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Variant SKU</label>
                        <input type="text" name="variants[${variantIndex}][variant_sku]" class="form-control variant-sku-input" placeholder="Auto-generated SKU">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Additional Price (Optional)</label>
                        <input type="number" name="variants[${variantIndex}][additional_price]" class="form-control" step="0.01" placeholder="e.g., 5.00">
                    </div>
                </div>
                <h6>Sizes & Quantity</h6>
                <div class="p-2 border rounded bg-light">${sizeFields}</div>
            </div>
        `;
        container.append(variantHtml);
        variantIndex++;
    });
    $('#variant-container').on('click', '.remove-variant-btn', function() {
        $(this).closest('.variant-section').remove();
    });

    // Delegated event listener for variant image preview
    $('#variant-container').on('change', '.variant-image-input', function(event) {
        const preview = $(this).siblings('.variant-image-preview')[0];
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Delegated event listener for auto-generating SKU
    $('#variant-container').on('change', '.variant-color-select', function() {
        const selectedColorText = $(this).find('option:selected').text().toUpperCase().replace(/\s+/g, ''); // Remove spaces
        const productCode = $('#product_code').val().toUpperCase();
        const variantSection = $(this).closest('.variant-section');
        const skuInput = variantSection.find('.variant-sku-input');

        if (productCode && selectedColorText) {
            skuInput.val(`${productCode}-${selectedColorText}`);
        }
    });

    // --- Multiple Thumbnail Image Preview Logic ---
    const thumbnailInput = document.getElementById('thumbnailInput');
    const previewContainer = document.getElementById('thumbnail-preview-container');
    const dataTransfer = new DataTransfer();

    function renderPreviews() {
        previewContainer.innerHTML = '';
        const files = Array.from(thumbnailInput.files);

        files.forEach((file, index) => {
            if (!file.type.startsWith('image/')) { return; }
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('thumbnail-wrapper');
                wrapper.style.position = 'relative';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('img-thumbnail');
                img.style.height = '80px';
                img.style.width = '80px';
                img.style.objectFit = 'cover';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '&times;';
                removeBtn.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-preview-btn');
                removeBtn.dataset.index = index;
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '0';
                removeBtn.style.right = '0';
                removeBtn.style.padding = '0px 5px';
                removeBtn.style.lineHeight = '1';

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                previewContainer.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    thumbnailInput.addEventListener('change', function() {
        for (const file of this.files) {
            dataTransfer.items.add(file);
        }
        this.files = dataTransfer.files;
        renderPreviews();
    });

    previewContainer.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-preview-btn')) {
            const indexToRemove = parseInt(e.target.dataset.index, 10);
            const newFiles = new DataTransfer();
            const currentFiles = Array.from(thumbnailInput.files);
            
            currentFiles.forEach((file, index) => {
                if (index !== indexToRemove) {
                    newFiles.items.add(file);
                }
            });

            dataTransfer.items.clear();
            Array.from(newFiles.files).forEach(file => dataTransfer.items.add(file));
            thumbnailInput.files = newFiles.files;

            renderPreviews();
        }
    });
});
</script>
 <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview']],
                ['help', ['help']]
            ]
        });
    });
    </script>
@endsection
