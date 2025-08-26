<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModalLabel">Add New Size Chart</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addForm" method="post" action="{{ route('size-chart.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dark">Chart Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <hr>
                    <h5>Size Entries</h5>
                    <div id="add-entry-container">
                        <!-- Dynamic rows will be added here -->
                    </div>

                    <button type="button" class="btn btn-success btn-sm mt-2" id="add-entry-btn">
                        <i class="fa fa-plus"></i> Add Row
                    </button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
