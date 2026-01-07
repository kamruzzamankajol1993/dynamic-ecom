@extends('admin.master.master')
@section('title', 'Create New Order')

@section('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
         /* --- Global Font & Layout Adjustments --- */
        .main-content {
            font-size: 0.9rem;
        }
        .main-content h2 { font-size: 1.6rem; }
        .main-content h5 { font-size: 1.1rem; }
        .main-content h6 { font-size: 0.95rem; }

        /* --- Beautiful Label Style --- */
        .form-label {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 0.3rem;
            font-size: 0.85rem;
        }

        /* --- Form & Table Adjustments --- */
        .form-control, .form-select, .btn, .ui-autocomplete {
            font-size: 0.875rem;
        }
        .product-information-table th {
            background-color: #f8f9fa;
            font-weight: 500;
            white-space: nowrap;
        }
        .summary-table td {
            border: none;
        }
        .total-due {
            background-color: #198754;
            color: white;
            padding: 1rem;
            border-radius: 0.25rem;
        }
        .address-box {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 1rem;
            min-height: 120px;
            background-color: #f8f9fa;
        }
         .ui-autocomplete {
            z-index: 1055 !important;
        }
        
        /* Custom styles for product autocomplete */
        .ui-autocomplete.product-autocomplete-list li {
            padding: 5px;
        }
        .autocomplete-item {
            display: flex;
            align-items: center;
        }
        .autocomplete-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 10px;
            border: 1px solid #eee;
        }
        .autocomplete-label {
            font-size: 0.9rem;
            font-weight: 500;
            line-height: 1.2;
        }
    </style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">New Invoice Generate</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Invoice</li>
                </ol>
            </nav>
        </div>
        <form action="{{ route('order.store') }}" method="POST">
            @csrf
            <div class="row">
                {{-- Left Side: Client Info --}}
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Client Information</h5>
                        </div>
                        <div class="card-body">
                             <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Search Client (Name/Phone)*</label>
                                    <div class="input-group">
                                        <input type="text" id="customerSearch" class="form-control" placeholder="Start typing to search...">
                                        <button class="btn btn-outline-success" type="button" id="openQuickCustomerModal" title="Add New Customer" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                                            <i class="fa fa-user-plus"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="customer_id" id="customerId">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Shipping Address</label>
                                    <div class="input-group">
                                        <select name="shipping_address" id="shippingAddressSelect" class="form-select">
                                            <option value="">Choose...</option>
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#newAddressModal">Add New</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="address-box border-primary">
                                        <h6 class="text-primary"><i class="fa fa-home me-2"></i>Client Home Address</h6>
                                        <textarea name="home_address" id="clientHomeAddress" class="form-control bg-transparent border-0" rows="3" placeholder="Client Home Address"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="address-box border-success">
                                        <h6 class="text-success"><i class="fa fa-shipping-fast me-2"></i>Shipping Address</h6>
                                        <textarea name="shipping_address_text" id="clientShippingAddress" class="form-control bg-transparent border-0" rows="3" placeholder="Client Shipping Address"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Invoice Details --}}
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">Invoice</h5>
                            <div class="mb-3">
                                <label class="form-label">Invoice #*</label>
                                <input type="text" name="invoice_no" class="form-control" value="{{ $newInvoiceId }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Term*</label>
                                <select name="payment_term" class="form-select" required>
                                    <option value="cod">Cash on Delivery</option>
                                    <option value="online">Online Payment</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date*</label>
                                <input type="text" id="orderDate" name="order_date" class="form-control" value="{{ date('d-m-Y') }}" readonly style="background-color: #fff; cursor: pointer;">
                            </div>
                             <div class="mb-3">
                                <label class="form-label">Warehouse*</label>
                                <select name="warehouse" class="form-select" required>
                                    <option>SpotLightAttires</option>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label class="form-label">Order Form*</label>
                                <select name="order_from" class="form-select" required>
                                     <option value="admin">Admin</option>
                                    <option value="web">Web</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Full Width: Product Information --}}
             <div class="row mt-0">
                <div class="col-lg-12">
                     <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Product Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table product-information-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">Product Name*</th>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th style="width: 80px;">Qty*</th>
                                            <th style="width: 120px;">Rate</th>
                                            <th>Amount</th>
                                            <th style="width: 120px;">Discount</th>
                                            <th>After Dis.</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-rows-container">
                                        {{-- Product rows will be added here --}}
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="addNewProductBtn" class="btn btn-primary btn-sm"><i class="fa fa-plus me-1"></i> Add New Product</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Section: Notes & Summary --}}
            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <label class="form-label">Note</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                     <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <table class="table summary-table">
                                <tbody>
                                   <tr>
                                        <td>Net Price</td>
                                        <td><input type="text" id="netPrice" name="subtotal" class="form-control" readonly></td>
                                    </tr>
                                    {{-- START: MODIFIED TOTAL DISCOUNT ROW --}}
                                    <tr>
                                        <td>
                                            Total Discount
                                            <div class="mt-1">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input discount-type" type="radio" name="discount_type" id="discountFixed" value="fixed" checked>
                                                    <label class="form-check-label" for="discountFixed">Fixed</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input discount-type" type="radio" name="discount_type" id="discountPercent" value="percent">
                                                    <label class="form-check-label" for="discountPercent">%</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{-- User inputs value here (e.g. 10 for 10% or 50 for 50tk) --}}
                                            <input type="number" id="discountValue" name="discount_value" class="form-control" value="0" step="0.01">
                                            
                                            {{-- Hidden field stores the CALCULATED amount for DB 'discount' column --}}
                                            <input type="hidden" id="totalDiscount" name="discount" value="0">
                                            
                                            {{-- Display calculated amount --}}
                                            <small id="calculatedDiscountText" class="text-success fw-bold d-block mt-1"></small>
                                        </td>
                                    </tr>
                                    {{-- END: MODIFIED TOTAL DISCOUNT ROW --}}
                                    <tr>
                                        <td>Delivery Charge</td>
                                        <td><input type="number" id="deliveryCharge" name="shipping_cost" class="form-control" value="0" step="0.01"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Grand Total</strong></td>
                                        <td><input type="text" id="grandTotal" name="total_amount" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <td>Total Pay</td>
                                        <td><input type="number" id="totalPay" name="total_pay" class="form-control" value="0" step="0.01"></td>
                                    </tr>
                                    <tr>
                                        <td>COD</td>
                                        <td><input type="text" id="cod" name="cod" class="form-control" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="total-due d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Total Due</h5>
                                    <span id="totalDueText">0 Taka</span>
                                </div>
                                <button type="submit" class="btn btn-light"><i class="fa fa-shopping-cart me-1"></i> Checkout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<div class="modal fade" id="newAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Shipping Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="newAddressText" class="form-label">Full Address</label>
                    <textarea id="newAddressText" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="newAddressType" class="form-label">Address Type</label>
                    <select id="newAddressType" class="form-select">
                        <option value="Home">Home</option>
                        <option value="Office">Office</option>
                        <option value="Others">Other</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveNewAddressBtn" class="btn btn-primary">Add Address</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-labelledby="quickCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickCustomerForm">
                    <div id="quickCustomerErrors" class="alert alert-danger" style="display: none;">
                        <ul class="mb-0"></ul>
                    </div>

                    <div class="mb-3">
                        <label for="quick_name" class="form-label">Customer Name*</label>
                        <input type="text" class="form-control" id="quick_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quick_phone" class="form-label">Mobile Number* (11 Digits)</label>
                        <input type="number" class="form-control" id="quick_phone" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, ''); if (this.value.length > 11) this.value = this.value.slice(0, 11);" 
                               pattern="[0-9]{11}" title="Please enter an 11-digit mobile number" required>
                    </div>
                    <div class="mb-3">
                        <label for="quick_secondary_phone" class="form-label">Secondary Phone (Optional)</label>
                        <input type="number" class="form-control" id="quick_secondary_phone" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, ''); if (this.value.length > 11) this.value = this.value.slice(0, 11);" 
                               pattern="[0-9]{11}" title="Please enter an 11-digit mobile number">
                    </div>
                    <div class="mb-3">
                        <label for="quick_address" class="form-label">Address* (Home & Default)</label>
                        <textarea class="form-control" id="quick_address" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveQuickCustomerBtn" class="btn btn-primary">Save Customer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
