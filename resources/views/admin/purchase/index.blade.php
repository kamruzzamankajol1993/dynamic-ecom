@extends('admin.master.master')
@section('title', 'Purchase List')
@section('css')
<style>
    .loader-row { text-align: center; }
    .spinner-border-sm { width: 1.5rem; height: 1.5rem; border-width: .2em; }
</style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Purchase List</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search by ID or Supplier..." aria-label="Search">
                </form>
                <a href="{{ route('purchase.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;"><i data-feather="plus" class="me-1"></i> Add New Purchase</a>
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
                                <th class="sortable" data-column="purchase_date">Date</th>
                                <th class="sortable" data-column="purchase_no">Purchase No</th>
                                <th>Supplier</th>
                                <th>Total Amount</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th class="sortable" data-column="payment_status">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
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
        fetch: "{{ route('ajax.purchase.data') }}",
        destroy: id => `{{ url('purchase') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };

    const loaderRow = `<tr class="loader-row"><td colspan="9"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></td></tr>`;

    function fetchData() {
        $('#tableBody').html(loaderRow);
        $.get(routes.fetch, { page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="9" class="text-center">No purchases found.</td></tr>';
            } else {
                res.data.forEach((purchase, i) => {
                    const showUrl = `{{ url('purchase') }}/${purchase.id}`;
                    const editUrl = `{{ url('purchase') }}/${purchase.id}/edit`;
                    
                    let statusBadge = '';
                    if(purchase.payment_status == 'paid') statusBadge = '<span class="badge bg-success">Paid</span>';
                    else if(purchase.payment_status == 'partial') statusBadge = '<span class="badge bg-warning">Partial</span>';
                    else statusBadge = '<span class="badge bg-danger">Due</span>';
                    
                    const purchaseDate = new Date(purchase.purchase_date).toLocaleDateString('en-GB');

                    rows += `<tr>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td>${purchaseDate}</td>
                        <td>${purchase.purchase_no}</td>
                        <td>${purchase.supplier ? purchase.supplier.company_name : 'N/A'}</td>
                        <td>৳${parseFloat(purchase.total_amount).toFixed(2)}</td>
                        <td>৳${parseFloat(purchase.paid_amount).toFixed(2)}</td>
                        <td>৳${parseFloat(purchase.due_amount).toFixed(2)}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="${showUrl}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${purchase.id}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
            // Pagination logic here (same as customer index)
        });
    }

    $('#searchInput').on('keyup', function () { searchTerm = $(this).val(); currentPage = 1; fetchData(); });
    $(document).on('click', '.sortable', function () {
        let col = $(this).data('column');
        sortDirection = sortColumn === col ? (sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
        sortColumn = col; fetchData();
    });
    $(document).on('click', '.page-link', function (e) { e.preventDefault(); currentPage = $(this).data('page'); fetchData(); });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?', text: "This will delete the purchase and revert the stock quantities. This action cannot be undone!", icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.destroy(id), method: 'DELETE', data: { _token: routes.csrf },
                    success: function(res) {
                        Swal.fire('Deleted!', res.message, 'success');
                        fetchData();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });

    fetchData();
});
</script>
@endsection