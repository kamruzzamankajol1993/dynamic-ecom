<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModalLabel">Add New Extra Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('extracategory.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Name" required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm w-md mt-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>