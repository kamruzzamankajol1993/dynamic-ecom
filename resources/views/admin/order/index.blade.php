@extends('admin.master.master')
@section('title', 'Invoice List')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        background-color: transparent;
        border: 1px solid transparent;
        border-radius: .25rem;
        padding: .5rem 1rem;
        margin-right: 0.5rem;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6;
        font-weight: 500;
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 500;
        white-space: nowrap;
    }
    .badge { font-size: 0.8em; padding: 0.4em 0.6em; }
    .table th, .table td { vertical-align: middle; }
    #detailsModal .invoice-details p { margin-bottom: 0.5rem; }
    #detailsModal .invoice-items-table th, #detailsModal .invoice-items-table td { padding: 0.5rem; }
    #detailsModal .invoice-totals { text-align: right; }
    #detailsModal .invoice-totals td { padding: 0.25rem 0.5rem; }
    .pagination .page-link {
        /* border-radius: 50% !important; */
        margin: 0 2px;
    }
</style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Invoice List</h2>
            <a href="{{ route('order.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;">
                <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New Invoice
            </a>
        </div>
        <div class="card">
            <div class="card-header bg-light">
                <ul class="nav nav-tabs card-header-tabs" id="orderStatusTabs" role="tablist">
                    @php
                        $tabs = ['all', 'pending', 'waiting', 'ready to ship', 'shipping', 'delivered', 'cancelled', 'failed to delivery', 'refund only'];
                        $statusColors = [
                            'pending' => 'secondary', 'waiting' => 'info', 'ready to ship' => 'primary',
                            'shipping' => 'warning', 'delivered' => 'success', 'cancelled' => 'danger',
                            'failed to delivery' => 'dark', 'refund only' => 'light text-dark',
                        ];
                    @endphp
                    @foreach($tabs as $tab)
                        @php
                            $statusKey = strtolower(str_replace(' ', ' ', $tab));
                        @endphp
                        <li class="nav-item" role="presentation">
                            <button class="nav-link @if($tab === 'pending') active @endif" data-status="{{ $statusKey }}" type="button">
                                {{ ucfirst($tab) }} ({{ $statusCounts[$statusKey] ?? 0 }})
                            </button>
                        </li>
                    @endforeach
                </ul>
                
            </div>
            <div class="card-body">
                {{-- Filter Section --}}
                <div class="p-3 mb-3 bg-light rounded border">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label for="filterOrderId" class="form-label">Order ID</label>
                            <input type="text" class="form-control form-control-sm" id="filterOrderId" placeholder="Search ID...">
                        </div>
                        <div class="col-md-2">
                            <label for="filterCustomerName" class="form-label">Customer</label>
                            <input type="text" class="form-control form-control-sm" id="filterCustomerName" placeholder="Name/Phone...">
                        </div>
                         <div class="col-md-2">
                            <label for="filterProduct" class="form-label">Product</label>
                            <input type="text" class="form-control form-control-sm" id="filterProduct" placeholder="Name/Code...">
                        </div>
                        <div class="col-md-2">
                            <label for="filterStartDate" class="form-label">Start Date</label>
                            <input type="text" class="form-control form-control-sm" id="filterStartDate" placeholder="Select date...">
                        </div>
                        <div class="col-md-2">
                            <label for="filterEndDate" class="form-label">End Date</label>
                            <input type="text" class="form-control form-control-sm" id="filterEndDate" placeholder="Select date...">
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="button" id="filterBtn" class="btn btn-primary w-100"><i class="fa fa-filter me-1"></i></button>
                                <button type="button" id="resetBtn" class="btn btn-secondary w-100"><i class="fa fa-undo me-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button class="btn btn-danger btn-sm" id="deleteAllBtn" style="display: none;">
                            <i class="fa fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllCheckbox"></th>
                                <th>Sl</th>
                                <th>Order ID</th>
                                <th>Billing Name</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Total Pay</th>
                                <th>COD</th>
                                <th>Pay Status</th>
                                <th>Delivery Status</th>
                                <th>Order From</th>
                                <th>Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div id="entryInfo" class="text-muted"></div>
                <nav>
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <input type="hidden" id="statusOrderId">
                    <select id="statusSelect" class="form-select">
                        @foreach($tabs as $tab)
                            @if($tab !== 'all')
                            <option value="{{ strtolower(str_replace(' ', ' ', $tab)) }}">{{ ucfirst($tab) }}</option>
                            @endif
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i> Close</button>
                <button type="button" id="saveStatusBtn" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalTitle">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i> Close</button>
                <button type="button" class="btn btn-primary"><i class="fa fa-print me-1"></i> Print</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {
    var currentPage = 1, currentStatus = 'pending';
    var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    var detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
    var debounceTimer;

    flatpickr("#filterStartDate", { dateFormat: "Y-m-d" });
    flatpickr("#filterEndDate", { dateFormat: "Y-m-d" });

    var routes = {
        fetch: "{{ route('ajax.order.data') }}",
        destroy: id => `{{ url('order') }}/${id}`,
        destroyMultiple: "{{ route('order.destroy-multiple') }}",
        updateStatus: id => `{{ route('order.update-status', ':id') }}`.replace(':id', id),
        getDetails: id => `{{ route('order.get-details', ':id') }}`.replace(':id', id),
        csrf: "{{ csrf_token() }}"
    };

    const statusColors = @json($statusColors);

    function fetchData() {
        $('#tableBody').html('<tr><td colspan="13" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div></td></tr>');
        
        const filterData = {
            page: currentPage,
            status: currentStatus,
            order_id: $('#filterOrderId').val(),
            customer_name: $('#filterCustomerName').val(),
            product_info: $('#filterProduct').val(),
            start_date: $('#filterStartDate').val(),
            end_date: $('#filterEndDate').val(),
        };

        $.get(routes.fetch, filterData, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="13" class="text-center">No orders found.</td></tr>';
            } else {
                res.data.forEach((order, i) => {
                    const showUrl = `{{ url('order') }}/${order.id}`;
                    const editUrl = `{{ url('order') }}/${order.id}/edit`;
                    const billingName = order.customer ? `${order.customer.name} - ${order.customer.phone}` : '<span class="text-danger">Customer Deleted</span>';
                    const date = new Date(order.created_at).toLocaleString('en-US', { day: '2-digit', month: 'short', year: 'numeric' });
                    
                    let payStatusBadge;
                    if (order.status === 'delivered') {
                        payStatusBadge = `<span class="badge bg-success">Paid</span>`;
                    } else if (order.payment_method === 'bkash' && order.statusMessage === 'successful') {
                        payStatusBadge = `<span class="badge bg-success">Paid</span>`;
                    } else if (parseFloat(order.cod) > 0) {
                        payStatusBadge = `<span class="badge bg-danger">Unpaid</span>`;
                    } else {
                        payStatusBadge = order.payment_status === 'paid' ? `<span class="badge bg-success">Paid</span>` : `<span class="badge bg-danger">Unpaid</span>`;
                    }

                    const statusKey = order.status.replace(' ', ' ');
                      const deliveryStatusButton = `<button class="btn btn-sm btn-${statusColors[statusKey]} btn-update-status" data-id="${order.id}" data-status="${order.status}">${order.status.replace(/_/g, ' ').toUpperCase()}</button>`;
                    const detailsButton = `<button class="btn btn-sm btn-primary btn-details" data-id="${order.id}"><i class="fa fa-eye me-1"></i></button>`;
                    const orderFromBadge = order.order_from ? (order.order_from === 'web' ? `<span class="badge bg-info">Web</span>` : `<span class="badge bg-secondary">Admin</span>`) : '';

                    rows += `<tr>
                        <td><input type="checkbox" class="row-checkbox" value="${order.id}"></td>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td><b>${order.invoice_no}</b></td>
                        <td>${billingName}</td>
                        <td>${date}</td>
                        <td>${order.total_amount}</td>
                        <td>${order.total_pay}</td>
                        <td>${order.cod}</td>
                        <td>${payStatusBadge}</td>
                        <td>${deliveryStatusButton}</td>
                        <td>${orderFromBadge}</td>
                        <td>${detailsButton}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">...</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="${showUrl}"><i class="fa fa-eye me-2"></i>View</a></li>
                                    <li><a class="dropdown-item" href="${editUrl}"><i class="fa fa-edit me-2"></i>Edit</a></li>
                                    <li><button class="dropdown-item btn-delete" data-id="${order.id}"><i class="fa fa-trash me-2"></i>Delete</button></li>
                                </ul>
                            </div>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
            
            const startEntry = (res.current_page - 1) * 10 + 1;
            const endEntry = startEntry + res.data.length - 1;
            $('#entryInfo').text(res.data.length > 0 ? `Showing ${startEntry} to ${endEntry} of ${res.total} entries` : 'No entries');

            let paginationHtml = '';
            if (res.last_page > 1) {
                paginationHtml += `<li class="page-item ${res.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page - 1}">&laquo;</a></li>`;
                const startPage = Math.max(1, res.current_page - 2);
                const endPage = Math.min(res.last_page, res.current_page + 2);
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `<li class="page-item ${i === res.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                paginationHtml += `<li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page + 1}">&raquo;</a></li>`;
            }
            $('#pagination').html(paginationHtml);
        });
    }

    // --- Event Handlers ---
    $('#orderStatusTabs .nav-link').on('click', function() {
        $('#orderStatusTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        currentStatus = $(this).data('status');
        currentPage = 1;
        fetchData();
    });

    // Onkeyup search with debounce
    const searchInputs = '#filterOrderId, #filterCustomerName, #filterProduct';
    $(document).on('keyup', searchInputs, function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            currentPage = 1; 
            fetchData();
        }, 500); // 500ms delay
    });
    
    $('#filterBtn').on('click', function() {
        clearTimeout(debounceTimer);
        currentPage = 1;
        fetchData();
    });

    $('#resetBtn').on('click', function() {
        $('#filterForm')[0].reset();
        flatpickr("#filterStartDate").clear();
        flatpickr("#filterEndDate").clear();
        currentPage = 1;
        fetchData();
    });

    $(document).on('click', '.page-link', function (e) { e.preventDefault(); if(!$(this).parent().hasClass('disabled')) { currentPage = $(this).data('page'); fetchData(); } });
    
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
                    url: routes.destroy(id),
                    method: 'DELETE',
                    data: { _token: routes.csrf },
                    success: function() {
                        Swal.fire('Deleted!', 'The order has been deleted.', 'success');
                        fetchData();
                    }
                });
            }
        });
    });
     $(document).on('click', '.btn-update-status', function() {
        const orderId = $(this).data('id');
        const currentStatus = $(this).data('status');
        $('#statusOrderId').val(orderId);
        $('#statusSelect').val(currentStatus);
        statusModal.show();
    });
    $('#saveStatusBtn').on('click', function() {
        const orderId = $('#statusOrderId').val();
        const newStatus = $('#statusSelect').val();
        $.post(routes.updateStatus(orderId), { _token: routes.csrf, status: newStatus }, function() {
            statusModal.hide();
            fetchData();
        });
    });

    $(document).on('click', '.btn-details', function() {
        const orderId = $(this).data('id');
        $.get(routes.getDetails(orderId), function(data) {
            $('#detailsModalTitle').text(`Invoice`);
            let itemsHtml = '';
            if (data.order_details && data.order_details.length > 0) {
                data.order_details.forEach(item => {
                    const imageUrl = item.product.thumbnail_image && Array.isArray(item.product.thumbnail_image) && item.product.thumbnail_image.length > 0
                        ? `{{ asset('public/uploads') }}/${item.product.thumbnail_image[0]}`
                        : 'https://placehold.co/50x50';
                    itemsHtml += `
                        <tr>
                            <td><img src="${imageUrl}" width="40" class="img-thumbnail"></td>
                            <td>${item.product.name}<div class="text-muted">${item.unit_price} x ${item.quantity}</div></td>
                            <td class="text-end">${item.subtotal}</td>
                        </tr>`;
                });
            }
            const detailsHtml = `
                <div class="invoice-details mb-4">
                    <p><strong>Invoice id:</strong> <a href="#">${data.invoice_no}</a></p>
                    <p><strong>Billing Name:</strong> ${data.customer ? data.customer.name : 'N/A'} - ${data.customer ? data.customer.phone : 'N/A'}</p>
                    <p><strong>Customer Type:</strong> <span class="badge bg-warning-soft text-warning">${data.customer ? data.customer.type : ''}</span></p>
                    <p>${data.shipping_address}</p>
                </div>
                <table class="table invoice-items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Product Name</th>
                            <th class="text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>${itemsHtml}</tbody>
                </table>
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <table class="table table-sm invoice-totals">
                            <tbody>
                                <tr><td>Sub Total:</td><td>${data.subtotal}</td></tr>
                                <tr><td>Discount:</td><td>${data.discount}</td></tr>
                                <tr><td>Shipping:</td><td>${data.shipping_cost}</td></tr>
                                <tr><td><strong>Total:</strong></td><td><strong>${data.total_amount}</strong></td></tr>
                                <tr><td>Total Pay:</td><td>${data.total_pay}</td></tr>
                                <tr><td><strong>Cod:</strong></td><td><strong>${data.cod}</strong></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <p><strong>Notes:</strong> ${data.notes || 'No notes for this order.'}</p>
            `;
            $('#detailsModalBody').html(detailsHtml);
            detailsModal.show();
        });
    });

    function updateDeleteButton() {
        const selectedCount = $('.row-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        $('#deleteAllBtn').toggle(selectedCount > 0);
    }
    $('#selectAllCheckbox').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).is(':checked'));
        updateDeleteButton();
    });
    $(document).on('change', '.row-checkbox', function() {
        updateDeleteButton();
    });
    $('#deleteAllBtn').on('click', function() {
        const selectedIds = $('.row-checkbox:checked').map((_, el) => el.value).get();
        Swal.fire({
            title: `Delete ${selectedIds.length} orders?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.destroyMultiple,
                    method: 'DELETE',
                    data: { ids: selectedIds, _token: routes.csrf },
                    success: function() {
                        Swal.fire('Deleted!', 'The selected orders have been deleted.', 'success');
                        fetchData();
                        $('#deleteAllBtn').hide();
                        $('#selectAllCheckbox').prop('checked', false);
                    }
                });
            }
        });
    });

    fetchData();
});
</script>
@endsection