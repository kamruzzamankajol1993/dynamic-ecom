@extends('admin.master.master')
@section('title', 'Generate Barcodes')

@section('css')
    <!-- jQuery UI for autocomplete -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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

        {{-- The form tag is still useful for serializing data, but we won't submit it directly --}}
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
                                    <th style="width: 120px;">QTY</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="printQueueBody">
                                <tr id="noDataRow">
                                    <td colspan="3" class="text-center">No Data Available</td>
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
                               <option value="a4-40">sheet (A4) (1.799" x 1.003")</option>
                                <option value="a4-30">sheet (A4) (1" x 2.625")</option>
                                <option value="a4-24">sheet (A4) (1.334" x 2.48")</option>
                                <option value="a4-20">sheet (A4) (1" x 4")</option>
                                <option value="a4-18">sheet (A4) (1.835" x 2.5")</option>
                                <option value="a4-14">sheet (A4) (1.33" x 4")</option>
                                <option value="a4-12">sheet (A4) (2.834" x 2.5")</option>
                                <option value="a4-10">sheet (A4) (2" x 4")</option>
                                <option value="thermal-label">Thermal Label (2x1 inch)</option>
                                <option value="custom">Custom Size</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" name="show_store_name" value="1" id="showStoreName" checked>
                                <label class="form-check-label" for="showStoreName">Show Store Name</label>
                            </div>
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" name="show_product_name" value="1" id="showProductName" checked>
                                <label class="form-check-label" for="showProductName">Show Product Name</label>
                            </div>
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" name="show_price" value="1" id="showPrice" checked>
                                <label class="form-check-label" for="showPrice">Show Price</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="show_border" value="1" id="showBorder">
                                <label class="form-check-label" for="showBorder">Show Border</label>
                            </div>
                        </div>
                    </div>
                    <div id="customSizeFields" class="row mt-3" style="display: none;">
                        <div class="col-md-3">
                            <label class="form-label">Paper Width (mm)</label>
                            <input type="number" name="paper_width" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Paper Height (mm)</label>
                            <input type="number" name="paper_height" class="form-control">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="button" id="previewBtn" class="btn btn-success">Preview</button>
                        <button type="reset" id="resetBtn" class="btn btn-danger">Reset</button>
                        <button type="button" id="printBtn" class="btn btn-primary">Print</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card mt-4" id="previewCard" style="display:none;">
            <div class="card-header">
                <h5>Print Preview</h5>
            </div>
            <div class="card-body">
                <iframe id="previewFrame" style="width: 100%; height: 500px; border: 1px solid #ddd;"></iframe>
            </div>
        </div>

    </div>
</main>
@endsection

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
$(document).ready(function() {
    let productQueue = {};

    $("#productSearch").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('barcode.search') }}",
                data: { term: request.term },
                dataType: "json",
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
            $(this).val(''); // Clear search input
            return false;
        }
    });

    function addProductToQueue(product) {
        if (productQueue[product.id]) {
            let qtyInput = $(`#qty-${product.id}`);
            qtyInput.val(parseInt(qtyInput.val()) + 1);
        } else {
            productQueue[product.id] = product;
            $('#noDataRow').hide();
            const newRow = `
                <tr id="row-${product.id}">
                    <td>
                        ${product.name} (${product.product_code})
                        <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                    </td>
                    <td>
                        <input type="number" name="products[${product.id}][qty]" id="qty-${product.id}" class="form-control form-control-sm" value="1" min="1">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-product" data-id="${product.id}">&times;</button>
                    </td>
                </tr>
            `;
            $('#printQueueBody').append(newRow);
        }
    }

    $('#printQueueBody').on('click', '.remove-product', function() {
        const id = $(this).data('id');
        delete productQueue[id];
        $(`#row-${id}`).remove();
        if (Object.keys(productQueue).length === 0) {
            $('#noDataRow').show();
        }
    });

    $('#paperSize').on('change', function() {
        $('#customSizeFields').toggle($(this).val() === 'custom');
    });

    $('#resetBtn').on('click', function() {
        productQueue = {};
        $('#printQueueBody').empty().append('<tr id="noDataRow"><td colspan="3" class="text-center">No Data Available</td></tr>');
        $('#previewCard').hide();
        $('#printForm')[0].reset();
    });

    $('#previewBtn').on('click', function() {
        const form = $('#printForm');
        const formData = form.serialize();

        $.ajax({
            url: "{{ route('barcode.print') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#previewCard').show();
                $('#previewFrame').contents().find('html').html(response);
            },
            error: function() {
                alert('Could not generate preview.');
            }
        });
    });

    // --- UPDATED PRINT BUTTON LOGIC ---
    $('#printBtn').on('click', function() {
        const form = $('#printForm');
        const formData = form.serialize();

        $.ajax({
            url: "{{ route('barcode.print') }}",
            method: 'POST',
            data: formData,
            success: function(responseHtml) {
                // Create a hidden iframe
                const printFrame = document.createElement('iframe');
                printFrame.style.position = 'absolute';
                printFrame.style.width = '0';
                printFrame.style.height = '0';
                printFrame.style.border = '0';
                document.body.appendChild(printFrame);

                // Write the HTML to the iframe
                const frameDoc = printFrame.contentWindow.document;
                frameDoc.open();
                frameDoc.write(responseHtml);
                frameDoc.close();

                // Wait for the iframe to load, then trigger the print dialog
                printFrame.onload = function() {
                    printFrame.contentWindow.focus();
                    printFrame.contentWindow.print();
                    // Optional: remove the iframe after printing
                    setTimeout(() => { document.body.removeChild(printFrame); }, 1000);
                };
            },
            error: function() {
                alert('Could not prepare content for printing.');
            }
        });
    });
});
</script>
@endsection
