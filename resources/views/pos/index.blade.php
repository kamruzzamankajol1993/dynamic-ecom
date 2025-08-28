@extends('pos.master.master')

@section('title', 'POS - Sales')
@section('styles')
  
@endsection

@section('body')
   <div class="row g-2">
                <div class="col-lg-5">
                    <div class="card left-panel p-2">
                            <div class="cart-top-inputs">
                            <div class="position-relative">
                                <div class="input-group mb-2">
                                    <input type="text" id="customer-search" class="form-control" placeholder="Search or select customer...">
                                    <input type="hidden" id="selected-customer-id">
                                    <button class="btn btn-light border" data-bs-toggle="modal" data-bs-target="#addCustomerModal" type="button"><i class="fa-solid fa-plus text-success"></i></button>
                                </div>
                                <div id="customer-results" class="list-group position-absolute w-100" style="top: 100%; z-index: 1050; display: none; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Scan barcode or type the number then hit enter">
                                <button class="btn btn-light border" type="button"><i class="fa-solid fa-check text-primary"></i></button>
                            </div>
                        </div>

                        <div class="cart-table-wrapper">
                             <div class="cart-table-header d-flex justify-content-between align-items-center">
    <button id="clear-cart-btn" class="btn btn-sm btn-link text-danger"><i class="fa-solid fa-eraser me-1"></i> Clear Cart</button>
    <button id="delete-selected-btn" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can me-1"></i> Delete Selected</button>
</div>
                             <div class="cart-table-scroll">
                                 <table class="table table-hover cart-table">
                                     <thead>
                                         <tr>
                                             <th scope="col" class="text-center" style="width: 1%;"><input class="form-check-input" id="select-all-cart-items" type="checkbox"></th>
                                             <th scope="col">#</th>
                                             <th scope="col">Item</th>
                                             <th scope="col" class="text-center">Qty</th>
                                             <th scope="col" class="text-end">Price</th>
                                             <th scope="col"></th>
                                         </tr>
                                     </thead>
                                    <tbody id="cart-table-body">
                        {{-- Cart items will appear here --}}
                    </tbody>
                                 </table>
                             </div>
                        </div>
                        
                         <div class="cart-summary">
    <div class="d-flex justify-content-between">
        <p class="mb-1">Subtotal</p>
        <p id="cart-subtotal" class="mb-1 fw-bold">৳0.00</p>
    </div>
    
    <div class="d-flex justify-content-between align-items-center">
        <p class="mb-1">Discount Type</p>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="discountType" id="fixed" value="fixed" checked>
                <label class="form-check-label" for="fixed">Fixed</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="discountType" id="percentage" value="percentage">
                <label class="form-check-label" for="percentage">%</label>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="mb-1">Discount Amount</p>
        <input type="text" id="cart-discount-input" class="form-control form-control-sm" style="max-width: 120px;" value="0.00">
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="mb-1">Shipping Cost</p>
        <input type="text" id="cart-shipping-cost-input" class="form-control form-control-sm" style="max-width: 120px;" value="0.00">
    </div>
    
    <hr class="my-1">
    
    <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Total Payable</h6>
        <h6 id="cart-total-payable" class="mb-0 fw-bold grand-total">৳0.00</h6>
    </div>
    
    <hr class="my-1">
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="mb-1">Payment Type</p>
        <select id="payment-type-select" class="form-select form-select-sm" style="max-width: 140px;">
            <option value="Cash" selected>Cash</option>
            <option value="Card">Card</option>
            <option value="Mobile Banking">Mobile Banking</option>
        </select>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="mb-1">Total Pay</p>
        <input type="text" id="cart-total-pay-input" class="form-control form-control-sm" style="max-width: 140px;" value="0.00">
    </div>
    
    <div class="d-flex justify-content-between align-items-center">
        <p class="mb-1">Due</p>
        <p id="cart-due" class="mb-1 fw-bold text-danger">৳0.00</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <p class="mb-1">COD Amount</p>
        <input type="text" id="cart-cod-input" class="form-control form-control-sm" style="max-width: 120px;" value="0.00">
    </div>

     <div class="mb-2">
        <label for="order-notes" class="form-label mb-1 small">Order Notes (Optional)</label>
        <textarea id="order-notes" class="form-control form-control-sm" rows="2"></textarea>
    </div>
    
    <div class="d-flex gap-2 mt-2">
        <button class="btn btn-lock w-100"><i class="fa-solid fa-lock"></i></button>
        <button class="btn btn-cancel w-100">Cancel</button>
        <button class="btn btn-hold w-100">Hold</button>
        <button id="process-order-btn" class="btn btn-pay w-100">Pay</button>
    </div>
