@extends('admin.master.master')
@section('title', 'Product Deals')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Product Deal List</h2>
            <div class="d-flex align-items-center">
                 <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search deals..." aria-label="Search">
                </form>
                <a href="{{ route('offer-product.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;"><i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Create New Deal</a>
            </div>
        </div>
        @include('flash_message')
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th class="sortable" data-column="title">Deal Title</th>
                                <th>Main Offer Name</th>
                                <th class="sortable" data-column="buy_quantity">Buy/Get</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            {{-- Data will be loaded via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div id="pagination-info" class="text-muted"></div>
                <nav>
                    <ul class="pagination justify-content-center mb-0" id="pagination"></ul>
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

    function fetchData() {
        $.get("{{ route('ajax.offer-product.data') }}", {
            page: currentPage,
            search: searchTerm,
            sort: sortColumn,
            direction: sortDirection
        }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="5" class="text-center">No product deals found.</td></tr>';
            } else {
                res.data.forEach((deal, i) => {
                    const showUrl = `{{ url('offer-product') }}/${deal.id}`;
                    const editUrl = `{{ url('offer-product') }}/${deal.id}/edit`;

                    rows += `<tr>
                        <td>${res.from + i}</td>
                        <td>${deal.title}</td>
                        <td>${deal.bundle_offer ? deal.bundle_offer.name : 'N/A'}</td>
                        <td>Buy ${deal.buy_quantity} / Get ${deal.get_quantity}</td>
                        <td>
                            <a href="${showUrl}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${deal.id}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
            $('#pagination-info').text(`Showing ${res.from} to ${res.to} of ${res.total} entries`);

            // Pagination logic
            let paginationHtml = '';
            if (res.last_page > 1) {
                for (let i = 1; i <= res.last_page; i++) {
                     paginationHtml += `<li class="page-item ${i === res.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
            }
            $('#pagination').html(paginationHtml);
        });
    }

    // Event Handlers
    $('#searchInput').on('keyup', function () {
        searchTerm = $(this).val();
        currentPage = 1; // Reset to first page
        fetchData();
    });

    $(document).on('click', '.sortable', function (e) {
        e.preventDefault();
        let col = $(this).data('column');
        sortDirection = sortColumn === col ? (sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
        sortColumn = col;
        fetchData();
    });

    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        currentPage = $(this).data('page');
        fetchData();
    });

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
                    url: `{{ url('offer-product') }}/${id}`,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        fetchData(); // Refresh the table
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });

    fetchData(); // Initial data load
});
</script>
@endsection
