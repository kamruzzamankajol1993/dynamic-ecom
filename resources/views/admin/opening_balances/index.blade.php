@extends('admin.master.master')
@section('title', 'Opening Balances')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Opening Balances</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search by account..." aria-label="Search">
                </form>
                <a href="{{ route('opening-balances.create') }}" class="btn text-white" style="background-color: var(--primary-color);">
                    <i data-feather="plus" class="me-1"></i>Add New
                </a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>SL</th>
                                <th>Account Name</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>As of Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="text-muted"></div>
                <nav><ul class="pagination justify-content-center" id="pagination"></ul></nav>
            </div>
        </div>
    </div>
</main>
@endsection
@section('script')
<script>
$(document).ready(function() {
    var currentPage = 1, searchTerm = '';
    var routes = {
        fetch: "{{ route('ajax.opening-balances.data') }}",
        destroy: id => `{{ url('opening-balances') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };
    const loaderRow = `<tr><td colspan="6" class="text-center"><div class="spinner-border"></div></td></tr>`;

    function fetchData() {
        $('#tableBody').html(loaderRow);
        $.get(routes.fetch, { page: currentPage, search: searchTerm }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="6" class="text-center">No opening balances found.</td></tr>';
            } else {
                res.data.forEach((balance, index) => {
                    const editUrl = `{{ url('opening-balances') }}/${balance.id}/edit`;
                    const typeBadge = balance.type === 'debit' 
                        ? `<span class="badge bg-success text-capitalize">${balance.type}</span>`
                        : `<span class="badge bg-danger text-capitalize">${balance.type}</span>`;
                    const asOfDate = new Date(balance.as_of_date).toLocaleDateString('en-GB');

                    rows += `<tr>
                        <td>${res.from + index}</td>
                        <td>${balance.account.name} (${balance.account.code || 'N/A'})</td>
                        <td>৳${parseFloat(balance.amount).toLocaleString('en-IN')}</td>
                        <td>${typeBadge}</td>
                        <td>${asOfDate}</td>
                        <td class="text-center">
                            <a href="${editUrl}" class="btn btn-sm btn-warning me-2" title="Edit"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${balance.id}" title="Delete"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
            
            // --- PAGINATION SCRIPT ---
            let paginationHtml = '';
            if (res.last_page > 1) {
                for (let i = 1; i <= res.last_page; i++) {
                    paginationHtml += `<li class="page-item ${i === res.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
            }
            $('#pagination').html(paginationHtml);
        });
    }

    $('#searchInput').on('keyup', function () { searchTerm = $(this).val(); currentPage = 1; fetchData(); });
    $(document).on('click', '.page-link', function (e) { e.preventDefault(); currentPage = $(this).data('page'); fetchData(); });
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?', icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.destroy(id), method: 'DELETE', data: { _token: routes.csrf },
                    success: function() {
                        Swal.fire('Deleted!', 'The opening balance has been deleted.', 'success');
                        fetchData();
                    }
                });
            }
        });
    });
    fetchData();
});
</script>
@endsection