@extends('admin.master.master')
@section('title', 'Accounting Settings')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Accounting Settings</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search by transaction type..." aria-label="Search">
                </form>
                <a href="{{ route('accounting-settings.create') }}" class="btn text-white" style="background-color: var(--primary-color);">
                    <i data-feather="plus" class="me-1"></i> Add New Setting
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
                                <th>Transaction Type</th>
                                <th>Debit Account</th>
                                <th>Credit Account</th>
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
        fetch: "{{ route('ajax.accounting-settings.data') }}",
        destroy: id => `{{ url('accounting-settings') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };
    const loaderRow = `<tr><td colspan="5" class="text-center"><div class="spinner-border"></div></td></tr>`;

    function fetchData() {
        $('#tableBody').html(loaderRow);
        $.get(routes.fetch, { page: currentPage, search: searchTerm }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="5" class="text-center">No settings found.</td></tr>';
            } else {
                res.data.forEach((setting, index) => {
                    const editUrl = `{{ url('accounting-settings') }}/${setting.id}/edit`;
                    rows += `<tr>
                        <td>${res.from + index}</td>
                        <td>${setting.transaction_type}</td>
                        <td>${setting.debit_account ? setting.debit_account.name : 'N/A'}</td>
                        <td>${setting.credit_account ? setting.credit_account.name : 'N/A'}</td>
                        <td class="text-center">
                            <a href="${editUrl}" class="btn btn-sm btn-warning me-2" title="Edit"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${setting.id}" title="Delete"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
            
            // --- PAGINATION SCRIPT ---
               // Pagination
            var paginationHtml = '';
            if (res.last_page > 1) {
                paginationHtml += `
                    <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="1">First</a>
                    </li>
                    <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${res.current_page - 1}">Prev</a>
                    </li>`;

                const start = Math.max(1, res.current_page - 2);
                const end = Math.min(res.last_page, res.current_page + 2);

                for (var i = start; i <= end; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === res.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
                }

                paginationHtml += `
                    <li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${res.current_page + 1}">Next</a>
                    </li>
                    <li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${res.last_page}">Last</a>
                    </li>`;
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
                        Swal.fire('Deleted!', 'The setting has been deleted.', 'success');
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