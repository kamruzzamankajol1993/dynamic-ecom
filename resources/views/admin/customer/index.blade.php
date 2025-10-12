@extends('admin.master.master')
@section('title', 'Customer List')
@section('css')
<style>
    .loader-row {
        text-align: center;
    }
    .spinner-border-sm {
        width: 1.5rem;
        height: 1.5rem;
        border-width: .2em;
    }
</style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Customer List</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search customers..." aria-label="Search">
                </form>
                <a href="{{ route('customer.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;"><i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New Customer</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th class="sortable" data-column="name">Name</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Total Buy</th>
                                <th class="sortable" data-column="type">Type</th>
                                <th class="sortable" data-column="status">Status</th>
                                 <th>Source</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            {{-- Loader row will be shown here initially --}}
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
@endsection
@section('script')
<script>
$(document).ready(function() {
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.customer.data') }}",
        destroy: id => `{{ url('customer') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };

    const loaderRow = `
        <tr class="loader-row">
            <td colspan="9">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;

    function fetchData() {
        $('#tableBody').html(loaderRow); // Show loader before fetching

        $.get(routes.fetch, {
            page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection
        }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="9" class="text-center">No customers found.</td></tr>';
            } else {
                res.data.forEach((customer, i) => {
                    const statusBadge = customer.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    const showUrl = `{{ url('customer') }}/${customer.id}`;
                    const editUrl = `{{ url('customer') }}/${customer.id}/edit`;
                    const typeText = customer.type.charAt(0).toUpperCase() + customer.type.slice(1);

                    let contactHtml = `<div>${customer.phone}</div>`;
                    if (customer.email) {
                        contactHtml += `<small class="text-muted">${customer.email}</small>`;
                    }

                    let addressHtml = customer.address || 'N/A';
                    if (!customer.address && Array.isArray(customer.addresses) && customer.addresses.length > 0) {
                        addressHtml = customer.addresses[0].address;
                    }

                     // --- NEW LOGIC FOR SOURCE BADGE ---
                    let sourceBadge = '';
                    if (customer.source === 'admin') {
                        sourceBadge = '<span class="badge bg-info">Admin</span>';
                    } else {
                        sourceBadge = '<span class="badge bg-secondary">Website</span>';
                    }
                    // --- END OF NEW LOGIC ---

                    // Format the total buy amount
                    const totalBuy = customer.orders_sum_total_amount ? parseFloat(customer.orders_sum_total_amount).toFixed(2) : '0.00';

                    rows += `<tr>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td>${customer.name}</td>
                        <td>${contactHtml}</td>
                        <td>${addressHtml}</td>
                        <td>৳${totalBuy}</td>
                        <td>${typeText}</td>
                        <td>${statusBadge}</td>
                        <td>${sourceBadge}</td>
                        <td class="d-flex gap-2">
                            <a href="${showUrl}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                             <form action="${routes.destroy(customer.id)}" method="POST" class="d-inline">
                            <input type="hidden" name="_token" value="${routes.csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>
                        </form>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows); // Replace loader with data

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

    $('#searchInput').on('keyup', function () { searchTerm = $(this).val(); currentPage = 1; fetchData(); });
    $(document).on('click', '.sortable', function () {
        let col = $(this).data('column');
        sortDirection = sortColumn === col ? (sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
        sortColumn = col; fetchData();
    });
    $(document).on('click', '.page-link', function (e) { e.preventDefault(); currentPage = $(this).data('page'); fetchData(); });

    // UPDATED DELETE BUTTON CLICK HANDLER
    $(document).on('click', '.btn-delete', function () {
        const deleteButton = $(this); // Reference to the button
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Find the closest form and submit it
                deleteButton.closest('form').submit();
            }
        });
    });

    fetchData();
});
</script>
@endsection
