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
    /* Checkbox Styles */
    .form-check-input {
        cursor: pointer;
    }
</style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Customer List</h2>
            <div class="d-flex align-items-center flex-wrap gap-2">
                
                <select id="filterType" class="form-select" style="width: 130px;">
                    <option value="">All Types</option>
                    <option value="normal">Normal</option>
                    <option value="silver">Silver</option>
                    <option value="platinum">Platinum</option>
                </select>

                <select id="filterYear" class="form-select" style="width: 130px;">
                    <option value="">All Years</option>
                    @foreach(range(2030, 2025) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                <div id="bulkActionContainer" class="d-flex align-items-center bg-light p-1 rounded border" style="display: none;">
                    <span class="me-2 text-muted small px-2">Selected:</span>
                    <select id="bulkTypeSelect" class="form-select form-select-sm me-2" style="width: 130px;">
                        <option value="">Change Type</option>
                        <option value="normal">Set Normal</option>
                        <option value="silver">Set Silver</option>
                        <option value="platinum">Set Platinum</option>
                    </select>
                    <button type="button" id="applyBulkUpdate" class="btn btn-warning btn-sm text-white">Update</button>
                </div>
                
                <form class="d-flex" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search customers..." aria-label="Search">
                </form>
                <a href="{{ route('customer.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;"><i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add Customer</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                <th>Sl</th>
                                <th class="sortable" data-column="name">Name</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Total Buy <span id="yearLabel" class="text-muted small"></span></th>
                                <th class="sortable" data-column="type">Type</th>
                                <th class="sortable" data-column="status">Status</th>
                                <th>Source</th>
                                <th class="sortable" data-column="created_at">Created At</th>
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
        bulkUpdate: "{{ route('customer.bulk-update-type') }}",
        csrf: "{{ csrf_token() }}"
    };

    const loaderRow = `
        <tr class="loader-row">
            <td colspan="11">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;

    function fetchData() {
        $('#tableBody').html(loaderRow);
        
        // Reset check all box and hide bulk action
        $('#checkAll').prop('checked', false);
        toggleBulkAction();

        let typeFilter = $('#filterType').val();
        let yearFilter = $('#filterYear').val(); // New Year Filter Value

        // Update Table Header Label
        if(yearFilter) {
            $('#yearLabel').text(`(${yearFilter})`);
        } else {
            $('#yearLabel').text('');
        }

        $.get(routes.fetch, {
            page: currentPage, 
            search: searchTerm, 
            sort: sortColumn, 
            direction: sortDirection,
            type: typeFilter,
            year: yearFilter // Sending year to controller
        }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="11" class="text-center">No customers found.</td></tr>';
            } else {
                res.data.forEach((customer, i) => {
                    const statusBadge = customer.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    const showUrl = `{{ url('customer') }}/${customer.id}`;
                    const editUrl = `{{ url('customer') }}/${customer.id}/edit`;
                    const typeText = customer.type ? (customer.type.charAt(0).toUpperCase() + customer.type.slice(1)) : 'N/A';

                    let contactHtml = `<div>${customer.phone}</div>`;
                    if (customer.email) {
                        contactHtml += `<small class="text-muted">${customer.email}</small>`;
                    }

                    let addressHtml = customer.address || 'N/A';
                    if (!customer.address && Array.isArray(customer.addresses) && customer.addresses.length > 0) {
                        addressHtml = customer.addresses[0].address;
                    }

                    let sourceBadge = '';
                    if (customer.source === 'admin') {
                        sourceBadge = '<span class="badge bg-info">Admin</span>';
                    } else {
                        sourceBadge = '<span class="badge bg-secondary">Website</span>';
                    }

                    // This value will now reflect the filtered year if controller is updated
                    const totalBuy = customer.orders_sum_total_amount ? parseFloat(customer.orders_sum_total_amount).toFixed(2) : '0.00';
                    
                    const createdAt = new Date(customer.created_at);
                    const formattedDate = `${String(createdAt.getDate()).padStart(2, '0')}/${String(createdAt.getMonth() + 1).padStart(2, '0')}/${createdAt.getFullYear()}`;

                    rows += `<tr>
                        <td><input type="checkbox" class="form-check-input row-checkbox" value="${customer.id}"></td>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td>${customer.name}</td>
                        <td>${contactHtml}</td>
                        <td>${addressHtml}</td>
                        <td class="fw-bold">৳${totalBuy}</td>
                        <td>${typeText}</td>
                        <td>${statusBadge}</td>
                        <td>${sourceBadge}</td>
                        <td>${formattedDate}</td>
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
            $('#tableBody').html(rows);

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

    // --- Filter Listeners ---
    $('#filterType, #filterYear').on('change', function() {
        currentPage = 1;
        fetchData();
    });

    $('#searchInput').on('keyup', function () { searchTerm = $(this).val(); currentPage = 1; fetchData(); });
    
    $(document).on('click', '.sortable', function () {
        let col = $(this).data('column');
        sortDirection = sortColumn === col ? (sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
        sortColumn = col; fetchData();
    });
    
    $(document).on('click', '.page-link', function (e) { e.preventDefault(); currentPage = $(this).data('page'); fetchData(); });

    $(document).on('click', '.btn-delete', function () {
        const deleteButton = $(this); 
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
                deleteButton.closest('form').submit();
            }
        });
    });

    // ----------------------------------------------------
    // BULK ACTION LOGIC
    // ----------------------------------------------------

    // 1. Check All functionality
    $('#checkAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkAction();
    });

    // 2. Individual Checkbox functionality
    $(document).on('change', '.row-checkbox', function() {
        if(!$(this).prop('checked')) {
            $('#checkAll').prop('checked', false);
        }
        toggleBulkAction();
    });

    // 3. Show/Hide Bulk Action Div (Fixed Logic)
    function toggleBulkAction() {
        var checkedCount = $('.row-checkbox:checked').length;
        if(checkedCount > 0) {
            $('#bulkActionContainer').css('display', 'flex'); // Show
        } else {
            $('#bulkActionContainer').hide(); // Hide
        }
    }

    // 4. Apply Bulk Update
    $('#applyBulkUpdate').on('click', function() {
        let selectedType = $('#bulkTypeSelect').val();
        let ids = [];
        
        $('.row-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length === 0) {
            Swal.fire('Error', 'Please select at least one customer.', 'error');
            return;
        }
        if(!selectedType) {
            Swal.fire('Error', 'Please select a type to update.', 'error');
            return;
        }

        Swal.fire({
            title: 'Confirm Update',
            text: `Are you sure you want to change ${ids.length} customers to ${selectedType.toUpperCase()}? This will update their discount accordingly.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Update'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.bulkUpdate,
                    type: "POST",
                    data: {
                        _token: routes.csrf,
                        ids: ids,
                        type: selectedType
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire('Success', response.message, 'success');
                            
                            // Reset everything
                            $('#checkAll').prop('checked', false);
                            $('.row-checkbox').prop('checked', false);
                            $('#bulkTypeSelect').val('');
                            $('#bulkActionContainer').hide();
                            
                            fetchData(); // Refresh table
                        } else {
                            Swal.fire('Error', 'Something went wrong.', 'error');
                        }
                    },
                    error: function(err) {
                        console.log(err);
                        Swal.fire('Error', 'Failed to update. Check console/logs.', 'error');
                    }
                });
            }
        });
    });

    fetchData();
});
</script>
@endsection