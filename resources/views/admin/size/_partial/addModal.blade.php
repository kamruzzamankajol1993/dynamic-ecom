<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModalLabel">Add New Size</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addForm" method="post" action="{{ route('size.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark">Size Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark">Size Code (e.g., S, M, L)</label>
                        <input type="text" name="code" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark">Size Type (e.g., INT, EU, BD)</label>
                        <input type="text" name="size_type" class="form-control">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm w-md mt-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
