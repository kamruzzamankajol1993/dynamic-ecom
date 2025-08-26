<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModalLabel">Add New Sub-Subcategory</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addForm" method="post" action="{{ route('sub-subcategory.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark">Category</label>
                        <select name="category_id" id="addCategoryId" class="form-control searchable-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark">Subcategory</label>
                        <select name="subcategory_id" id="addSubcategoryId" class="form-control" required>
                            <option value="">Select Category First</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark">Sub-Subcategory Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm w-md mt-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