$(document).ready(function() {
    // Initialize datepicker
    $("#orderDate").datepicker({
        dateFormat: 'dd-mm-yy'
    });

    var newAddressModal = new bootstrap.Modal(document.getElementById('newAddressModal'));
    
    // Global variable to store current customer's discount %
    let customerDiscountPercent = 0;

    // --- CUSTOMER LOGIC ---

    function populateCustomerAddresses(customerId, shippingAddressSelect) {
        $.get(`{{ url('order-get-customer-details') }}/${customerId}`, function(data) {
            // Set home address
            let homeAddress = data.main_address;
            if (!homeAddress && data.addresses.length > 0) {
                homeAddress = data.addresses[0].address;
            }
            $('#clientHomeAddress').val(homeAddress || '');

            // Populate shipping select
            shippingAddressSelect.empty().append('<option value="">Choose...</option>');
            
            // Group 1: Existing Saved Addresses
            if (data.addresses.length > 0) {
                shippingAddressSelect.append('<optgroup label="Saved Addresses">');
                data.addresses.forEach(addr => {
                    shippingAddressSelect.append(`<option value="${addr.address}" data-type="${addr.address_type}">${addr.address} (${addr.address_type})</option>`);
                });
                shippingAddressSelect.append('</optgroup>');
            }

            // Group 2: Add New Address options
            shippingAddressSelect.append('<optgroup label="Add New Address">');
            shippingAddressSelect.append('<option value="add_new_home" data-type-to-add="Home">Add New Home Address...</option>');
            shippingAddressSelect.append('<option value="add_new_office" data-type-to-add="Office">Add New Office Address...</option>');
            shippingAddressSelect.append('<option value="add_new_others" data-type-to-add="Others">Add New Other Address...</option>');
            shippingAddressSelect.append('</optgroup>');
        });
    }

    // 1. Initialize customer autocomplete
    $("#customerSearch").autocomplete({
        source: "{{ route('order.search-customers') }}",
        minLength: 0,
        select: function(event, ui) {
            const customer = ui.item;
            const itemType = customer.type ? ` (${customer.type})` : '';
            $('#customerSearch').val(`${customer.name} - ${customer.phone}${itemType}`);
            $('#customerId').val(customer.id);
            
            // --- START: CUSTOMER DISCOUNT LOGIC ---
            // Get discount percent from the customer object (ensure your controller returns this field)
            customerDiscountPercent = parseFloat(customer.discount_in_percent) || 0;

            if (customerDiscountPercent > 0) {
                // If discount exists, set Type to '%' and Value to the percent amount
                $('#discountPercent').prop('checked', true);
                $('#discountValue').val(customerDiscountPercent);
            } else {
                // Default to Fixed 0
                $('#discountFixed').prop('checked', true);
                $('#discountValue').val(0);
            }

            // Recalculate totals immediately
            calculateFinalTotals();
            // --- END: CUSTOMER DISCOUNT LOGIC ---

            populateCustomerAddresses(customer.id, $('#shippingAddressSelect'));

            return false;
        }
    }).on('focus', function() {
        $(this).autocomplete("search", "");
    });

    // 2. Customize the renderer for Platinum color
    $("#customerSearch").data("ui-autocomplete")._renderItem = function(ul, item) {
        // Check if type is 'platinum' (case-insensitive), apply red color
        const typeClass = (item.type && item.type.toLowerCase() === 'platinum') ? 'text-danger fw-bold' : 'text-success';
        const itemType = item.type ? ` <span class="${typeClass}">(${item.type})</span>` : '';
        
        return $("<li>").append(`<div>${item.name} - ${item.phone}${itemType}</div>`).appendTo(ul);
    };

    // 3. Handle selection from the shipping address dropdown
    $('#shippingAddressSelect').on('change', function() {
        const selected = $(this).find('option:selected');
        const value = $(this).val();
        const typeToAdd = selected.data('type-to-add');

        if (typeToAdd) {
            $('#newAddressType').val(typeToAdd);
            $('#newAddressText').val(''); 
            newAddressModal.show();
            $(this).val('');
        } else {
            $('#clientShippingAddress').val(value);
        }
    });

    // 4. Handle saving the new address from the modal
    $('#saveNewAddressBtn').on('click', function() {
        const newAddress = $('#newAddressText').val();
        const newType = $('#newAddressType').val();
        const shippingAddressSelect = $('#shippingAddressSelect');
        
        if (newAddress) {
            const displayText = `${newAddress} (${newType})`;
            let optgroup = shippingAddressSelect.find('optgroup[label="Saved Addresses"]');
            if (optgroup.length === 0) {
                shippingAddressSelect.prepend('<optgroup label="Saved Addresses"></optgroup>');
                optgroup = shippingAddressSelect.find('optgroup[label="Saved Addresses"]');
            }
            const newOption = $(`<option value="${newAddress}" data-type="${newType}">${displayText}</option>`);
            optgroup.append(newOption);
            newOption.prop('selected', true);
            $('#clientShippingAddress').val(newAddress);
            $('#newAddressText').val('');
            $('#newAddressType').val('Home'); 
            newAddressModal.hide();
        }
    });

    // --- QUICK CUSTOMER MODAL LOGIC ---
    var quickCustomerModal = new bootstrap.Modal(document.getElementById('quickCustomerModal'));
    var quickCustomerErrors = $('#quickCustomerErrors');
    var quickCustomerErrorList = quickCustomerErrors.find('ul');

    $('#saveQuickCustomerBtn').on('click', function(e) {
        e.preventDefault();
        quickCustomerErrors.hide();
        quickCustomerErrorList.empty();

        const name = $('#quick_name').val();
        const phone = $('#quick_phone').val();
        const secondary_phone = $('#quick_secondary_phone').val();
        const address = $('#quick_address').val();

        $(this).prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('order.customer.quick-store') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { name, phone, secondary_phone, address },
            success: function(response) {
                $('#customerId').val(response.customer.id);
                const itemType = response.customer.type ? ` (${response.customer.type})` : '';
                $('#customerSearch').val(response.customer.name + ' - ' + response.customer.phone + itemType);
                
                // Set default discount for new customer (usually 0)
                $('#discountFixed').prop('checked', true);
                $('#discountValue').val(0);
                calculateFinalTotals();

                $('#clientHomeAddress').val(response.address.address);
                $('#clientShippingAddress').val(response.address.address);

                const shippingAddressSelect = $('#shippingAddressSelect');
                shippingAddressSelect.empty();
                shippingAddressSelect.append('<option value="">Choose...</option>');

                const savedOptgroup = $('<optgroup label="Saved Addresses"></optgroup>');
                const newAddressOption = $(`<option value="${response.address.address}" data-type="Home">${response.address.address} (Home)</option>`);
                savedOptgroup.append(newAddressOption);
                shippingAddressSelect.append(savedOptgroup);

                const addNewOptgroup = $('<optgroup label="Add New Address"></optgroup>');
                addNewOptgroup.append('<option value="add_new_home" data-type-to-add="Home">Add New Home Address...</option>');
                addNewOptgroup.append('<option value="add_new_office" data-type-to-add="Office">Add New Office Address...</option>');
                addNewOptgroup.append('<option value="add_new_others" data-type-to-add="Others">Add New Other Address...</option>');
                shippingAddressSelect.append(addNewOptgroup);
                
                shippingAddressSelect.val(response.address.address); 
                
                if(window.toastr) toastr.success(response.message);
                
                $('#quickCustomerForm')[0].reset();
                quickCustomerModal.hide();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        quickCustomerErrorList.append('<li>' + value[0] + '</li>');
                    });
                    quickCustomerErrors.show();
                } else {
                    quickCustomerErrorList.append('<li>An unexpected error occurred. Please try again.</li>');
                    quickCustomerErrors.show();
                }
            },
            complete: function() {
                $('#saveQuickCustomerBtn').prop('disabled', false).text('Save Customer');
            }
        });
    });

    // --- PRODUCT ROW & CALCULATION LOGIC ---
    let productRowIndex = 0;
    let productsCache = {};

    function addProductRow() {
        const rowHtml = `
            <tr class="product-row" data-index="${productRowIndex}">
                <td>
                    <input type="text" class="form-control product-search" placeholder="Search product...">
                    <input type="hidden" name="items[${productRowIndex}][product_id]">
                </td>
                <td><select class="form-select color-select" name="items[${productRowIndex}][color]"></select></td>
                <td><select class="form-select size-select" name="items[${productRowIndex}][size]"></select></td>
                <td><input type="number" name="items[${productRowIndex}][quantity]" class="form-control quantity" value="1" min="1"></td>
                <td><input type="number" name="items[${productRowIndex}][unit_price]" class="form-control unit-price" step="0.01" readonly></td>
                <td><input type="text" class="form-control amount" readonly></td>
                <td><input type="number" name="items[${productRowIndex}][discount]" class="form-control discount" value="0" step="0.01"></td>
                <td><input type="text" class="form-control after-discount" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-product-btn">&times;</button></td>
            </tr>
        `;
        $('#product-rows-container').append(rowHtml);
        initializeProductSearch($(`.product-row[data-index=${productRowIndex}] .product-search`));
        productRowIndex++;
    }

    function initializeProductSearch(element) {
        $(element).autocomplete({
            source: "{{ route('order.search-products') }}",
            minLength: 1,
            select: function(event, ui) {
                const row = $(this).closest('tr');
                const productId = ui.item.id;
                row.find('input[name$="[product_id]"]').val(productId);
                $(this).val(ui.item.value); 

                if (productsCache[productId]) {
                    populateVariations(row, productsCache[productId]);
                } else {
                    $.get(`{{ url('order-get-product-details') }}/${productId}`, function(data) {
                        productsCache[productId] = data;
                        populateVariations(row, data);
                    });
                }
                return false; 
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            ul.addClass('product-autocomplete-list'); 
            return $("<li>")
                .append(`
                    <div class="autocomplete-item">
                        <img src="${item.image_url}" alt="Product" class="autocomplete-image">
                        <span class="autocomplete-label">${item.label}</span>
                    </div>
                `)
                .appendTo(ul);
        };
    }

    function populateVariations(row, productData) {
        const colorSelect = row.find('.color-select');
        colorSelect.empty().append('<option value="">Select Color</option>');
        if (productData.variants) {
            productData.variants.forEach(variant => {
                colorSelect.append(`<option value="${variant.color_name}">${variant.color_name}</option>`);
            });
        }
        colorSelect.trigger('change');
    }

    $('#product-rows-container').on('change', '.color-select', function() {
        const row = $(this).closest('tr');
        const productId = row.find('input[name$="[product_id]"]').val();
        const selectedColor = $(this).val();
        const productData = productsCache[productId];
        const sizeSelect = row.find('.size-select');
        sizeSelect.empty().append('<option value="">Select Size</option>');

        const variant = productData.variants.find(v => v.color_name === selectedColor);
        if (variant && variant.sizes) {
            variant.sizes.forEach(size => {
                sizeSelect.append(`<option value="${size.name}" data-price="${size.additional_price || 0}" data-quantity="${size.quantity || 0}">${size.name} (${size.quantity} pcs)</option>`);
            });
        }
        sizeSelect.trigger('change');
    });

    $('#product-rows-container').on('change', '.size-select', function() {
        const row = $(this).closest('tr');
        const quantityInput = row.find('.quantity');
        
        if ($(this).val()) {
            const selectedOption = $(this).find('option:selected');
            const productId = row.find('input[name$="[product_id]"]').val();
            const productData = productsCache[productId];
            const additionalPrice = parseFloat(selectedOption.data('price')) || 0;
            const availableQuantity = parseInt(selectedOption.data('quantity')) || 0;
            const basePrice = parseFloat(productData.base_price);

            row.find('.unit-price').val((basePrice + additionalPrice).toFixed(2));
            quantityInput.attr('max', availableQuantity);
            quantityInput.attr('placeholder', `Max: ${availableQuantity}`);
            
            if (parseInt(quantityInput.val()) > availableQuantity) {
                quantityInput.val(1);
            }
        } else {
            row.find('.unit-price').val('');
            quantityInput.removeAttr('max').attr('placeholder', '');
        }
        quantityInput.trigger('input'); 
    });

    // --- REVISED CALCULATION FUNCTION ---
    // --- REVISED CALCULATION FUNCTION WITH ROUNDING UP ---
    function calculateFinalTotals() {
        let netPrice = 0;
        
        // 1. Calculate Net Price from product rows
        $('.product-row').each(function() {
            const row = $(this);
            const quantity = parseFloat(row.find('.quantity').val()) || 0;
            const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
            const itemDiscount = parseFloat(row.find('.discount').val()) || 0; 
            
            const amount = quantity * unitPrice;
            const afterDiscount = amount - itemDiscount;
            
            row.find('.amount').val(amount.toFixed(2));
            row.find('.after-discount').val(afterDiscount.toFixed(2));
            netPrice += amount;
        });

        // 2. Calculate Total Discount (Order Level)
        const discountType = $('input[name="discount_type"]:checked').val(); 
        const discountValue = parseFloat($('#discountValue').val()) || 0;
        let finalTotalDiscountAmt = 0;

        if (discountType === 'percent') {
            finalTotalDiscountAmt = (netPrice * discountValue) / 100;
            $('#calculatedDiscountText').text(`(${discountValue}% = ${finalTotalDiscountAmt.toFixed(2)} Tk)`);
        } else {
            finalTotalDiscountAmt = discountValue;
            $('#calculatedDiscountText').text('');
        }

        $('#totalDiscount').val(finalTotalDiscountAmt.toFixed(2));

        // 3. Final Calculations
        const deliveryCharge = parseFloat($('#deliveryCharge').val()) || 0;
        const totalPay = parseFloat($('#totalPay').val()) || 0;
        
        // Calculate raw grand total (with decimals)
        let rawGrandTotal = netPrice - finalTotalDiscountAmt + deliveryCharge;
        
        // --- NEW LOGIC: Round UP to next integer (Math.ceil) ---
        // Example: 558.1 -> 559, 558.6 -> 559
        const grandTotal = Math.ceil(rawGrandTotal);
        
        const cod = grandTotal - totalPay;

        $('#netPrice').val(netPrice.toFixed(2));
        
        // শো করানোর সময় পূর্ণ সংখ্যা (Integer) দেখানো হচ্ছে
        $('#grandTotal').val(grandTotal); 
        $('#cod').val(cod);
        $('#totalDueText').text(`${cod} Taka`);
    }

    // Trigger calculation on input/change of relevant fields
    $('#product-rows-container').on('input', '.quantity, .unit-price, .discount', calculateFinalTotals);
    $('#deliveryCharge, #totalPay').on('input', calculateFinalTotals);
    // New triggers for discount type and value
    $('#discountValue, input[name="discount_type"]').on('input change', calculateFinalTotals);

    $('#addNewProductBtn').on('click', addProductRow);
    $('#product-rows-container').on('click', '.remove-product-btn', function() { 
        $(this).closest('tr').remove(); 
        calculateFinalTotals();
    });

    addProductRow();
});
</script>
@endsection