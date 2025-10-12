 <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="addCustomerForm">
              <div class="mb-3"><label for="customer-name" class="col-form-label">Customer Name:</label><input type="text" class="form-control" id="customer-name-modal" required></div>
             {{-- --- UPDATED LINE --- --}}
              <div class="mb-3"><label for="customer-phone" class="col-form-label">Mobile Number:</label><input type="number" class="form-control" id="customer-phone-modal" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '');"></div>
              {{-- --- END UPDATE --- --}}
              <div class="mb-3"><label for="customer-address" class="col-form-label">Address:</label><textarea class="form-control" id="customer-address-modal"></textarea></div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="saveCustomerBtn" class="btn btn-primary" style="background-color: var(--primary-color); border-color: var(--primary-color);">Save Customer</button>
          </div>
        </div>
      </div>
    </div>