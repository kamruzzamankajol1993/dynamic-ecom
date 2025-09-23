@foreach($categories as $category)
    <li class="category-tree-item">
        <div class="form-check">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="category_ids[]" 
                value="{{ $category->id }}" 
                id="cat-{{ $category->id }}"
                @if(in_array($category->id, $assignedCategoryIds ?? [])) checked @endif
            >
            <label class="form-check-label" for="cat-{{ $category->id }}">
                @if($category->children->isNotEmpty())
                    <i class="toggle-icon fas fa-plus-square me-1"></i>
                @else
                    <span style="display:inline-block; width:16px;"></span>
                @endif
                {{ $category->name }}
            </label>
        </div>

        @if($category->children->isNotEmpty())
            <ul class="category-tree-child list-unstyled" style="display: none;">
                @include('admin.product._partials.category-tree-checkbox', [
                    'categories' => $category->children,
                    'assignedCategoryIds' => $assignedCategoryIds
                ])
            </ul>
        @endif
    </li>
@endforeach