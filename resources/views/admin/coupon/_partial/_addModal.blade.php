<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Coupon Code*</label><input type="text" name="code" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Discount Type*</label><select name="type" class="form-select"><option value="fixed">Fixed Amount</option><option value="percent">Percentage</option></select></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Value*</label><input type="number" name="value" class="form-control" step="0.01" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Minimum Purchase Amount</label><input type="number" name="min_amount" class="form-control" step="0.01"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Usage Limit</label><input type="number" name="usage_limit" class="form-control"></div>
                        {{-- Changed input type and added ID --}}
                        <div class="col-md-6 mb-3"><label class="form-label">Expires At</label><input type="text" id="add_expires_at" name="expires_at" class="form-control" autocomplete="off"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">User Type</label><select name="user_type" class="form-select"><option value="all">All Users</option><option value="normal">Normal</option><option value="platinum">Platinum</option></select></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select></div>
                        <div class="col-md-12 mb-3"><label class="form-label">Applicable Products (Optional)</label><select name="product_ids[]" class="form-control select2-add" multiple style="width: 100%;">@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach</select></div>
                        <div class="col-md-12 mb-3"><label class="form-label">Applicable Categories (Optional)</label><select name="category_ids[]" class="form-control select2-add" multiple style="width: 100%;">@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>
