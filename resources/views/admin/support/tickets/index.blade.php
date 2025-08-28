@extends('admin.master.master')
@section('title', 'Support Tickets & FAQs')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Support Tickets & FAQs</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search questions..." aria-label="Search">
                </form>
                <a href="{{ route('support-tickets.create') }}" class="btn text-white" style="background-color: var(--primary-color);"><i data-feather="plus" class="me-1"></i> Add New</a>
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
                                <th class="sortable" data-column="question">Question</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th class="sortable" data-column="status">Status</th>
                                <th>Action</th>
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
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';
    var routes = {
        fetch: "{{ route('ajax.support-tickets.data') }}",
        destroy: id => `{{ url('support-tickets') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };
    const loaderRow = `<tr class="loader-row"><td colspan="6"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></td></tr>`;

    function fetchData() {
        $('#tableBody').html(loaderRow);
        $.get(routes.fetch, { page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="6" class="text-center">No tickets found.</td></tr>';
            } else {
                res.data.forEach((ticket, i) => {
                    const statusBadge = ticket.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    const typeBadge = ticket.is_faq == 1 ? '<span class="badge bg-info">FAQ</span>' : '<span class="badge bg-secondary">Ticket</span>';
                    const showUrl = `{{ url('support-tickets') }}/${ticket.id}`;
                    const editUrl = `{{ url('support-tickets') }}/${ticket.id}/edit`;
                    
                    rows += `<tr>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td>${ticket.question}</td>
                        <td>${ticket.category ? ticket.category.name : 'N/A'}</td>
                        <td>${typeBadge}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="${showUrl}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${ticket.id}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
            // Pagination logic here (same as previous modules)
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
            title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.destroy(id), method: 'DELETE', data: { _token: routes.csrf },
                    success: function() {
                        Swal.fire('Deleted!', 'The ticket has been deleted.', 'success');
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