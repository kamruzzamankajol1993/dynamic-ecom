@extends('admin.master.master')
@section('title', 'Generate Barcodes for POS')

@section('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .main-content { font-size: 0.9rem; }
        .form-label { font-size: 0.85rem; font-weight: 500; margin-bottom: 0.3rem; }
        .table th, .table td { padding: 0.6rem 0.5rem; vertical-align: middle; }
        #previewFrame { width: 100%; height: 500px; border: 1px solid #ddd; background: #f9f9f9; }
    </style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Generate & Print Barcodes</h2>

        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="productSearch" class="form-label">Search Product by Code/Name</label>
                    <input type="text" id="productSearch" class="form-control" placeholder="Start typing to search...">
                </div>
            </div>
        </div>

        <form id="printForm"> 
            @csrf
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Print Queue</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Variant (Color - Size)</th>
                                    <th style="width: 120px;">QTY</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="printQueueBody">
                                <tr id="noDataRow">
                                    <td colspan="4" class="text-center">No Data Available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Print Settings</h5>
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="paperSize" class="form-label">Paper Size*</label>
                           <select id="paperSize" name="paper_size" class="form-select" required>
    <option value="">Choose Paper Size</option>
    <option value="single-38-25" selected>Single Label (38mm x 25mm)</option> 
    
    <option value="a4-40">sheet (A4) (1.799" x 1.003")</option>
    <option value="a4-30">sheet (A4) (1" x 2.625")</option>
    <option value="a4-24">sheet (A4) (1.334" x 2.48")</option>
    <option value="thermal-label">Thermal Label (2x1 inch)</option>
    
    <option value="custom">Custom Size (Single Label)</option>
</select>
                        </div>
                        
                        <div class="col-md-9 d-flex flex-wrap align-items-center pt-3">
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" name="show_store_name" value="1" id="showStoreName" checked>
                                <label class="form-check-label" for="showStoreName">Store Name</label>
                            </div>
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" name="show_product_name" value="1" id="showProductName" checked>
                                <label class="form-check-label" for="showProductName">Product Name</label>
                            </div>
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" name="show_variant" value="1" id="showVariant" checked>
                                <label class="form-check-label" for="showVariant">Color & Size</label>
                            </div>
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" name="show_price" value="1" id="showPrice" checked>
                                <label class="form-check-label" for="showPrice">Price</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_border" value="1" id="showBorder">
                                <label class="form-check-label" for="showBorder">Border</label>
                            </div>
                        </div>
                    </div>

                    <div id="customSizeFields" class="row mt-3" style="display: none;">
                        <div class="col-md-3">
                            <label class="form-label">Width (mm)</label>
                            <input type="number" name="paper_width" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Height (mm)</label>
                            <input type="number" name="paper_height" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" id="previewBtn" class="btn btn-success">Preview Barcodes</button>
                        <button type="reset" id="resetBtn" class="btn btn-danger">Reset All</button>
                        <button type="button" id="printBtn" class="btn btn-primary">Direct Print</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card mt-4" id="previewCard" style="display:none;">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Print Preview</h5>
                <span class="badge bg-info text-dark">নিচের দিকে স্ক্রল করে প্রিভিউ দেখুন</span>
            </div>
            <div class="card-body">
                <iframe id="previewFrame"></iframe>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    function showLoading(message = 'Processing...') {
        Swal.fire({ title: message, allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
    }

    $("#productSearch").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('barcode.search') }}",
                data: { term: request.term },
                success: function(data) {
                    response(data.map(item => ({
                        label: `${item.name} (${item.product_code})`,
                        value: item.id,
                        product: item
                    })));
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            addProductToQueue(ui.item.product);
            $(this).val('');
            return false;
        }
    });

    function addProductToQueue(product) {
        let variantHtml = '<option value="" data-variant-id="" data-size-id="">Default (No Variation)</option>';
        if(product.variants && product.variants.length > 0) {
            product.variants.forEach(v => {
                if(v.sizes && v.sizes.length > 0) {
                    v.sizes.forEach(s => {
                        variantHtml += `<option value="${v.id}-${s.id}" 
                            data-variant-id="${v.id}" 
                            data-size-id="${s.id}" 
                            data-color-name="${v.color_name}" 
                            data-size-name="${s.name}">
                            ${v.color_name} - ${s.name}
                        </option>`;
                    });
                }
            });
        }

        const rowId = Date.now();
        const newRow = `
            <tr id="row-${rowId}">
                <td>
                    ${product.name} (${product.product_code})
                    <input type="hidden" name="products[${rowId}][id]" value="${product.id}">
                    <input type="hidden" name="products[${rowId}][variant_id]" class="variant-id-val">
                    <input type="hidden" name="products[${rowId}][size_id]" class="size-id-val">
                </td>
                <td>
                    <select class="form-select form-select-sm variant-picker">
                        ${variantHtml}
                    </select>
                    <input type="hidden" name="products[${rowId}][color]" class="color-name">
                    <input type="hidden" name="products[${rowId}][size]" class="size-name">
                </td>
                <td><input type="number" name="products[${rowId}][qty]" class="form-control form-control-sm" value="1" min="1"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-product">&times;</button></td>
            </tr>`;
        $('#printQueueBody').append(newRow);
        $('#noDataRow').hide();
    }

    $(document).on('change', '.variant-picker', function() {
        const sel = $(this).find(':selected');
        const row = $(this).closest('tr');
        row.find('.variant-id-val').val(sel.data('variant-id') || '');
        row.find('.size-id-val').val(sel.data('size-id') || '');
        row.find('.color-name').val(sel.data('color-name') || '');
        row.find('.size-name').val(sel.data('size-name') || '');
    });

    $(document).on('click', '.remove-product', function() {
        $(this).closest('tr').remove();
        if ($('#printQueueBody tr').length === 1) $('#noDataRow').show();
    });

    $('#previewBtn, #printBtn').on('click', function() {
        const isPrint = $(this).attr('id') === 'printBtn';
        if ($('#printQueueBody tr:not(#noDataRow)').length === 0) {
            Swal.fire('Error', 'প্রথমে প্রোডাক্ট যোগ করুন।', 'error');
            return;
        }
        if (!$('#paperSize').val()) {
            Swal.fire('Warning', 'পেপার সাইজ সিলেক্ট করুন।', 'warning');
            return;
        }

        showLoading(isPrint ? 'Printing...' : 'Generating Preview...');

        $.ajax({
            url: "{{ route('barcode.print') }}",
            method: 'POST',
            data: $('#printForm').serialize(),
            success: function(response) {
                Swal.close();
                if (isPrint) {
                    const ifr = document.createElement('iframe');
                    ifr.style.cssText = 'position:absolute;width:0;height:0;border:0';
                    document.body.appendChild(ifr);
                    ifr.contentWindow.document.write(response);
                    ifr.contentWindow.document.close();
                    ifr.onload = function() {
                        ifr.contentWindow.print();
                        setTimeout(() => document.body.removeChild(ifr), 1000);
                    };
                } else {
                    $('#previewCard').show();
                    $('#previewFrame').contents().find('html').html(response);
                    Swal.fire({ icon: 'success', title: 'Preview Ready!', text: 'নিচের দিকে স্ক্রল করুন।', timer: 2000, toast: true, position: 'top-end', showConfirmButton: false });
                    $('html, body').animate({ scrollTop: $("#previewCard").offset().top - 20 }, 800);
                }
            },
            error: function() { Swal.fire('Failed', 'সার্ভার থেকে ডাটা আনতে সমস্যা হয়েছে।', 'error'); }
        });
    });

    $('#paperSize').on('change', function() { $('#customSizeFields').toggle($(this).val() === 'custom'); });
});
</script>
@endsection