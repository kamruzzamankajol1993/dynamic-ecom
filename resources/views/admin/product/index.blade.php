@extends('admin.master.master')
@section('title', 'Product List')
@section('css')
<style>
    /* --- Font & Layout Adjustments --- */
    .main-content {
        font-size: 0.9rem; /* Reduced base font size */
    }
    .main-content h2 { font-size: 1.6rem; }
    .main-content h5 { font-size: 1.1rem; }

    /* Forms & Buttons */
    .form-control, .form-select, .btn {
        font-size: 0.875rem; /* Consistent font size for form elements */
    }
    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.3rem;
    }

    /* Cards */
    .card-body, .card-header, .card-footer {
        padding: 1rem;
    }

    /* Tables */
    .table {
        font-size: 0.875rem;
    }
    .table th, .table td {
        padding: 0.6rem 0.5rem; /* Reduce padding for a tighter look */
        vertical-align: middle;
    }
    .pagination {
        font-size: 0.875rem;
    }
</style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Product List</h2>
            <a href="{{ route('product.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;">
                <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New Product
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Filter Products</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="productNameFilter" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productNameFilter" placeholder="Enter name...">
                    </div>
                    <div class="col-md-3">
                        <label for="productCodeFilter" class="form-label">Product Code</label>
                        <input type="text" class="form-control" id="productCodeFilter" placeholder="Enter code...">
                    </div>
                    <div class="col-md-3">
                        <label for="categoryFilter" class="form-label">Category</label>
                        <select id="categoryFilter" class="form-select">
                            <option value="" selected>All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary me-2" id="filterBtn">Filter</button>
                        <button class="btn btn-secondary" id="resetBtn">Reset</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <div class="mb-3">
                    <div class="d-inline-block" style="display: none;" id="bulkActionContainer">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="bulkActions" data-bs-toggle="dropdown" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="bulkActions">
                                <li><a class="dropdown-item bulk-action-btn" href="#" data-status="1">Set to Active</a></li>
                                <li><a class="dropdown-item bulk-action-btn" href="#" data-status="0">Set to Inactive</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                  <th><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                                <th>Sl</th>
                                <th>Image</th>
                                <th class="sortable" data-column="name">Name</th>
                                <th>Price</th>
                                <th>Total Stock</th>
                                <th class="sortable" data-column="created_at">Created At</th>
                                <th class="sortable" data-column="status">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            {{-- Data will be loaded via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="text-muted"></div>
                <nav>
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</main>
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="stockModalLabel">Stock Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="stockModalBodyContent">
          </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Store all sizes passed from the controller for easy lookup
    const allSizes = @json($sizes);

    var currentPage = 1,
        productName = '',
        productCode = '',
        categoryId = '',
        sortColumn = 'id',
        sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.product.data') }}",
        destroy:"{{ route('ajax_products_delete') }}",
        bulk_status_update: "{{ route('ajax.product.bulk-status-update') }}",
        csrf: "{{ csrf_token() }}"
    };
    
    // --- Debounce function to delay execution ---
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    function fetchData() {
        const loaderHtml = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Fetching products...</p>
                </td>
            </tr>`;
        $('#tableBody').html(loaderHtml);

        $.get(routes.fetch, {
            page: currentPage,
            product_name: productName,
            product_code: productCode,
            category_id: categoryId,
            sort: sortColumn,
            direction: sortDirection
        }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="8" class="text-center">No products found.</td></tr>';
            } else {
                res.data.forEach((product, i) => {
                    const statusBadge = product.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    const firstImage = Array.isArray(product.thumbnail_image) && product.thumbnail_image.length > 0 ? product.thumbnail_image[0] : null;
                    const imageUrl = firstImage ? `{{ asset('public/uploads') }}/${firstImage}` : 'https://placehold.co/50x50';
                    const editUrl = `{{ url('product') }}/${product.id}/edit`;
                    const showUrl = `{{ url('product') }}/${product.id}`;

                    let priceHtml = `<b>${product.base_price}</b>`;
                    if (product.discount_price) {
                        priceHtml = `<del>${product.base_price}</del><br><b>${product.discount_price}</b>`;
                    }

                    let totalStock = 0;
                    if (product.variants && product.variants.length > 0) {
                        product.variants.forEach(variant => {
                            if (variant.sizes && Array.isArray(variant.sizes)) {
                                variant.sizes.forEach(sizeInfo => {
                                    totalStock +=  Number(sizeInfo.quantity);
                                });
                            }
                        });
                    }
                    
                    const safeProductName = product.name.replace(/'/g, "&apos;");
                    const variantsJson = JSON.stringify(product.variants);
                    const stockButton = `<button type="button" class="btn btn-sm btn-outline-secondary btn-stock-modal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stockModal"
                                            data-product-name='${safeProductName}'
                                            data-variants='${variantsJson}'>
                                            <b>${totalStock}</b>
                                         </button>`;

                    const createdAt = new Date(product.created_at).toLocaleDateString('en-US', {
                        day: '2-digit', month: 'short', year: 'numeric'
                    });

                    rows += `<tr>
                         <td><input class="form-check-input product-checkbox" type="checkbox" value="${product.id}"></td>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td><img src="${imageUrl}" alt="${product.name}" width="50" class="img-thumbnail"></td>
                        <td>${product.name}</td>
                        <td>${priceHtml}</td>
                        <td>${stockButton}</td>
                        <td>${createdAt}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="${showUrl}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${product.id}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);

            // Pagination logic
            let paginationHtml = '';
            if (res.last_page > 1) {
                paginationHtml += `<li class="page-item ${res.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="1">First</a></li>`;
                paginationHtml += `<li class="page-item ${res.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page - 1}">Prev</a></li>`;
                const startPage = Math.max(1, res.current_page - 2);
                const endPage = Math.min(res.last_page, res.current_page + 2);
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `<li class="page-item ${i === res.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                paginationHtml += `<li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page + 1}">Next</a></li>`;
                paginationHtml += `<li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.last_page}">Last</a></li>`;
            }
            $('#pagination').html(paginationHtml);
        });
    }

    // Modal Population Logic
    $(document).on('click', '.btn-stock-modal', function() {
        const productName = $(this).data('product-name');
        const variants = $(this).data('variants');
        const modalTitle = $('#stockModalLabel');
        const modalBody = $('#stockModalBodyContent');

        modalTitle.text(`Stock Details for: ${productName}`);
        modalBody.empty();

        if (variants && variants.length > 0) {
            let contentHtml = '<table class="table table-sm table-bordered"><thead><tr><th>Color</th><th>Size</th><th>Quantity</th></tr></thead><tbody>';
            let hasStock = false;
            variants.forEach(variant => {
                if (variant.sizes && Array.isArray(variant.sizes)) {
                    const availableSizes = variant.sizes.filter(s => s.quantity > 0);
                    if (availableSizes.length > 0) {
                        hasStock = true;
                        availableSizes.forEach(sizeInfo => {
                            const sizeName = allSizes[sizeInfo.size_id] ? allSizes[sizeInfo.size_id].name : 'Unknown';
                            contentHtml += `<tr>
                                <td>${variant.color ? variant.color.name : 'N/A'}</td>
                                <td>${sizeName}</td>
                                <td><b>${sizeInfo.quantity}</b></td>
                            </tr>`;
                        });
                    }
                }
            });
            contentHtml += '</tbody></table>';

            if (!hasStock) {
                modalBody.html('<p class="text-muted">No stock variations available for this product.</p>');
            } else {
                modalBody.html(contentHtml);
            }
        } else {
            modalBody.html('<p class="text-muted">No stock variations available for this product.</p>');
        }
    });

    // --- Central function to apply filters and fetch data ---
    function applyFiltersAndFetch() {
        productName = $('#productNameFilter').val();
        productCode = $('#productCodeFilter').val();
        categoryId = $('#categoryFilter').val();
        currentPage = 1; 
        fetchData();
    }

    // --- Event handlers for filters ---
    $('#filterBtn').on('click', applyFiltersAndFetch);
    $('#categoryFilter').on('change', applyFiltersAndFetch);
    $('#productNameFilter, #productCodeFilter').on('keyup', debounce(applyFiltersAndFetch, 400));

    $('#resetBtn').on('click', function() {
        $('#productNameFilter').val('');
        $('#productCodeFilter').val('');
        $('#categoryFilter').val('');
        applyFiltersAndFetch();
    });

     // --- Bulk Action Logic ---
    function updateBulkActionVisibility() {
        const checkedCount = $('.product-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkActionContainer').show();
        } else {
            $('#bulkActionContainer').hide();
        }
    }

    $('#selectAllCheckbox').on('click', function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkActionVisibility();
    });

    $(document).on('click', '.product-checkbox', function() {
        $('#selectAllCheckbox').prop('checked', $('.product-checkbox:checked').length === $('.product-checkbox').length);
        updateBulkActionVisibility();
    });

    $('.bulk-action-btn').on('click', function(e) {
        e.preventDefault();
        const status = $(this).data('status');
        const selectedIds = $('.product-checkbox:checked').map((_, el) => $(el).val()).get();

        if (selectedIds.length === 0) {
            Swal.fire('No selection', 'Please select at least one product.', 'info');
            return;
        }

        const statusText = status == 1 ? 'Active' : 'Inactive';
        Swal.fire({
            title: `Update ${selectedIds.length} products?`,
            text: `Set status to "${statusText}" for all selected products.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, update!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.bulk_status_update,
                    method: 'POST',
                    data: { _token: routes.csrf, ids: selectedIds, status: status },
                    success: (response) => {
                        Swal.fire('Success!', response.message, 'success');
                        fetchData();
                    },
                    error: () => Swal.fire('Error!', 'Something went wrong.', 'error')
                });
            }
        });
    });

    // Other event handlers
    $(document).on('click', '.sortable', function () {
        let col = $(this).data('column');
        sortDirection = sortColumn === col ? (sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
        sortColumn = col;
        fetchData();
    });

    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        currentPage = $(this).data('page');
        fetchData();
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.destroy,
                    method: 'get',
                    data: { id: id },
                    success: function() {
                        Swal.fire('Deleted!', 'The product has been deleted.', 'success');
                        fetchData(); 
                    }
                });
            }
        });
    });

    fetchData(); // Initial data load
});
</script>
@endsection