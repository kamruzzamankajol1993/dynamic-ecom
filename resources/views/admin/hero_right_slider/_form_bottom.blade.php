@php
    $isCategory = $section->linkable_type === App\Models\Category::class;
    $isExtraCategory = $section->linkable_type === App\Models\ExtraCategory::class;
@endphp

<div class="row">
    <div class="col-md-12 mb-3"><label class="form-label">Title*</label><input type="text" name="{{ $prefix }}[title]" class="form-control" value="{{ $section->title }}" required></div>
    <div class="col-md-12 mb-3"><label class="form-label">Subtitle</label><input type="text" name="{{ $prefix }}[subtitle]" class="form-control" value="{{ $section->subtitle }}"></div>
    <div class="col-md-12 mb-3"><label class="form-label">Image</label><input type="file" name="{{ $prefix }}[image]" class="form-control"><small class="text-muted">Upload to change. Required: 330x300px</small> @if($section->image)<img src="{{ asset($section->image) }}" height="50" class="mt-2 d-block">@endif</div>
    
    <div class="col-md-12 mb-3">
        <label class="form-label d-block">Link Type*</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="{{ $prefix }}[link_type]" id="{{ $prefix }}_type_category" value="category" required @checked($isCategory)>
            <label class="form-check-label" for="{{ $prefix }}_type_category">Category</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="{{ $prefix }}[link_type]" id="{{ $prefix }}_type_extracategory" value="extracategory" @checked($isExtraCategory)>
            <label class="form-check-label" for="{{ $prefix }}_type_extracategory">Extra Category</label>
        </div>
    </div>

    <div id="{{ $prefix }}_category-select-div" class="mb-3" style="{{ $isCategory ? '' : 'display:none;' }}">
        <label class="form-label">Select Category</label>
        <select class="form-control select2 category-select" data-prefix="{{ $prefix }}">
            <option value="">Select...</option>
            @foreach($categories as $item)<option value="{{ $item->id }}" @if($isCategory && $section->linkable_id == $item->id) selected @endif>{{ $item->name }}</option>@endforeach
        </select>
    </div>

    <div id="{{ $prefix }}_extracategory-select-div" class="mb-3" style="{{ $isExtraCategory ? '' : 'display:none;' }}">
        <label class="form-label">Select Extra Category</label>
        <select class="form-control select2 extracategory-select" data-prefix="{{ $prefix }}">
            <option value="">Select...</option>
            @foreach($extraCategories as $item)<option value="{{ $item->id }}" @if($isExtraCategory && $section->linkable_id == $item->id) selected @endif>{{ $item->name }}</option>@endforeach
        </select>
    </div>
    <input type="hidden" name="{{ $prefix }}[link_id]" value="{{ $section->linkable_id }}">
    
    <div class="col-md-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="{{ $prefix }}[status]" value="1" @checked($section->status)><label class="form-check-label">Active</label></div></div>
</div>

<script>
// This ensures the script for each partial doesn't conflict
document.addEventListener('DOMContentLoaded', function () {
    const prefix = '{{ $prefix }}';
    const radioSelector = `input[name="${prefix}[link_type]"]`;
    const categoryDiv = `#${prefix}_category-select-div`;
    const extraCategoryDiv = `#${prefix}_extracategory-select-div`;
    const hiddenInput = `input[name="${prefix}[link_id]"]`;

    $(radioSelector).on('change', function() {
        if ($(this).val() === 'category') {
            $(categoryDiv).show();
            $(extraCategoryDiv).hide();
            $(hiddenInput).val($(categoryDiv + ' select').val());
        } else {
            $(extraCategoryDiv).show();
            $(categoryDiv).hide();
            $(hiddenInput).val($(extraCategoryDiv + ' select').val());
        }
    });

    $(document).on('change', `${categoryDiv} select, ${extraCategoryDiv} select`, function() {
        if ($(this).closest('div').is(':visible')) {
            $(hiddenInput).val($(this).val());
        }
    });
});
</script>