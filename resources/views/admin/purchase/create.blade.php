@extends('admin.master.master')
@section('title', 'Create Purchase')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Create New Purchase</h2>
        </div>
        <form action="{{ route('purchase.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-control select2" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
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
                            <label class="form-label">Select Product</label>
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
                                {{-- Rows added via JS --}}
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
                                <span id="subtotal">৳0.00</span>
                                <input type="hidden" name="subtotal" id="subtotal-input" value="0">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Discount:</span>
                                <input type="number" name="discount" id="discount" class="form-control form-control-sm" style="width: 100px;" value="0">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Shipping Cost:</span>
                                <input type="number" name="shipping_cost" id="shipping_cost" class="form-control form-control-sm" style="width: 100px;" value="0">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold mb-2">
                                <span>Grand Total:</span>
                                <span id="grand_total">৳0.00</span>
                                <input type="hidden" name="total_amount" id="grand_total-input" value="0">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Paid Amount:</span>
                                <input type="number" name="paid_amount" id="paid_amount" class="form-control form-control-sm" style="width: 100px;" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save Purchase</button>
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
    let rowIndex = 0;

    $('#product-search').on('select2:select', function (e) {
    const productId = e.params.data.id;
    if (!productId) return;

    // 1. Generate a URL template with a placeholder using the route() helper
    let urlTemplate = "{{ route('stock.variants.get', ['product' => ':id']) }}";

    // 2. Replace the placeholder with the actual JavaScript productId
    let variantsUrl = urlTemplate.replace(':id', productId);

    if (productDataCache[productId]) {
        addProductRow(productDataCache[productId]);
    } else {
        // 3. Use the correctly generated URL in the AJAX call
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
                <td>
                    ${product.name}
                    <input type="hidden" name="products[${rowIndex}][product_id]" value="${product.id}">
                </td>
                <td>
                    <select class="form-control form-control-sm variant-select" name="products[${rowIndex}][variant_id]" required>${variantOptions}</select>
                </td>
                <td>
                    <select class="form-control form-control-sm size-select" name="products[${rowIndex}][size_id]" required></select>
                </td>
                <td><input type="number" class="form-control form-control-sm quantity" name="products[${rowIndex}][quantity]" value="1" min="1" required></td>
                <td><input type="number" class="form-control form-control-sm unit-cost" name="products[${rowIndex}][unit_cost]" value="0.00" min="0" required></td>
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
            subtotal += parseFloat($(this).text().replace('৳', '')) || 0;
        });
        
        const discount = parseFloat($('#discount').val()) || 0;
        const shipping = parseFloat($('#shipping_cost').val()) || 0;
        const grandTotal = (subtotal - discount) + shipping;

        $('#subtotal').text(`৳${subtotal.toFixed(2)}`);
        $('#subtotal-input').val(subtotal.toFixed(2));
        $('#grand_total').text(`৳${grandTotal.toFixed(2)}`);
        $('#grand_total-input').val(grandTotal.toFixed(2));
    }
});
</script>
@endsection