</div>
                    </div>
                </div>

                <div class="col-lg-7">
    <div class="card right-panel p-2 main-container">
        {{-- SEARCH AND FILTER BAR --}}
        <div class="row g-2 mb-2">
            <div class="col-sm-8">
                <input type="search" id="product-search-input" class="form-control" placeholder="Search product by name or sku...">
            </div>
            <div class="col-sm-4">
                <select class="form-select" id="product-category-select">
                    <option value="" selected>All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 text-end">
                        <button id="reset-filter-btn" class="btn btn-sm btn-danger" style="display: none;">
                            <i class="fa-solid fa-xmark me-1"></i> Reset
                        </button>
                    </div>
        </div>
        
        {{-- STATIC BUTTONS BAR (as per your request) --}}
        <div class="action-buttons-bar">
            <button class="action-btn" id="filter-by-animation-btn">animation category</button>
                <button id="bundle-offer-btn" class="action-btn" data-bs-toggle="modal" data-bs-target="#bundleOfferListModal">Bundle Offer</button>
        </div>

        {{-- DYNAMIC PRODUCT GRID --}}
        <div class="product-grid" id="product-grid-wrapper">
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2" id="product-grid-container">
                {{-- Products will be loaded here by AJAX --}}
            </div>
            <div id="loading-spinner" class="text-center p-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
   </div>

<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <img id="modal-product-image" src="" class="img-fluid rounded" alt="Product Image">
                    </div>
                    <div class="col-md-7">
                        <h4 id="modal-product-name"></h4>
                        <p class="fs-5 fw-bold text-primary" id="modal-product-price"></p>
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Color:</label>
                            <div id="modal-color-options" class="d-flex flex-wrap gap-2">
                                </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Size:</label>
                            <div id="modal-size-options" class="d-flex flex-wrap gap-2">
                                </div>
                        </div>
                        <hr>
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <label for="modal-quantity" class="form-label fw-bold">Quantity:</label>
                                <div class="input-group" style="max-width: 150px;">
                                    <button class="btn btn-outline-secondary" type="button" id="modal-quantity-minus">-</button>
                                    <input type="text" id="modal-quantity" class="form-control text-center" value="1" min="1">
                                    <button class="btn btn-outline-secondary" type="button" id="modal-quantity-plus">+</button>
                                </div>
                            </div>
                            <div class="align-self-end">
                                <button class="btn btn-primary btn-lg" id="modal-add-to-cart-btn"><i class="fa-solid fa-cart-plus me-2"></i> Add to Cart</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--bundle modal-->
<div class="modal fade" id="bundleOfferListModal" tabindex="-1" aria-labelledby="bundleOfferListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bundleOfferListModalLabel">Available Bundle Offers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="bundle-offer-grid" class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
                    {{-- Bundle offer cards will be loaded here by AJAX --}}
                </div>
                <div id="bundle-list-loading" class="text-center p-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="bundleOfferDetailModal" tabindex="-1" aria-labelledby="bundleOfferDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bundleOfferDetailModalLabel">Configure Your Bundle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-7">
                        <h4 id="bundle-detail-title" class="mb-3"></h4>
                        <div id="bundle-product-selection-area">
                            {{-- Product selection UIs will be dynamically inserted here --}}
                        </div>
                    </div>
                    <div class="col-lg-5 border-start">
                        <h4>Your Selections</h4>
                        <p>Please select a color and size for each product.</p>
                        <ul id="bundle-selection-summary" class="list-group mb-3">
                           {{-- Summary of selected variants will appear here --}}
                        </ul>
                        <div class="text-end">
                            <p class="fs-5">Total Price: <strong id="bundle-detail-price" class="text-primary"></strong></p>
                            <button class="btn btn-primary btn-lg" id="add-bundle-to-cart-btn" disabled>
                                <i class="fa-solid fa-cart-plus me-2"></i> Add Bundle to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">Order Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="invoice-content">
                {{-- Invoice HTML will be loaded here --}}
            </div>
            <div class="modal-footer">
                <a href="#" id="pos-print-btn" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-receipt me-1"></i> POS Print</a>
                <a href="#" id="a4-print-btn" target="_blank" class="btn btn-primary"><i class="fa-solid fa-print me-1"></i> A4 Print</a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
    let currentPage = 1;
    let nextPageUrl = '';
    let isLoading = false;
    let currentRequest = null;
    let isAnimationFilterActive = false;

    const productGridContainer = $('#product-grid-container');
    const loadingSpinner = $('#loading-spinner');
    const productGridWrapper = $('#product-grid-wrapper');
    const productDetailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
const invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
     // =================================================================
    // Cart State and Core Functions
    // =================================================================
      let cart = [];

    // Main function to render the cart table and summary
    function renderCart() {
    const cartBody = $('#cart-table-body');
    cartBody.empty();
    $('#select-all-cart-items').prop('checked', false);

    if (cart.length === 0) {
        // Updated colspan to 6 to account for the new column
        cartBody.html('<tr><td colspan="6" class="text-center text-muted p-4">Cart is empty</td></tr>');
        calculateSummary();
        return;
    }

    cart.forEach((item, index) => {
        let itemHtml = '';
        const subtotal = (item.price * item.quantity).toLocaleString();
        
        if (item.type === 'product') {
            itemHtml = `
                <tr data-cart-id="${item.id}">
                    <td class="text-center"><input class="form-check-input cart-item-checkbox" type="checkbox" value="${item.id}"></td>
                    <td>${index + 1}</td>
                    <td>
                        <strong>${item.productName}</strong><br>
                        <small class="text-muted">${item.colorName}, ${item.sizeName}</small>
                    </td>
                    <td><div class="input-group input-group-sm qty-control"><button class="btn btn-light border qty-minus" type="button">-</button><input type="text" class="form-control text-center cart-quantity-input" value="${item.quantity}" min="1"><button class="btn btn-light border qty-plus" type="button">+</button></div></td>
                    <td class="text-end">৳${subtotal}</td>
                    <td class="text-center"><button class="btn btn-sm btn-link text-danger remove-item-btn"><i class="fa-solid fa-xmark"></i></button></td>
                </tr>
            `;
        } else if (item.type === 'bundle') {
            let productListHtml = item.products.map(p => `<li><small>${p.productName} (${p.colorName}, ${p.sizeName})</small></li>`).join('');
            itemHtml = `
                <tr data-cart-id="${item.id}">
                    <td class="text-center"><input class="form-check-input cart-item-checkbox" type="checkbox" value="${item.id}"></td>
                    <td>${index + 1}</td>
                    <td>
                        <strong>${item.bundleTitle}</strong>
                        <ul class="list-unstyled mb-0 ps-3">${productListHtml}</ul>
                    </td>
                    <td><div class="input-group input-group-sm qty-control"><input type="text" class="form-control text-center" value="1" disabled></div></td>
                    <td class="text-end">৳${subtotal}</td>
                    <td class="text-center"><button class="btn btn-sm btn-link text-danger remove-item-btn"><i class="fa-solid fa-xmark"></i></button></td>
                </tr>
            `;
        }
        cartBody.append(itemHtml);
    });

    calculateSummary();
}

    function calculateSummary() {
    let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    // 1. Get the selected discount type ('fixed' or 'percentage')
    const discountType = $('input[name="discountType"]:checked').val();
    const discountValue = parseFloat($('#cart-discount-input').val()) || 0;
     const shippingCost = parseFloat($('#cart-shipping-cost-input').val()) || 0;
    let calculatedDiscount = 0;

    // 2. Calculate the discount based on the selected type
    if (discountType === 'percentage') {
        calculatedDiscount = (subtotal * discountValue) / 100;
    } else { // 'fixed'
        calculatedDiscount = discountValue;
    }
    
    const totalPayable = (subtotal - calculatedDiscount) + shippingCost;
    const totalPaid = parseFloat($('#cart-total-pay-input').val()) || 0;
    const due = totalPayable - totalPaid;
    
    $('#cart-subtotal').text(`৳${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
    $('#cart-total-payable').text(`৳${totalPayable.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
    $('#cart-due').text(`৳${due.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
    $('#cart-cod-input').val(due.toFixed(2));
}

// 3. Add this new event listener for the radio buttons.
// This ensures the summary recalculates when the type is changed.
$('input[name="discountType"]').on('change', calculateSummary);



    // Adds an item to the cart or updates quantity
    function addToCart(item) {
        const existingItem = cart.find(cartItem => cartItem.id === item.id);
        
        if (existingItem && item.type === 'product') {
            existingItem.quantity += item.quantity;
        } else {
            cart.push(item);
        }

        renderCart();
    }

    // =================================================================
    // Event Handlers
    // =================================================================

    // Add single product to cart (Corrected)
    $('#modal-add-to-cart-btn').on('click', function() {
        // 1. Use more specific, scoped selectors to avoid conflicts
        const selectedColor = $('#productDetailModal .color-option-btn.active');
        const selectedSize = $('#productDetailModal .size-option-btn.active');

        if (selectedColor.length === 0) {
            Swal.fire('Wait!', 'Please select a color.', 'warning');
            return;
        }
        if (selectedSize.length === 0) {
            Swal.fire('Wait!', 'Please select a size.', 'warning');
            return;
        }

        // 2. Get data directly from the elements we've already found
        const variantIndex = selectedColor.data('variant-index');
        const variant = selectedProductData.variants[variantIndex];
        
        const sizeId = selectedSize.data('size-id');
        // 3. More robust way to get the name: trim whitespace before splitting
        const sizeName = selectedSize.text().trim().split(' ')[0]; 
        
        const quantity = parseInt($('#modal-quantity').val());
        const finalPrice = (selectedProductData.discount_price > 0 && selectedProductData.discount_price < selectedProductData.base_price)
                            ? selectedProductData.discount_price
                            : selectedProductData.base_price;
        
        const cartItem = {
            id: `product-${selectedProductData.id}-${variant.id}-${sizeId}`,
            type: 'product',
            productId: selectedProductData.id,
            productName: selectedProductData.name,
            variantId: variant.id,
            colorName: variant.color.name,
            sizeId: sizeId,
            sizeName: sizeName, // This is now correctly retrieved
            quantity: quantity,
            price: finalPrice,
            image: selectedProductData.main_image[0] ?? null
        };

        addToCart(cartItem);
        Swal.fire({ icon: 'success', title: 'Added to Cart!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
        productDetailModal.hide();
    });

    // Add bundle to cart
    $('#add-bundle-to-cart-btn').on('click', function() {
        const cartItem = {
            id: `bundle-${Date.now()}`, // Unique ID for the bundle instance
            type: 'bundle',
            bundleId: detailData.bundle.id,
            bundleTitle: $('#bundle-detail-title').text(),
            price: parseFloat($('#bundle-detail-price').text().replace(/[^0-9.-]+/g,"")),
            quantity: 1,
            products: Object.values(bundleSelections)
        };
        
        addToCart(cartItem);
        Swal.fire({ icon: 'success', title: 'Bundle Added!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
        bundleDetailModal.hide();
    });

    // Handle dynamic cart actions (quantity, remove)
     // Handle dynamic cart actions (quantity, remove)
    $('#cart-table-body').on('click', function(e) {
        const target = $(e.target);
        const cartRow = target.closest('tr');
        const cartId = cartRow.data('cart-id');
        const itemIndex = cart.findIndex(item => item.id === cartId);

        if (itemIndex === -1) return;

        // Remove item (Single Delete)
        if (target.closest('.remove-item-btn').length) {
            cart.splice(itemIndex, 1);
            renderCart();
        }

        // Quantity plus
        if (target.closest('.qty-plus').length) {
            cart[itemIndex].quantity++;
            renderCart();
        }

        // Quantity minus
        if (target.closest('.qty-minus').length) {
            if (cart[itemIndex].quantity > 1) {
                cart[itemIndex].quantity--;
                renderCart();
            }
        }
    });

    
    // Handle manual quantity input
    $('#cart-table-body').on('change', '.cart-quantity-input', function() {
        const cartId = $(this).closest('tr').data('cart-id');
        const itemIndex = cart.findIndex(item => item.id === cartId);
        let newQty = parseInt($(this).val());
        
        if (itemIndex > -1 && newQty > 0) {
            cart[itemIndex].quantity = newQty;
        }
        renderCart(); // Re-render to validate and update totals
    });

    // Handle summary calculations on input change
    $('#cart-discount-input,#cart-shipping-cost-input, #cart-total-pay-input').on('keyup', calculateSummary);

    // "Select All" checkbox functionality
    $('#select-all-cart-items').on('change', function() {
        $('.cart-item-checkbox').prop('checked', $(this).is(':checked'));
    });

    // "Delete Selected" button functionality
    $('#delete-selected-btn').on('click', function() {
        const idsToDelete = [];
        $('.cart-item-checkbox:checked').each(function() {
            idsToDelete.push($(this).val());
        });

        if (idsToDelete.length === 0) {
            Swal.fire('No items selected', 'Please select items to delete using the checkboxes.', 'info');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${idsToDelete.length} item(s).`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Filter the cart array, keeping only items whose ID is NOT in the idsToDelete array
                cart = cart.filter(item => !idsToDelete.includes(item.id));
                renderCart();
                Swal.fire('Deleted!', 'The selected items have been removed.', 'success');
            }
        });
    });

    // Clear cart button (renamed from "Delete Selected" for clarity, now a separate function)
    $('#clear-cart-btn').on('click', function() {
        if (cart.length === 0) {
            Swal.fire('Cart is already empty', '', 'info');
            return;
        }
        Swal.fire({
            title: 'Are you sure?',
            text: "This will clear all items from your cart.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                renderCart();
                Swal.fire('Cleared!', 'Your cart has been emptied.', 'success');
            }
        });
    });

    // Initial render
    renderCart();


    $('#process-order-btn').on('click', function() {
        if (cart.length === 0) { Swal.fire('Cart is empty', 'Please add products to the cart.', 'warning'); return; }
        const btn = $(this);
        const orderData = { 
            customer_id: $('#selected-customer-id').val(),
             cart: cart, 
             notes: $('#order-notes').val(),
             subtotal: parseFloat($('#cart-subtotal').text().replace(/[^0-9.-]+/g,"")), 
             discount: parseFloat($('#cart-discount-input').val()) || 0, 
             total_payable: parseFloat($('#cart-total-payable').text().replace(/[^0-9.-]+/g,"")), 
             total_pay: parseFloat($('#cart-total-pay-input').val()) || 0, 
             cod: parseFloat($('#cart-cod-input').val()) || 0, // Add cod to the data
              shipping_cost: parseFloat($('#cart-shipping-cost-input').val()) || 0,
             due: parseFloat($('#cart-due').text().replace(/[^0-9.-]+/g,"")), 
             payment_method: $('#payment-type-select').val(), 
             _token: "{{ csrf_token() }}" };
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
        
        $.ajax({
            url: "{{ route('pos.orders.store') }}",
            type: 'POST',
            data: JSON.stringify(orderData),
            contentType: 'application/json',
            success: (response) => {
                Swal.fire({ icon: 'success', title: 'Order Placed!', text: response.message });
                
                if(response.order_id) {
                    const orderId = response.order_id;

                    // --- CORRECTED PART: Use route() helper with a placeholder ---
                    let invoiceUrlTemplate = "{{ route('pos.orders.invoice', ['order' => ':id']) }}";
                    let printUrlTemplate = "{{ route('pos.orders.print', ['order' => ':id']) }}";

                    let finalInvoiceUrl = invoiceUrlTemplate.replace(':id', orderId);
                    let finalPrintUrlBase = printUrlTemplate.replace(':id', orderId);
                    // --- END CORRECTION ---

                    $.ajax({
                        url: finalInvoiceUrl, // Use the correctly generated URL
                        type: 'GET',
                        success: function(invoiceHtml) {
                            $('#invoice-content').html(invoiceHtml);
                            // Set dynamic print URLs
                            $('#pos-print-btn').attr('href', `${finalPrintUrlBase}?type=pos`);
                            $('#a4-print-btn').attr('href', `${finalPrintUrlBase}?type=a4`);
                            invoiceModal.show();
                        }
                    });
                }
            },
            error: (xhr) => {
                let msg = 'An unexpected error occurred.';
                if (xhr.responseJSON) msg = xhr.responseJSON.message || Object.values(xhr.responseJSON.errors).join('\n');
                Swal.fire('Order Failed', msg, 'error');
            },
            complete: () => { btn.prop('disabled', false).html('Pay'); }
        });
    });

     // New: Reset the cart and UI only after the invoice modal is closed.
    // Update the modal close handler to reset the new COD input
