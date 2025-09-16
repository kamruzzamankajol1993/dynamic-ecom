@extends('admin.master.master')

@section('title', 'Shareholder List')

@section('css')
<style>
    .loader-row { text-align: center; }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Shareholder List</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search by Name or Email..." aria-label="Search">
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th style="width:5%">SL</th>
                                <th style="width:10%">Image</th>
                                <th class="sortable" data-column="name">Name</th>
                                <th class="sortable" data-column="email">Email</th>
                                <th>Branch</th>
                                <th style="width:15%">Actions</th>
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
        fetch: "{{ route('ajax.shareholders.data') }}",
        show: id => `{{ url('users') }}/${id}`,
        edit: id => `{{ url('users') }}/${id}/edit`, // Links to the standard user edit page
        destroy: id => `{{ url('users') }}/${id}`, // Deletes the standard user record
        csrf: "{{ csrf_token() }}"
    };

    const loaderRow = `<tr class="loader-row"><td colspan="6"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></td></tr>`;

    function fetchData() {
        $('#tableBody').html(loaderRow);
        $.get(routes.fetch, { page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="6" class="text-center">No shareholders found.</td></tr>';
            } else {
                res.data.forEach((user, index) => {
                    const profile = user.image 
                        ? `{{ asset('/') }}${user.image}` 
                        : `{{ asset('/') }}public/No_Image_Available.jpg`;

                    rows += `<tr>
                        <td>${res.from + index}</td>
                        <td><img src="${profile}" class="rounded-circle" width="40" height="40" alt="${user.name}"></td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.branch_name || 'N/A'}</td>
                        <td>
                             ${res.can_show ? `<a href="${routes.show(user.id)}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>` : ''}
                            ${res.can_edit ? `<a href="${routes.edit(user.id)}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>` : ''}
                            ${res.can_delete ? `<button class="btn btn-sm btn-danger btn-delete" data-id="${user.id}"><i class="fa fa-trash"></i></button>` : ''}
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
    
    $(document).on('click', '.sortable', function () {
        const col = $(this).data('column');
        sortDirection = (sortColumn === col && sortDirection === 'asc') ? 'desc' : 'asc';
        sortColumn = col;
        fetchData();
    });

    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            fetchData();
        }
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the user.",
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
                        Swal.fire('Deleted!', 'The user has been deleted.', 'success');
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