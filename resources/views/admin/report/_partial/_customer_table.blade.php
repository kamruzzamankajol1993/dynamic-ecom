<div class="table-responsive position-relative" style="min-height: 400px;">
    <div class="loading-spinner" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Sl</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th class="text-center">Total Orders</th>
                <th class="text-end">Total Spent</th>
            </tr>
        </thead>
        <tbody id="table-body"></tbody>
    </table>
</div>
<div class="card-footer bg-white d-flex justify-content-between align-items-center">
    <div class="text-muted" id="pagination-info"></div>
    <nav>
        <ul class="pagination mb-0" id="pagination-container"></ul>
    </nav>
</div>
