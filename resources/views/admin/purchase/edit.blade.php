@extends('admin.master.master')
@section('title', 'Edit Purchase')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Edit Purchase: {{ $purchase->purchase_no }}</h2>
        </div>
        <form action="{{ route('purchase.update', $purchase->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-control select2" required>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchase->supplier_id) == $supplier->id)>
                                    {{ $supplier->company_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Products</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Select Product to Add</label>
                            <select id="product-search" class="form-control select2">
                                <option value="">Search for a product</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Unit Cost</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-table-body">
                                @foreach($purchase->purchaseDetails as $index => $item)
                                <tr data-row-index="{{ $index }}">
                                    <td>
                                        {{ $item->product->name }}
                                        <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm variant-select" name="products[{{ $index }}][variant_id]" required>
                                            <option value="{{ $item->product_variant_id }}">{{ $item->productVariant->color->name }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm size-select" name="products[{{ $index }}][size_id]" required>
                                            <option value="{{ $item->size_id }}">{{ $item->size->name }}</option>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control form-control-sm quantity" name="products[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" required></td>
                                    <td><input type="number" class="form-control form-control-sm unit-cost" name="products[{{ $index }}][unit_cost]" value="{{ $item->unit_cost }}" min="0" required></td>
                                    <td class="total-cost">৳{{ number_format($item->total_cost, 2) }}</td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row-btn">&times;</button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">৳{{  $purchase->subtotal}}</span>
                                <input type="hidden" name="subtotal" id="subtotal-input" value="{{ old('subtotal', $purchase->subtotal) }}">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Discount:</span>
                                <input type="number" name="discount" id="discount" class="form-control form-control-sm" style="width: 100px;" value="{{ old('discount', $purchase->discount) }}">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Shipping Cost:</span>
                                <input type="number" name="shipping_cost" id="shipping_cost" class="form-control form-control-sm" style="width: 100px;" value="{{ old('shipping_cost', $purchase->shipping_cost) }}">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold mb-2">
                                <span>Grand Total:</span>
                                <span id="grand_total">৳{{ number_format(old('total_amount', $purchase->total_amount), 2) }}</span>
                                <input type="hidden" value="{{ old('total_amount', $purchase->total_amount) }}" name="total_amount" id="grand_total-input">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Paid Amount:</span>
                                <input type="number" name="paid_amount" id="paid_amount" class="form-control form-control-sm" style="width: 100px;" value="{{ old('paid_amount', $purchase->paid_amount) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control">{{ old('notes', $purchase->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Purchase</button>
            </div>
        </form>
    </div>
</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();
    let productDataCache = {};
    let rowIndex = {{ $purchase->purchaseDetails->count() }};

    // Pre-cache data for existing products using the named route
    @foreach($purchase->purchaseDetails as $item)
        @if($item->product)
            // MODIFIED: Using route() helper directly with the PHP variable
            $.get("{{ route('stock.variants.get', ['product' => $item->product_id]) }}", function(product) {
                productDataCache[{{ $item->product_id }}] = product;
            });
        @endif
    @endforeach

    $('#product-search').on('select2:select', function (e) {
        const productId = e.params.data.id;
        if (!productId) return;

        // MODIFIED: Using route() helper with a placeholder for the JS variable
        let urlTemplate = "{{ route('stock.variants.get', ['product' => ':id']) }}";
        let variantsUrl = urlTemplate.replace(':id', productId);

        if (productDataCache[productId]) {
            addProductRow(productDataCache[productId]);
        } else {
            $.ajax({
                url: variantsUrl,
                type: 'GET',
                success: function(product) {
                    productDataCache[productId] = product;
                    addProductRow(product);
                }
            });
        }
        $(this).val(null).trigger('change');
    });

    function addProductRow(product) {
        if (product.variants.length === 0) {
            alert('This product has no variants.');
            return;
        }

        let variantOptions = product.variants.map(v => `<option value="${v.id}">${v.color.name}</option>`).join('');
        
        const rowHtml = `
            <tr data-row-index="${rowIndex}">
                <td>${product.name}<input type="hidden" name="products[${rowIndex}][product_id]" value="${product.id}"></td>
                <td><select class="form-control form-control-sm variant-select" name="products[${rowIndex}][variant_id]" required>${variantOptions}</select></td>
                <td><select class="form-control form-control-sm size-select" name="products[${rowIndex}][size_id]" required></select></td>
                <td><input type="number" class="form-control form-control-sm quantity" name="products[${rowIndex}][quantity]" value="1" min="1" required></td>
                <td><input type="number" class="form-control form-control-sm unit-cost" name="products[${rowIndex}][unit_cost]" value="0.00" min="0" step="0.01" required></td>
                <td class="total-cost">৳0.00</td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row-btn">&times;</button></td>
            </tr>`;
        
        $('#purchase-table-body').append(rowHtml);
        $(`tr[data-row-index="${rowIndex}"] .variant-select`).trigger('change');
        rowIndex++;
    }

    $(document).on('change', '.variant-select', function() {
        const variantId = $(this).val();
        const row = $(this).closest('tr');
        const productId = row.find('input[name*="[product_id]"]').val();
        const sizeSelect = row.find('.size-select');
        
        const product = productDataCache[productId];
        if (!product) return;
        const variant = product.variants.find(v => v.id == variantId);
        
        let sizeOptions = variant.detailed_sizes.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        sizeSelect.html(sizeOptions);
    });

    $(document).on('input', '.quantity, .unit-cost', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const unitCost = parseFloat(row.find('.unit-cost').val()) || 0;
        const total = quantity * unitCost;
        row.find('.total-cost').text(`৳${total.toFixed(2)}`);
        updateTotals();
    });

    $(document).on('click', '.remove-row-btn', function() {
        $(this).closest('tr').remove();
        updateTotals();
    });

    $('#discount, #shipping_cost, #paid_amount').on('input', updateTotals);

     function updateTotals() {
    let subtotal = 0;
    $('.total-cost').each(function() {
        // This line is now corrected to remove both the currency symbol and commas
        const value = $(this).text().replace('৳', '').replace(/,/g, '');
        subtotal += parseFloat(value) || 0;
    });
    
    const discount = parseFloat($('#discount').val()) || 0;
    const shipping = parseFloat($('#shipping_cost').val()) || 0;
    const grandTotal = (subtotal - discount) + shipping;

    $('#subtotal').text(`৳${subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
    $('#subtotal-input').val(subtotal.toFixed(2));
    $('#grand_total').text(`৳${grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
    $('#grand_total-input').val(grandTotal.toFixed(2));
}

    // Initial calculation on page load
    updateTotals();
});
</script>
@endsection