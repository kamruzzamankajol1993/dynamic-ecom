@extends('admin.master.master')
@section('title', 'Edit Order')

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
            <h2 class="mb-0">Edit Invoice</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('order.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        
        <form action="{{ route('order.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT') 

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
                                        <input type="text" id="customerSearch" class="form-control" 
                                               placeholder="Start typing to search..." 
                                               value="{{ $order->customer ? $order->customer->name . ' - ' . $order->customer->phone : '' }}">
                                               
                                        <button class="btn btn-outline-success" type="button" id="openQuickCustomerModal" title="Add New Customer" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                                            <i class="fa fa-user-plus"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="customer_id" id="customerId" value="{{ $order->customer ? $order->customer_id : '' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Shipping Address</label>
                                    <div class="input-group">
                                        <select name="shipping_address_select" id="shippingAddressSelect" class="form-select">
                                            <option value="">Choose...</option>
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#newAddressModal">Add New</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="address-box border-primary">
                                        <h6 class="text-primary"><i class="fa fa-home me-2"></i>Client Home Address</h6>
                                        <textarea name="home_address" id="clientHomeAddress" class="form-control bg-transparent border-0" rows="3" placeholder="Client Home Address">{{ $order->customer->address ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="address-box border-success">
                                        <h6 class="text-success"><i class="fa fa-shipping-fast me-2"></i>Shipping Address</h6>
                                        <textarea name="shipping_address" id="clientShippingAddress" class="form-control bg-transparent border-0" rows="3" placeholder="Client Shipping Address">{{ $order->shipping_address }}</textarea>
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
                                <input type="text" name="invoice_no" class="form-control" value="{{ $order->invoice_no }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Term*</label>
                                <select name="payment_term" class="form-select" required>
                                    <option value="cod" {{ $order->payment_term == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                                    <option value="online" {{ $order->payment_term == 'online' ? 'selected' : '' }}>Online Payment</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date*</label>
                                <input type="text" id="orderDate" name="order_date" class="form-control" value="{{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}" readonly style="background-color: #fff; cursor: pointer;">
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
                                     <option value="admin" {{ $order->order_from == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="web" {{ $order->order_from == 'web' ? 'selected' : '' }}>Web</option>
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
                                        @foreach($order->orderDetails as $index => $detail)
                                        <tr class="product-row" 
                                            data-index="{{ $index }}"
                                            data-product-id="{{ $detail->product_id }}"
                                            data-selected-color="{{ $detail->color }}"
                                            data-selected-size="{{ $detail->size }}">
                                            <td>
                                                <input type="text" class="form-control product-search" placeholder="Search product..." value="{{ $detail->product->name ?? 'N/A' }}">
                                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $detail->product_id }}">
                                            </td>
                                            <td><select class="form-select color-select" name="items[{{ $index }}][color]"></select></td>
                                            <td><select class="form-select size-select" name="items[{{ $index }}][size]"></select></td>
                                            <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" value="{{ $detail->quantity }}" min="1"></td>
                                            <td><input type="number" name="items[{{ $index }}][unit_price]" class="form-control unit-price" value="{{ $detail->unit_price }}" step="0.01"></td>
                                            <td><input type="text" class="form-control amount" readonly></td>
                                            <td><input type="number" name="items[{{ $index }}][discount]" class="form-control discount" value="{{ $detail->discount ?? 0 }}" step="0.01"></td>
                                            <td><input type="text" class="form-control after-discount" readonly></td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-product-btn">&times;</button></td>
                                        </tr>
                                        @endforeach
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
                            <textarea name="notes" class="form-control" rows="3">{{ $order->notes }}</textarea>
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
                                        <td><input type="text" id="netPrice" name="subtotal" class="form-control" value="{{ $order->subtotal }}" readonly></td>
                                    </tr>
                                    {{-- Discount Section --}}
                                    <tr>
                                        <td>
                                            Total Discount
                                            <div class="mt-1">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input discount-type" type="radio" name="discount_type" id="discountFixed" value="fixed" 
                                                        {{ $order->discount_type == 'fixed' || !$order->discount_type ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="discountFixed">Fixed</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input discount-type" type="radio" name="discount_type" id="discountPercent" value="percent"
                                                        {{ $order->discount_type == 'percent' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="discountPercent">%</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" id="discountValue" name="discount_value" class="form-control" 
                                                   value="{{ $order->discount_value ?? 0 }}" step="0.01">
                                            <input type="hidden" id="totalDiscount" name="discount" value="{{ $order->discount ?? 0 }}">
                                            <small id="calculatedDiscountText" class="text-success fw-bold d-block mt-1"></small>
                                        </td>
                                    </tr>
                                    
                                    {{-- START: REWARD POINT DISCOUNT DISPLAY --}}
                                    {{-- এই সেকশনটি নতুন যোগ করা হয়েছে --}}
                                    @if(isset($order->reward_point_discount) && $order->reward_point_discount > 0)
                                    <tr>
                                        <td class="text-warning fw-bold">Reward Discount</td>
                                        <td>
                                            <input type="text" class="form-control text-warning fw-bold" value="{{ $order->reward_point_discount }}" readonly>
                                            {{-- Hidden field for calculation in JS --}}
                                            <input type="hidden" id="rewardPointDiscount" value="{{ $order->reward_point_discount }}">
                                        </td>
                                    </tr>
                                    @else
                                        {{-- If no reward discount, value is 0 --}}
                                        <input type="hidden" id="rewardPointDiscount" value="0">
                                    @endif
                                    {{-- END: REWARD POINT DISCOUNT DISPLAY --}}

                                    <tr>
                                        <td>Delivery Charge</td>
                                        <td><input type="number" id="deliveryCharge" name="shipping_cost" class="form-control" value="{{ $order->shipping_cost ?? 0 }}" step="0.01"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Grand Total</strong></td>
                                        <td><input type="text" id="grandTotal" name="total_amount" class="form-control" value="{{ $order->total_amount }}" readonly></td>
                                    </tr>
                                    <tr>
                                        <td>Total Pay</td>
                                        <td><input type="number" id="totalPay" name="total_pay" class="form-control" value="{{ $order->total_pay ?? 0 }}" step="0.01"></td>
                                    </tr>
                                    <tr>
                                        <td>COD</td>
                                        <td><input type="text" id="cod" name="cod" class="form-control" value="{{ $order->cod }}" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="total-due d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Total Due</h5>
                                    <span id="totalDueText">{{ $order->due }} Taka</span>
                                </div>
                                <button type="submit" class="btn btn-light"><i class="fa fa-shopping-cart me-1"></i> Update & Checkout</button>
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
    let initialProductDetails = @json($productDetailsJs);
    let productsCache = initialProductDetails;

    // Initialize the date picker
    $("#orderDate").datepicker({
        dateFormat: 'dd-mm-yy'
    });

    var newAddressModal = new bootstrap.Modal(document.getElementById('newAddressModal'));

    // --- Customer Address Logic ---
    function populateCustomerAddresses(customerId, shippingAddressSelect) {
        $.get(`{{ url('order-get-customer-details') }}/${customerId}`, function(data) {
            // Set home address
            let homeAddress = data.main_address;
            if (!homeAddress && data.addresses.length > 0) {
                homeAddress = data.addresses[0].address;
            }
            if ($('#clientHomeAddress').val() === '') {
                 $('#clientHomeAddress').val(homeAddress || '');
            }

            shippingAddressSelect.empty().append('<option value="">Choose...</option>');
            
            if (data.addresses.length > 0) {
                shippingAddressSelect.append('<optgroup label="Saved Addresses">');
                data.addresses.forEach(addr => {
                    shippingAddressSelect.append(`<option value="${addr.address}" data-type="${addr.address_type}">${addr.address} (${addr.address_type})</option>`);
                });
                shippingAddressSelect.append('</optgroup>');
            }

            shippingAddressSelect.append('<optgroup label="Add New Address">');
            shippingAddressSelect.append('<option value="add_new_home" data-type-to-add="Home">Add New Home Address...</option>');
            shippingAddressSelect.append('<option value="add_new_office" data-type-to-add="Office">Add New Office Address...</option>');
            shippingAddressSelect.append('<option value="add_new_others" data-type-to-add="Others">Add New Other Address...</option>');
            shippingAddressSelect.append('</optgroup>');

            const savedShippingAddress = `{{ $order->shipping_address ?? '' }}`;
            if (savedShippingAddress) {
                if (shippingAddressSelect.find(`option[value="${savedShippingAddress}"]`).length > 0) {
                    shippingAddressSelect.val(savedShippingAddress);
                } else {
                    shippingAddressSelect.val('');
                }
            }
        });
    }

    // Initialize existing customer data
    const initialCustomerId = $('#customerId').val();
    if (initialCustomerId) {
        populateCustomerAddresses(initialCustomerId, $('#shippingAddressSelect'));
    }

    // --- Customer Search & Discount Logic ---
    $("#customerSearch").autocomplete({
        source: "{{ route('order.search-customers') }}",
        minLength: 0,
        select: function(event, ui) {
            const customer = ui.item;
            const itemType = customer.type ? ` (${customer.type})` : '';
            $('#customerSearch').val(`${customer.name} - ${customer.phone}${itemType}`);
            $('#customerId').val(customer.id);
            
            // --- NEW: AUTO DISCOUNT LOGIC ---
            let customerDiscountPercent = parseFloat(customer.discount_in_percent) || 0;
            if (customerDiscountPercent > 0) {
                $('#discountPercent').prop('checked', true);
                $('#discountValue').val(customerDiscountPercent);
            } else {
                $('#discountFixed').prop('checked', true);
                $('#discountValue').val(0);
            }
            calculateFinalTotals();
            // ---------------------------------

            populateCustomerAddresses(customer.id, $('#shippingAddressSelect'));
            return false;
        }
    }).on('focus', function() {
        $(this).autocomplete("search", "");
    });

    // --- NEW: Custom Renderer for Platinum Red Color ---
    $("#customerSearch").data("ui-autocomplete")._renderItem = function(ul, item) {
        const typeClass = (item.type && item.type.toLowerCase() === 'platinum') ? 'text-danger fw-bold' : 'text-success';
        const itemType = item.type ? ` <span class="${typeClass}">(${item.type})</span>` : '';
        return $("<li>").append(`<div>${item.name} - ${item.phone}${itemType}</div>`).appendTo(ul);
    };

    // Address Dropdown Change Logic
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

    // Save New Address Modal
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

    // --- Quick Customer Modal Logic ---
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

                // Default discount for new customer
                $('#discountFixed').prop('checked', true);
                $('#discountValue').val(0);
                calculateFinalTotals();

                $('#clientHomeAddress').val(response.address.address);
                $('#clientShippingAddress').val(response.address.address);

                const shippingAddressSelect = $('#shippingAddressSelect');
                shippingAddressSelect.empty().append('<option value="">Choose...</option>');

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

    // --- Product Logic ---
    let productRowIndex = {{ $order->orderDetails->count() }}; 

    function initializeExistingRows() {
        $('#product-rows-container .product-row').each(function() {
            const row = $(this);
            const productId = row.data('product-id');
            const selectedColor = row.data('selected-color');
            const selectedSize = row.data('selected-size');
            
            if (productId && productsCache[productId]) {
                const productData = productsCache[productId];
                const colorSelect = row.find('.color-select');
                
                colorSelect.empty().append('<option value="">Select Color</option>');
                if (productData.variants) {
                    productData.variants.forEach(variant => {
                        const isSelected = variant.color_name === selectedColor ? 'selected' : '';
                        colorSelect.append(`<option value="${variant.color_name}" ${isSelected}>${variant.color_name}</option>`);
                    });
                }

                const sizeSelect = row.find('.size-select');
                sizeSelect.empty().append('<option value="">Select Size</option>');
                
                const selectedVariant = productData.variants.find(v => v.color_name === selectedColor);
                if (selectedVariant && selectedVariant.sizes) {
                    selectedVariant.sizes.forEach(size => {
                        const isSelected = size.name === selectedSize ? 'selected' : '';
                        sizeSelect.append(`<option value="${size.name}" data-price="${size.additional_price || 0}" data-quantity="${size.quantity || 0}" ${isSelected}>${size.name} (${size.quantity} pcs)</option>`);
                    });
                }

                const selectedSizeOption = sizeSelect.find('option:selected');
                if (selectedSizeOption.length) {
                    const availableQuantity = parseInt(selectedSizeOption.data('quantity')) || 0;
                    const quantityInput = row.find('.quantity');
                    quantityInput.attr('max', availableQuantity);
                    quantityInput.attr('placeholder', `Max: ${availableQuantity}`);
                }
            }
        });
    }

    initializeExistingRows();
    $('.product-search').each(function() {
        initializeProductSearch(this);
    });
    calculateFinalTotals(); // Initial Calculation

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

   // --- UPDATED CALCULATION LOGIC WITH REWARD DISCOUNT ---
   function calculateFinalTotals() {
        let netPrice = 0;
        
        // 1. Calculate Product Totals
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

        // 2. Calculate Total Manual/Coupon Discount
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

        // 3. Get Reward Point Discount
        const rewardDiscount = parseFloat($('#rewardPointDiscount').val()) || 0;

        // 4. Final Sum
        const deliveryCharge = parseFloat($('#deliveryCharge').val()) || 0;
        const totalPay = parseFloat($('#totalPay').val()) || 0;
        
        // Calculate raw grand total
        // Formula: Net Price - Manual Discount - Reward Discount + Delivery
        let rawGrandTotal = netPrice - finalTotalDiscountAmt - rewardDiscount + deliveryCharge;
        
        // Round UP to next integer
        const grandTotal = Math.ceil(rawGrandTotal);

        const cod = grandTotal - totalPay;

        $('#netPrice').val(netPrice.toFixed(2));
        $('#grandTotal').val(grandTotal);
        $('#cod').val(cod);
        $('#totalDueText').text(`${cod} Taka`);
    }

    // --- EVENT LISTENERS FOR CALCULATION ---
    $('#product-rows-container').on('input', '.quantity, .unit-price, .discount', calculateFinalTotals);
    $('#deliveryCharge, #totalPay').on('input', calculateFinalTotals);
    // New listener for discount fields
    $('#discountValue, input[name="discount_type"]').on('input change', calculateFinalTotals);

    $('#addNewProductBtn').on('click', addProductRow);
    $('#product-rows-container').on('click', '.remove-product-btn', function() { 
        $(this).closest('tr').remove(); 
        calculateFinalTotals();
    });

});
</script>
@endsection