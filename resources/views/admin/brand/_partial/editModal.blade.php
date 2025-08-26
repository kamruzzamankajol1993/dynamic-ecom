<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editBrandForm" class="modal-content">
            <input type="hidden" id="editBrandId">
            <div class="modal-header">
                <h5 class="modal-title">Edit Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editName" class="form-label">Brand Name</label>
                    <input type="text" id="editName" name="name" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="editLogo" class="form-label">Logo</label>
                    <input type="file" id="editLogo" name="logo" class="form-control">
                    <img id="logoPreview" src="" alt="Logo Preview" class="img-thumbnail mt-2" style="max-width: 100px; display: none;">
                </div>
                <div class="mb-3">
                    <label for="editStatus" class="form-label">Status</label>
                    <select id="editStatus" name="status" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
