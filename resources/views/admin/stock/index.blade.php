@extends('admin.master.master')

@section('title', 'Stock Management')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .variant-card {
        transition: all 0.3s ease;
    }
    .history-table td {
        font-size: 0.85rem;
    }
    .select2-container .select2-selection--single {
        height: 38px;
    }
</style>
@endsection

@section('body')
<main class="main-content">
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Stock Management</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select Product</h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="product-select">Search and select a product to manage its stock:</label>
                <select id="product-select" class="form-control" style="width: 100%;">
                    <option></option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div id="variants-container" class="mt-4" style="display: none;">
        <h3 id="selected-product-name" class="h4 mb-3 text-gray-800"></h3>
        <div id="variants-grid" class="row">
            {{-- Product variants will be loaded here via AJAX --}}
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="historyModalLabel">Stock Update History</h5>
        {{-- This button has been corrected to use data-bs-dismiss --}}
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>History for: <strong id="history-item-name"></strong></p>
        <div class="table-responsive">
            <table class="table table-bordered history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Change</th>
                        <th>New Qty</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody id="history-table-body">
                    {{-- History rows will be loaded here --}}
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>
</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#product-select').select2({
        placeholder: "Select a product",
        allowClear: true
    });

    let selectedProductData = null;
    const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));

    // Fetch variants when a product is selected
    $('#product-select').on('change', function() {
        const productId = $(this).val();
        if (!productId) {
            $('#variants-container').hide();
            return;
        }

        let urlTemplate = "{{ route('stock.variants.get', ['product' => ':id']) }}";
        let variantsUrl = urlTemplate.replace(':id', productId);

        $.ajax({
            url: variantsUrl,
            type: 'GET',
            success: function(product) {
                selectedProductData = product;
                renderVariants(product);
                $('#variants-container').show();
            },
            error: function() {
                Swal.fire('Error!', 'Failed to fetch product variants.', 'error');
            }
        });
    });

    // Render the variant cards
    function renderVariants(product) {
        $('#selected-product-name').text(`Stock for: ${product.name}`);
        const grid = $('#variants-grid').empty();

        if (product.variants.length === 0) {
            grid.html('<p class="text-muted col-12">This product has no variants (colors/sizes) defined.</p>');
            return;
        }

        product.variants.forEach(variant => {
            let sizesHtml = '<ul class="list-group list-group-flush">';
            if (variant.detailed_sizes && variant.detailed_sizes.length > 0) {
                variant.detailed_sizes.forEach(size => {
                    sizesHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${size.name}</strong>
                                <a href="#" class="ml-2 view-history-btn" data-variant-id="${variant.id}" data-size-id="${size.id}" data-name="${variant.color.name} - ${size.name}">
                                    <i class="fas fa-history fa-sm"></i>
                                </a>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control form-control-sm" value="${size.quantity}" style="width: 70px;">
                                <button class="btn btn-primary btn-sm ml-2 update-stock-btn" data-variant-id="${variant.id}" data-size-id="${size.id}">Update</button>
                            </div>
                        </li>`;
                });
            } else {
                sizesHtml += '<li class="list-group-item text-muted">No sizes available for this color.</li>';
            }
            sizesHtml += '</ul>';

            const cardHtml = `
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm variant-card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">${variant.color.name}</h6>
                        </div>
                        ${sizesHtml}
                    </div>
                </div>`;
            grid.append(cardHtml);
        });
    }

    // Handle stock update
    $(document).on('click', '.update-stock-btn', function() {
        const btn = $(this);
        const variantId = btn.data('variant-id');
        const sizeId = btn.data('size-id');
        const newQuantity = btn.siblings('input').val();

        Swal.fire({
            title: 'Add a note for this update (optional)',
            input: 'text',
            inputPlaceholder: 'e.g., Initial stock count, weekly adjustment...',
            showCancelButton: true,
            confirmButtonText: 'Confirm Update',
            showLoaderOnConfirm: true,
            preConfirm: (notes) => {
                return $.ajax({
                    url: "{{ route('stock.update') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        variant_id: variantId,
                        size_id: sizeId,
                        quantity: newQuantity,
                        notes: notes
                    }
                }).catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error.responseJSON.message || 'Server Error'}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Success!', 'Stock has been updated successfully.', 'success');
            }
        });
    });
    
    // Handle view history click
    $(document).on('click', '.view-history-btn', function(e) {
        e.preventDefault();
        const variantId = $(this).data('variant-id');
        const sizeId = $(this).data('size-id');
        const name = $(this).data('name');
        
        $('#history-item-name').text(name);
        const historyBody = $('#history-table-body').empty().html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
        
        let urlTemplate = "{{ route('stock.history.get', ['variant' => ':variantId', 'sizeId' => ':sizeId']) }}";
        let historyUrl = urlTemplate.replace(':variantId', variantId).replace(':sizeId', sizeId);

        $.ajax({
            url: historyUrl,
            type: 'GET',
            success: function(history) {
                historyBody.empty();
                if (history.length === 0) {
                    historyBody.html('<tr><td colspan="6" class="text-center">No history found for this item.</td></tr>');
                    return;
                }
                history.forEach(entry => {
                    const change = entry.quantity_change;
                    const changeHtml = change >= 0 
                        ? `<span class="text-success font-weight-bold">+${change}</span>` 
                        : `<span class="text-danger font-weight-bold">${change}</span>`;

                    const row = `
                        <tr>
                            <td>${new Date(entry.created_at).toLocaleString()}</td>
                            <td>${entry.user ? entry.user.name : 'N/A'}</td>
                            <td>${entry.type.replace('_', ' ').toUpperCase()}</td>
                            <td>${changeHtml}</td>
                            <td>${entry.new_quantity}</td>
                            <td>${entry.notes || ''}</td>
                        </tr>
                    `;
                    historyBody.append(row);
                });
            },
            error: function() {
                historyBody.html('<tr><td colspan="6" class="text-center text-danger">Failed to load history.</td></tr>');
            }
        });
        
        historyModal.show();
    });
});
</script>
@endsection