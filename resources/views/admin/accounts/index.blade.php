@extends('admin.master.master')
@section('title', 'Chart of Accounts')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Chart of Accounts</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search by name or code..." aria-label="Search">
                </form>
                <a href="{{ route('accounts.create') }}" class="btn text-white" style="background-color: var(--primary-color);">
                    <i data-feather="plus" class="me-1"></i> Add New Account
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
                                <th>Name</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Parent Account</th>
                                <th>Status</th>
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
        fetch: "{{ route('ajax.accounts.data') }}",
        destroy: id => `{{ url('accounts') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };
    const loaderRow = `<tr><td colspan="7" class="text-center"><div class="spinner-border"></div></td></tr>`;

    function fetchData() {
        $('#tableBody').html(loaderRow);
        $.get(routes.fetch, { page: currentPage, search: searchTerm }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="7" class="text-center">No accounts found.</td></tr>';
            } else {
                res.data.forEach((account, index) => {
                    const editUrl = `{{ url('accounts') }}/${account.id}/edit`;
                    const statusBadge = account.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    rows += `<tr>
                        <td>${res.from + index}</td>
                        <td>${account.name}</td>
                        <td>${account.code || 'N/A'}</td>
                        <td>${account.type}</td>
                        <td>${account.parent ? account.parent.name : 'N/A'}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">
                            <a href="${editUrl}" class="btn btn-sm btn-warning me-2" title="Edit"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${account.id}" title="Delete"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
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