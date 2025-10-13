@extends('pos.master.master')

@section('title', 'POS - Sales')
@section('styles')
  <style>
        /* Visually indicates that the left panel is locked */
        .left-panel.locked {
            opacity: 0.8;
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('body')
   <div class="row g-2">
                <div class="col-lg-5">
                    <div class="card left-panel p-2">
                            <div class="cart-top-inputs">
                            <div class="position-relative">
                                <div class="input-group mb-2">
                                    <input type="text" id="customer-search" class="form-control" placeholder="Search and select customer...">
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
    
    {{-- --- SECTION UPDATED --- --}}
    <div class="d-flex gap-2 mt-2">
        <button id="lock-order-btn" class="btn btn-lock w-100"><i class="fa-solid fa-lock"></i></button>
        <button id="cancel-order-btn" class="btn btn-cancel w-100">Cancel</button>
        <button id="process-order-btn" class="btn btn-pay w-100">Pay</button>
    </div>
    {{-- --- END UPDATE --- --}}
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
                         {{-- /// START: ADD THIS LINE /// --}}
                        <p id="modal-product-categories" class="mb-2"></p>
                        {{-- /// END: ADD THIS LINE /// --}}
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
            {{-- --- MODAL FOOTER UPDATED --- --}}
            <div class="modal-footer">
                <a href="#" id="pos-print-btn" target="_blank" class="btn btn-secondary"><i class="fa-solid fa-receipt me-1"></i> POS Print</a>
                <a href="#" id="a4-print-btn" target="_blank" class="btn btn-primary"><i class="fa-solid fa-print me-1"></i> A4 Print</a>
                <a href="#" id="a5-print-btn" target="_blank" class="btn btn-info"><i class="fa-solid fa-file-invoice me-1"></i> A5 Print</a>
            </div>
             {{-- --- END OF UPDATE --- --}}
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
let isCartLocked = false;
    const productGridContainer = $('#product-grid-container');
    const loadingSpinner = $('#loading-spinner');
    const productGridWrapper = $('#product-grid-wrapper');
    const productDetailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
    const invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
    let cart = [];


    // --- NEW: Event Listener for the Cancel Button ---
    $('#cancel-order-btn').on('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will cancel the current transaction and clear everything.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // 1. Reset the cart array
                cart = [];
                renderCart(); // This will also call calculateSummary

                // 2. Reset all summary inputs and notes
                $('#cart-discount-input, #cart-shipping-cost-input, #cart-total-pay-input, #cart-cod-input').val('0.00');
                $('#order-notes').val('');
                $('input[name="discountType"][value="fixed"]').prop('checked', true);
                   calculateSummary();
                // 3. Reset the customer selection by calling the global function from master.blade.php
                if (typeof setDefaultCustomer === 'function') {
                    setDefaultCustomer();
                }

                // 4. If the cart was locked, unlock it
                if (isCartLocked) {
                    $('#lock-order-btn').click(); 
                }

                Swal.fire('Cancelled!', 'The transaction has been cancelled.', 'success');
            }
        });
    });

    // --- NEW: Event Listener for the Lock Button ---
    $('#lock-order-btn').on('click', function() {
        isCartLocked = !isCartLocked; // Toggle the state
        const lockIcon = $(this).find('i');
        const leftPanel = $('.left-panel');
        const elementsToToggle = leftPanel.find('input, select, textarea, button').not(this);

        if (isCartLocked) {
            // Lock the cart
            elementsToToggle.prop('disabled', true);
            leftPanel.addClass('locked');
            lockIcon.removeClass('fa-lock').addClass('fa-lock-open');
            $(this).removeClass('btn-lock').addClass('btn-success'); // Change color
            Swal.fire({ icon: 'info', title: 'Cart Locked', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
        } else {
            // Unlock the cart
            elementsToToggle.prop('disabled', false);
            leftPanel.removeClass('locked');
            lockIcon.removeClass('fa-lock-open').addClass('fa-lock');
            $(this).removeClass('btn-success').addClass('btn-lock'); // Revert color
            Swal.fire({ icon: 'success', title: 'Cart Unlocked', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
        }
    });

    // --- 1. RENDER CART FUNCTION (UPDATED) ---
    // Renders the cart table based on the cart array.
    function renderCart() {
        const cartBody = $('#cart-table-body');
        cartBody.empty();
        $('#select-all-cart-items').prop('checked', false);

        if (cart.length === 0) {
            cartBody.html('<tr><td colspan="6" class="text-center text-muted p-4">Cart is empty</td></tr>');
            calculateSummary();
            return;
        }

        cart.forEach((item, index) => {
            let itemHtml = '';
            const subtotal = (item.price * item.quantity).toLocaleString();
            
            // --- NEW: Display logic for discounted items ---
            let itemDescriptionHtml = `
                <strong>${item.productName}</strong><br>
                <small class="text-muted">${item.colorName}, ${item.sizeName}</small>
            `;
            if (item.isDiscounted) {
                itemDescriptionHtml += `
                    <br><small class="fw-bold text-success">Price: ৳${item.price.toLocaleString()} 
                    <del class="text-danger small">৳${item.basePrice.toLocaleString()}</del></small>
                `;
            }else{
                itemDescriptionHtml += `<br><small class="fw-bold text-primary">Price: ৳${item.price.toLocaleString()}</small>`;
            }
            // --- END NEW ---

            if (item.type === 'product') {
                itemHtml = `
                    <tr data-cart-id="${item.id}">
                        <td class="text-center"><input class="form-check-input cart-item-checkbox" type="checkbox" value="${item.id}"></td>
                        <td>${index + 1}</td>
                        <td>${itemDescriptionHtml}</td>
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

    // --- 2. CALCULATE SUMMARY FUNCTION (UPDATED) ---
    // Calculates all totals, considering which products are eligible for discounts.
    function calculateSummary() {
        let discountableSubtotal = 0;
        let nonDiscountableSubtotal = 0;

        // --- NEW: Segregate items based on whether they are already discounted ---
        cart.forEach(item => {
            const itemSubtotal = item.price * item.quantity;
            if (item.isDiscounted || item.type === 'bundle') { // Bundles are also non-discountable
                nonDiscountableSubtotal += itemSubtotal;
            } else {
                discountableSubtotal += itemSubtotal;
            }
        });
        // --- END NEW ---

        const totalSubtotal = discountableSubtotal + nonDiscountableSubtotal;
        const discountType = $('input[name="discountType"]:checked').val();
        const discountValue = parseFloat($('#cart-discount-input').val()) || 0;
        const shippingCost = parseFloat($('#cart-shipping-cost-input').val()) || 0;
        let calculatedDiscount = 0;

        // --- UPDATED: Apply overall discount ONLY to eligible items ---
        if (discountType === 'percentage') {
            calculatedDiscount = (discountableSubtotal * discountValue) / 100;
        } else {
            calculatedDiscount = discountValue > discountableSubtotal ? discountableSubtotal : discountValue;
        }
        // --- END UPDATE ---
        
        const totalPayable = (discountableSubtotal - calculatedDiscount) + nonDiscountableSubtotal + shippingCost;
        const totalPaid = parseFloat($('#cart-total-pay-input').val()) || 0;
        const due = totalPayable - totalPaid;
        
        $('#cart-subtotal').text(`৳${totalSubtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
        $('#cart-total-payable').text(`৳${totalPayable.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
        $('#cart-due').text(`৳${due.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
        $('#cart-cod-input').val(due.toFixed(2));
    }
    
    // Recalculate summary when discount type changes
    $('input[name="discountType"]').on('change', calculateSummary);

    function addToCart(item) {
        const existingItem = cart.find(cartItem => cartItem.id === item.id);
        if (existingItem && item.type === 'product') {
            existingItem.quantity += item.quantity;
        } else {
            cart.push(item);
        }
        renderCart();
    }

    // --- 3. ADD TO CART FUNCTION (UPDATED) ---
    // Adds product to cart from the modal, now includes all price details.
    $('#modal-add-to-cart-btn').on('click', function() {
        const selectedColor = $('#productDetailModal .color-option-btn.active');
        const selectedSize = $('#productDetailModal .size-option-btn.active');

        if (selectedColor.length === 0 || selectedSize.length === 0) {
            Swal.fire('Wait!', 'Please select a color and a size.', 'warning');
            return;
        }
        
        const variantIndex = selectedColor.data('variant-index');
        const variant = selectedProductData.variants[variantIndex];
        const sizeId = selectedSize.data('size-id');
        const sizeName = selectedSize.text().trim().split(' ')[0]; 
        const quantity = parseInt($('#modal-quantity').val());
        const maxQty = parseInt(selectedSize.data('max-qty')); // Get max quantity from the button

        // --- NEW: Determine the correct price and discount status ---
        const isDiscounted = selectedProductData.discount_price > 0 && selectedProductData.discount_price < selectedProductData.base_price;
        const priceForCalculation = isDiscounted ? selectedProductData.discount_price : selectedProductData.base_price;
        // --- END NEW ---
        
        const cartItem = {
            id: `product-${selectedProductData.id}-${variant.id}-${sizeId}`,
            type: 'product',
            productId: selectedProductData.id,
            productName: selectedProductData.name,
            variantId: variant.id,
            colorName: variant.color.name,
            sizeId: sizeId,
            sizeName: sizeName,
            quantity: quantity,
            price: priceForCalculation, // Price used for all calculations
            basePrice: selectedProductData.base_price, // Original price
            isDiscounted: isDiscounted, // Flag to identify discounted items
            maxQty: maxQty // **<-- ADD THIS LINE to store the stock limit**
        };

        addToCart(cartItem);
        Swal.fire({ icon: 'success', title: 'Added to Cart!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
        productDetailModal.hide();
    });

    $('#add-bundle-to-cart-btn').on('click', function() {
        const cartItem = {
            id: `bundle-${Date.now()}`,
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

    $('#cart-table-body').on('click', function(e) {
        const target = $(e.target);
        const cartRow = target.closest('tr');
        const cartId = cartRow.data('cart-id');
        const itemIndex = cart.findIndex(item => item.id === cartId);
        if (itemIndex === -1) return;
        
        const item = cart[itemIndex]; // Get the item for easier access

        if (target.closest('.remove-item-btn').length) {
            cart.splice(itemIndex, 1);
            renderCart();
        }

        // **--- BLOCK TO UPDATE ---**
        if (target.closest('.qty-plus').length) {
            // Check against the stored max quantity
            if (item.quantity < item.maxQty) {
                item.quantity++;
                renderCart();
            } else {
                // Show a warning if stock limit is reached
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock Limit Reached!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }
        // **--- END OF UPDATE ---**

        if (target.closest('.qty-minus').length) {
            if (item.quantity > 1) {
                item.quantity--;
                renderCart();
            }
        }
    });

    $('#cart-table-body').on('change', '.cart-quantity-input', function() {
        const cartId = $(this).closest('tr').data('cart-id');
        const itemIndex = cart.findIndex(item => item.id === cartId);
        let newQty = parseInt($(this).val());
        if (itemIndex > -1 && newQty > 0) {
            cart[itemIndex].quantity = newQty;
        }
        renderCart();
    });

    $('#cart-discount-input,#cart-shipping-cost-input, #cart-total-pay-input').on('keyup', calculateSummary);

    $('#select-all-cart-items').on('change', function() {
        $('.cart-item-checkbox').prop('checked', $(this).is(':checked'));
    });

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

                 cart = cart.filter(item => !idsToDelete.includes(item.id));
                 // --- FIX: Reset inputs if the cart becomes empty ---
                if (cart.length === 0) {
                    $('#cart-discount-input, #cart-shipping-cost-input, #cart-total-pay-input, #cart-cod-input').val('0.00');
                    $('input[name="discountType"][value="fixed"]').prop('checked', true);
                }
                // --- END FIX ---
                renderCart();
                Swal.fire('Deleted!', 'The selected items have been removed.', 'success');
            }
        });
    });

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
                   // --- FIX: Reset all summary inputs before re-rendering ---
                $('#cart-discount-input, #cart-shipping-cost-input, #cart-total-pay-input, #cart-cod-input').val('0.00');
                $('input[name="discountType"][value="fixed"]').prop('checked', true);
                // --- END FIX ---
                renderCart();
                Swal.fire('Cleared!', 'Your cart has been emptied.', 'success');
            }
        });
    });

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
             cod: parseFloat($('#cart-cod-input').val()) || 0,
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
                
                // --- JAVASCRIPT BLOCK UPDATED ---
                if(response.order_id) {
                    const orderId = response.order_id;
                    // URL for fetching the modal content
                    let invoiceUrlTemplate = "{{ route('pos.orders.invoice', ['order' => ':id']) }}";
                    let finalInvoiceUrl = invoiceUrlTemplate.replace(':id', orderId);

                    // URLs for the new print routes
                    let posPrintUrlTemplate = "{{ route('order.print.pos', ['order' => ':id']) }}";
                    let a4PrintUrlTemplate = "{{ route('order.print.a4', ['order' => ':id']) }}";
                    let a5PrintUrlTemplate = "{{ route('order.print.a5', ['order' => ':id']) }}";

                    let finalPosPrintUrl = posPrintUrlTemplate.replace(':id', orderId);
                    let finalA4PrintUrl = a4PrintUrlTemplate.replace(':id', orderId);
                    let finalA5PrintUrl = a5PrintUrlTemplate.replace(':id', orderId);

                    $.ajax({
                        url: finalInvoiceUrl,
                        type: 'GET',
                        success: function(invoiceHtml) {
                            $('#invoice-content').html(invoiceHtml);
                            $('#pos-print-btn').attr('href', finalPosPrintUrl);
                            $('#a4-print-btn').attr('href', finalA4PrintUrl);
                            $('#a5-print-btn').attr('href', finalA5PrintUrl);
                            invoiceModal.show();
                        }
                    });
                }
                // --- END OF UPDATE ---
            },
            error: (xhr) => {
                let msg = 'An unexpected error occurred.';
                if (xhr.responseJSON) msg = xhr.responseJSON.message || Object.values(xhr.responseJSON.errors).join('\n');
                Swal.fire('Order Failed', msg, 'error');
            },
            complete: () => { btn.prop('disabled', false).html('Pay'); }
        });
    });

    $('#invoiceModal').on('hidden.bs.modal', function () {
        cart = [];
        renderCart();
        $('#cart-discount-input, #cart-shipping-cost-input, #cart-total-pay-input, #cart-cod-input').val('0.00');
        $('#order-notes').val('');
        $('input[name="discountType"][value="fixed"]').prop('checked', true);
        calculateSummary();
    });


    function checkAndLoadMore() {
        if (!isLoading && nextPageUrl && productGridWrapper.prop('scrollHeight') <= productGridWrapper.prop('clientHeight')) {
            const nextPage = new URL(nextPageUrl).searchParams.get('page');
            fetchProducts(nextPage);
        }
    }

   function fetchProducts(page = 1, replace = false) {
        if (isLoading) return;
        isLoading = true;
        loadingSpinner.show();

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
            data: data,
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

    fetchProducts(1, true);

    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    $('#product-search-input').on('keyup', debounce(function() {
        isAnimationFilterActive = false;
        $('#reset-filter-btn').hide();
        fetchProducts(1, true);
    }, 500));

    $('#product-category-select').on('change', function() {
        isAnimationFilterActive = false;
        $('#reset-filter-btn').hide();
        fetchProducts(1, true);
    });

    $('#filter-by-animation-btn').on('click', function() {
        isAnimationFilterActive = true;
        $('#product-search-input').val('');
        $('#product-category-select').val('');
        fetchProducts(1, true);
        $('#reset-filter-btn').show();
    });

    $('#reset-filter-btn').on('click', function() {
        isAnimationFilterActive = false;
        fetchProducts(1, true);
        $(this).hide();
    });

    productGridWrapper.on('scroll', function() {
        if (!isLoading && nextPageUrl) {
            const scrollHeight = $(this).prop('scrollHeight');
            const scrollTop = $(this).scrollTop();
            const clientHeight = $(this).prop('clientHeight');
            if (scrollTop + clientHeight >= scrollHeight - 300) {
                const nextPage = new URL(nextPageUrl).searchParams.get('page');
                fetchProducts(nextPage);
            }
        }
    });

    let selectedProductData = null;

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
        
        const priceContainer = $('#modal-product-price');
        if (product.discount_price > 0 && product.discount_price < product.base_price) {
            const priceHtml = `৳${parseFloat(product.discount_price).toLocaleString()} <del class="text-danger small ms-2">৳${parseFloat(product.base_price).toLocaleString()}</del>`;
            priceContainer.html(priceHtml);
        } else {
            priceContainer.text(`৳${parseFloat(product.base_price).toLocaleString()}`);
        }
   // /// START: JAVASCRIPT UPDATE ///
        const categoriesContainer = $('#modal-product-categories');
        // Check if the product has assigned categories and the array is not empty
        if (product.assigns && product.assigns.length > 0) {
            // Access the first assignment and its category name.
            // Fallback to category_name if the category relation isn't loaded for some reason.
            const firstCategoryName = product.assigns[0].category 
                ? product.assigns[0].category.name 
                : product.assigns[0].category_name;
            
            categoriesContainer.html(`<small class="text-muted"><strong>Category:</strong> ${firstCategoryName}</small>`);
            categoriesContainer.show(); // Make sure the element is visible
        } else {
            categoriesContainer.hide(); // Hide if no categories are assigned
        }
        // /// END: JAVASCRIPT UPDATE ///
        const imageUrl = product.main_image && product.main_image.length > 0 
            ? `{{ asset('public/uploads') }}/${product.main_image[0]}`
            : 'https://via.placeholder.com/400';
        $('#modal-product-image').attr('src', imageUrl);

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

        $('#modal-size-options').empty().append('<p class="text-muted">Select a color to see sizes.</p>');
        $('#modal-quantity').val(1);
    }

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

    $(document).on('click', '.size-option-btn', function() {
        $(this).addClass('active').siblings().removeClass('active');
        const maxQty = $(this).data('max-qty');
        $('#modal-quantity').attr('max', maxQty);
    });

    $('#modal-quantity-plus').on('click', function() {
        const qtyInput = $('#modal-quantity');
        const maxVal = parseInt(qtyInput.attr('max'));

        // **Check**: Do not allow quantity change if a size hasn't been selected
        if (isNaN(maxVal)) {
            Swal.fire({ 
                icon: 'info', 
                title: 'Please select a color and size first.', 
               
                showConfirmButton: true, 
                timer: 2500 
            });
            return; // Stop execution
        }

        let currentVal = parseInt(qtyInput.val()) || 0;
        if (currentVal < maxVal) {
            qtyInput.val(currentVal + 1);
        } else {
            Swal.fire({ 
                icon: 'warning', 
                title: 'Maximum stock reached!', 
             
                showConfirmButton: true, 
                timer: 2000 
            });
        }
    });

    $('#modal-quantity-minus').on('click', function() {
        const qtyInput = $('#modal-quantity');

        // **Check**: Do not allow quantity change if a size hasn't been selected
        if (!qtyInput.attr('max')) {
             Swal.fire({ 
                icon: 'info', 
                title: 'Please select a color and size first.', 
               
                showConfirmButton: false, 
                timer: 2500 
            });
            return; // Stop execution
        }

        let currentVal = parseInt(qtyInput.val()) || 1;
        if (currentVal > 1) {
            qtyInput.val(currentVal - 1);
        }
    });

    const bundleListModal = new bootstrap.Modal(document.getElementById('bundleOfferListModal'));
    const bundleDetailModal = new bootstrap.Modal(document.getElementById('bundleOfferDetailModal'));
    let bundleSelections = {};
    let requiredProductIds = [];
    const storageBaseUrl = "{{ asset('public/uploads') }}";

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

    function populateBundleDetailModal(data) {
        const { bundle, products } = data;
        bundleSelections = {};
        requiredProductIds = products.map(p => p.id);
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

    $(document).on('click', '.bundle-color-btn', function() {
        const productId = $(this).data('product-id');
        const variantIndex = $(this).data('variant-index');
        $(this).addClass('active').siblings().removeClass('active');
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

    $(document).on('click', '.bundle-size-btn', function() {
        const productId = $(this).data('product-id');
        $(this).addClass('active').siblings().removeClass('active');
        bundleSelections[productId].sizeId = $(this).data('size-id');
        bundleSelections[productId].sizeName = $(this).text().trim();
        updateBundleSummary();
    });

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
    
    let detailData = {};
    $(document).ajaxSuccess(function(event, xhr, settings) {
        if (settings.url.includes('bundle-offers/')) {
            detailData = xhr.responseJSON;
        }
    });
});
</script>
@endsection