$('#invoiceModal').on('hidden.bs.modal', function () {
    cart = [];
    renderCart();
    $('#cart-discount-input, #cart-shipping-cost-input, #cart-total-pay-input, #cart-cod-input').val('0.00');
      $('#order-notes').val('');
    $('input[name="discountType"][value="fixed"]').prop('checked', true);
    calculateSummary();
});

    // NEW FUNCTION: Checks if the container is scrollable and loads more if not.
    function checkAndLoadMore() {
        // Continue loading if not currently loading, a next page exists, AND the content is not yet tall enough to scroll.
        if (!isLoading && nextPageUrl && productGridWrapper.prop('scrollHeight') <= productGridWrapper.prop('clientHeight')) {
            const nextPage = new URL(nextPageUrl).searchParams.get('page');
            fetchProducts(nextPage);
        }
    }

    // Function to fetch products
   function fetchProducts(page = 1, replace = false) {
        if (isLoading) return;
        isLoading = true;
        loadingSpinner.show();

        // Add the new boolean flag to the data payload
        const data = {
            page: page,
            search: $('#product-search-input').val(),
            category_id: $('#product-category-select').val(),
            filter_by_all_animation: isAnimationFilterActive 
        };

        if(currentRequest) {
            currentRequest.abort();
        }

        currentRequest = $.ajax({
            url: "{{ route('pos.products.get') }}",
            type: 'GET',
            data: data, // Use the updated data object
            success: function(response) {
                if (replace) {
                    productGridContainer.html(response.html);
                } else {
                    productGridContainer.append(response.html);
                }
                nextPageUrl = response.next_page_url;
                currentPage = page;
                checkAndLoadMore();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus !== 'abort') {
                    console.error('Failed to fetch products:', errorThrown);
                    Swal.fire('Error!', 'Could not fetch products.', 'error');
                }
            },
            complete: function() {
                loadingSpinner.hide();
                isLoading = false;
                currentRequest = null;
            }
        });
    }

    // Initial product load
    fetchProducts(1, true);

    // Debounce function to limit AJAX calls on search
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Event listener for search input (Updated)
    $('#product-search-input').on('keyup', debounce(function() {
        isAnimationFilterActive = false; // Deactivate animation filter
        $('#reset-filter-btn').hide();
        fetchProducts(1, true);
    }, 500));

    // Event listener for category select (Updated)
    $('#product-category-select').on('change', function() {
        isAnimationFilterActive = false; // Deactivate animation filter
        $('#reset-filter-btn').hide();
        fetchProducts(1, true);
    });

    // --- New Filter Logic ---
    
    // In your action-buttons-bar, change the button to this:
    // <button id="filter-by-animation-btn" class="action-btn">Animation Category</button>
    $('#filter-by-animation-btn').on('click', function() {
        isAnimationFilterActive = true; // Activate the filter
        
        // Reset other filters for clarity
        $('#product-search-input').val('');
        $('#product-category-select').val('');
        
        fetchProducts(1, true); // Fetch filtered products
        $('#reset-filter-btn').show(); // Show the reset button
    });

    // Handles click on the reset button (Updated)
    $('#reset-filter-btn').on('click', function() {
        isAnimationFilterActive = false; // Deactivate the filter
        fetchProducts(1, true); // Fetch all products
        $(this).hide(); // Hide the reset button
    });

    // Event listener for infinite scroll
    productGridWrapper.on('scroll', function() {
        if (!isLoading && nextPageUrl) {
            const scrollHeight = $(this).prop('scrollHeight');
            const scrollTop = $(this).scrollTop();
            const clientHeight = $(this).prop('clientHeight');

            if (scrollTop + clientHeight >= scrollHeight - 300) { // 300px buffer
                const nextPage = new URL(nextPageUrl).searchParams.get('page');
                fetchProducts(nextPage);
            }
        }
    });

    // --- PRODUCT MODAL LOGIC ---

    let selectedProductData = null;

    // Event delegation for clicking a product card
    productGridContainer.on('click', '.product-card', function() {
        const productId = $(this).data('product-id');
        
        let urlTemplate = "{{ route('pos.products.details', ['product' => ':id']) }}";
        let productUrl = urlTemplate.replace(':id', productId);

        $.ajax({
            url: productUrl,
            type: 'GET',
            success: function(product) {
                selectedProductData = product;
                populateModal(product);
                productDetailModal.show();
            },
            error: function() {
                Swal.fire('Error!', 'Could not fetch product details.', 'error');
            }
        });
    });

     function populateModal(product) {
        $('#modal-product-name').text(product.name);
        
        // --- MODIFIED: Price display in modal ---
        const priceContainer = $('#modal-product-price');
        if (product.discount_price > 0 && product.discount_price < product.base_price) {
            const priceHtml = `৳${parseFloat(product.discount_price).toLocaleString()} <del class="text-danger small ms-2">৳${parseFloat(product.base_price).toLocaleString()}</del>`;
            priceContainer.html(priceHtml);
        } else {
            priceContainer.text(`৳${parseFloat(product.base_price).toLocaleString()}`);
        }
        // --- END MODIFICATION ---

        const imageUrl = product.main_image && product.main_image.length > 0 
            ? `{{ asset('public/uploads') }}/${product.main_image[0]}`
            : 'https://via.placeholder.com/400';
        $('#modal-product-image').attr('src', imageUrl);

        // Populate colors
        const colorOptions = $('#modal-color-options');
        colorOptions.empty();
        product.variants.forEach((variant, index) => {
            const colorBtn = $(`
                <button type="button" class="btn btn-outline-secondary color-option-btn" data-variant-index="${index}">
                    ${variant.color.name}
                </button>
            `);
            colorOptions.append(colorBtn);
        });

        // Reset sizes and quantity
        $('#modal-size-options').empty().append('<p class="text-muted">Select a color to see sizes.</p>');
        $('#modal-quantity').val(1);
    }

    // Handle color selection
     $(document).on('click', '.color-option-btn', function() {
        $(this).addClass('active').siblings().removeClass('active');
        const variantIndex = $(this).data('variant-index');
        const variant = selectedProductData.variants[variantIndex];
        
        const sizeOptions = $('#modal-size-options');
        sizeOptions.empty();

        if (variant.detailed_sizes && variant.detailed_sizes.length > 0) {
            variant.detailed_sizes.forEach((size, index) => {
                if(size.quantity > 0) {
                    const sizeBtn = $(`
                        <button type="button" class="btn btn-outline-secondary size-option-btn" data-size-id="${size.id}" data-max-qty="${size.quantity}">
                            ${size.name} <span class="badge bg-secondary">${size.quantity}</span>
                        </button>
                    `);
                    sizeOptions.append(sizeBtn);
                }
            });
        } else {
            sizeOptions.append('<p class="text-danger">No sizes available for this color.</p>');
        }
    });

    // Handle size selection
    $(document).on('click', '.size-option-btn', function() {
        $(this).addClass('active').siblings().removeClass('active');
        const maxQty = $(this).data('max-qty');
        $('#modal-quantity').attr('max', maxQty);
    });

    // Handle quantity controls
    $('#modal-quantity-plus').on('click', function() {
        let qtyInput = $('#modal-quantity');
        let currentVal = parseInt(qtyInput.val());
        let maxVal = parseInt(qtyInput.attr('max') || 999);
        if (currentVal < maxVal) {
            qtyInput.val(currentVal + 1);
        }
    });

    $('#modal-quantity-minus').on('click', function() {
        let qtyInput = $('#modal-quantity');
        let currentVal = parseInt(qtyInput.val());
        if (currentVal > 1) {
            qtyInput.val(currentVal - 1);
        }
    });

   

    // --- BUNDLE OFFER LOGIC ---

    const bundleListModal = new bootstrap.Modal(document.getElementById('bundleOfferListModal'));
    const bundleDetailModal = new bootstrap.Modal(document.getElementById('bundleOfferDetailModal'));
    let bundleSelections = {};
    let requiredProductIds = [];
    const storageBaseUrl = "{{ asset('public/uploads') }}";

    // 1. Fetch and display all available bundle offers
    $('#bundle-offer-btn').on('click', function() {
        $('#bundle-list-loading').show();
        $('#bundle-offer-grid').empty();
        
        $.ajax({
            url: "{{ route('pos.bundle-offers.get') }}",
            type: 'GET',
            success: function(offers) {
                if (offers.length === 0) {
                    $('#bundle-offer-grid').html('<p class="text-muted text-center col-12">No active bundle offers available right now.</p>');
                    return;
                }
                
                offers.forEach(offer => {
                    offer.bundle_offer_products.forEach(productSet => {
                        const imageUrl = productSet.image 
                            ? `{{ asset('public/uploads') }}/${productSet.image}` 
                            : 'https://via.placeholder.com/200';

                        const cardHtml = `
                            <div class="col">
                                <div class="card product-card bundle-offer-card" role="button" data-bundle-product-id="${productSet.id}">
                                    <img src="${imageUrl}" class="card-img-top" alt="${productSet.title}">
                                    <div class="card-body">
                                        <h6 class="card-title">${productSet.title}</h6>
                                        <div class="product-price">
                                            <span>৳${parseFloat(productSet.discount_price).toLocaleString()}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#bundle-offer-grid').append(cardHtml);
                    });
                });
            },
            error: function() {
                Swal.fire('Error!', 'Could not fetch bundle offers.', 'error');
            },
            complete: function() {
                 $('#bundle-list-loading').hide();
            }
        });
    });

    // 2. Fetch details for a specific bundle and open the detail modal
    $(document).on('click', '.bundle-offer-card', function() {
        const bundleProductId = $(this).data('bundle-product-id');
        bundleListModal.hide();
        
        $.ajax({
            url: `{{ url('bundle-offers') }}/${bundleProductId}`,
            type: 'GET',
            success: function(data) {
                populateBundleDetailModal(data);
                bundleDetailModal.show();
            },
            error: function() {
                 Swal.fire('Error!', 'Could not fetch bundle details.', 'error');
            }
        });
    });

    // 3. Populate the detail modal with product selectors
    function populateBundleDetailModal(data) {
        const { bundle, products } = data;
        bundleSelections = {}; // Reset selections
        requiredProductIds = products.map(p => p.id); // Store IDs of products in this bundle
        
        $('#bundle-detail-title').text(bundle.title);
        $('#bundle-detail-price').text(`৳${parseFloat(bundle.discount_price).toLocaleString()}`);
        
        const selectionArea = $('#bundle-product-selection-area');
        selectionArea.empty();
        
        products.forEach(product => {
            let colorOptionsHtml = '';
            product.variants.forEach((variant, index) => {
                colorOptionsHtml += `<button type="button" class="btn btn-sm btn-outline-secondary bundle-color-btn" data-product-id="${product.id}" data-variant-index="${index}">${variant.color.name}</button>`;
            });

            const imageUrl = product.main_image && product.main_image.length > 0 
                            ? `${storageBaseUrl}/${product.main_image[0]}`
                            : 'https://via.placeholder.com/100';

             const productHtml = `
                <div class="card mb-3" id="bundle-product-${product.id}">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <img src="${imageUrl}" class="rounded" alt="${product.name}" style="width: 80px; height: 80px; object-fit: contain; border: 1px solid #eee;">
                            </div>
                            <div class="col">
                                <h5 class="card-title mb-2">${product.name}</h5>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Color:</label>
                                    <div class="d-flex flex-wrap gap-2">${colorOptionsHtml}</div>
                                </div>
                                <div>
                                    <label class="form-label small fw-bold">Size:</label>
                                    <div class="d-flex flex-wrap gap-2 bundle-size-options" data-product-id="${product.id}">
                                        <p class="text-muted small m-0">Select a color first</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            selectionArea.append(productHtml);
        });
        updateBundleSummary();
    }

    // 4. Handle color selection within the bundle
    $(document).on('click', '.bundle-color-btn', function() {
        const productId = $(this).data('product-id');
        const variantIndex = $(this).data('variant-index');
        
        $(this).addClass('active').siblings().removeClass('active');
        
        // Find the product data from the original AJAX response stored in a variable
        const productData = detailData.products.find(p => p.id === productId);
        const variant = productData.variants[variantIndex];
        
        const sizeOptions = $(`.bundle-size-options[data-product-id="${productId}"]`);
        sizeOptions.empty();
        
        if (variant.detailed_sizes && variant.detailed_sizes.length > 0) {
            variant.detailed_sizes.forEach(size => {
                 if (size.quantity > 0) {
                    sizeOptions.append(`<button type="button" class="btn btn-sm btn-outline-secondary bundle-size-btn" data-product-id="${productId}" data-variant-index="${variantIndex}" data-size-id="${size.id}">${size.name}</button>`);
                }
            });
        } else {
            sizeOptions.html('<p class="text-danger small m-0">No sizes for this color.</p>');
        }

        // Clear previous size selection for this product
        if (bundleSelections[productId]) {
            delete bundleSelections[productId].sizeId;
            delete bundleSelections[productId].sizeName;
        }
        bundleSelections[productId] = { 
            productId: productId,
            productName: productData.name,
            variantId: variant.id,
            colorName: variant.color.name,
        };
        updateBundleSummary();
    });

    // 5. Handle size selection within the bundle
    $(document).on('click', '.bundle-size-btn', function() {
        const productId = $(this).data('product-id');
        $(this).addClass('active').siblings().removeClass('active');
        
        bundleSelections[productId].sizeId = $(this).data('size-id');
        bundleSelections[productId].sizeName = $(this).text().trim();
        updateBundleSummary();
    });

    // 6. Update the summary list and check if cart button can be enabled
    function updateBundleSummary() {
        const summaryList = $('#bundle-selection-summary');
        summaryList.empty();
        let allProductsSelected = true;

        requiredProductIds.forEach(productId => {
            const selection = bundleSelections[productId];
            if (selection && selection.sizeId) {
                summaryList.append(`<li class="list-group-item">${selection.productName} - ${selection.colorName}, ${selection.sizeName}</li>`);
            } else {
                summaryList.append(`<li class="list-group-item text-muted">${selection ? selection.productName : 'Product'} - Waiting for selection...</li>`);
                allProductsSelected = false;
            }
        });
        
        $('#add-bundle-to-cart-btn').prop('disabled', !allProductsSelected);
    }
    
    // Store data from detail ajax to use in event handlers
    let detailData = {};
    $(document).ajaxSuccess(function(event, xhr, settings) {
        if (settings.url.includes('bundle-offers/')) {
            detailData = xhr.responseJSON;
        }
    });

  
});
</script>
@endsection