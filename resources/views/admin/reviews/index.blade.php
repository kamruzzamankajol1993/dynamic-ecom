@extends('admin.master.master')
@section('title', 'Product Reviews')
@section('css')
<style>
    .loader-row { text-align: center; }
    .rating-stars .fa-star { color: #ffc107; }
    .rating-stars .fa-star-o { color: #e4e5e9; }
</style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Product Reviews</h2>
            <form class="d-flex" role="search">
                <input class="form-control" id="searchInput" type="search" placeholder="Search reviews..." aria-label="Search">
            </form>
        </div>
        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Rating</th>
                                <th class="sortable" data-column="published">Status</th>
                                <th class="sortable" data-column="created_at">Date</th>
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
        fetch: "{{ route('ajax.review.data') }}",
        destroy: id => `{{ url('review') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };

    const loaderRow = `<tr class="loader-row"><td colspan="7"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></td></tr>`;

    function renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="fa ${i <= rating ? 'fa-star' : 'fa-star-o'}"></i>`;
        }
        return `<span class="rating-stars">${stars}</span>`;
    }

    function fetchData() {
        $('#tableBody').html(loaderRow);
        $.get(routes.fetch, { page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="7" class="text-center">No reviews found.</td></tr>';
            } else {
                res.data.forEach((review, i) => {
                    const statusBadge = review.published == 1 ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-warning">Pending</span>';
                    const showUrl = `{{ url('review') }}/${review.id}`;
                    const editUrl = `{{ url('review') }}/${review.id}/edit`;
                    const reviewDate = new Date(review.created_at).toLocaleDateString('en-GB');

                    rows += `<tr>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td>${review.product ? review.product.name : 'N/A'}</td>
                        <td>${review.customer ? review.customer.name : 'N/A'}</td>
                        <td>${renderStars(review.rating)}</td>
                        <td>${statusBadge}</td>
                        <td>${reviewDate}</td>
                        <td>
                            <a href="${showUrl}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${review.id}"><i class="fa fa-trash"></i></button>
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
                        Swal.fire('Deleted!', 'The review has been deleted.', 'success');
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