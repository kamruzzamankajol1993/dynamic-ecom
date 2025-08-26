<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Expense</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="addForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Category*</label><select name="expense_category_id" class="form-select" required>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
                    <div class="mb-3"><label class="form-label">Amount*</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
                    <div class="mb-3"><label class="form-label">Date*</label><input type="text" id="add_expense_date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required autocomplete="off"></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>
