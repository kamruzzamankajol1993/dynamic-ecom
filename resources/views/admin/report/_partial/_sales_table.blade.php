<div class="table-responsive position-relative" style="min-height: 300px;">
    <div class="loading-spinner" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice ID</th>
                <th>Customer</th>
                <th class="text-end">Subtotal</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Shipping</th>
                <th class="text-end">Total</th>
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
