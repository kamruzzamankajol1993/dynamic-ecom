@extends('admin.master.master')
@section('title', 'Offer Name')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Offer Name List</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search offers..." aria-label="Search">
                </form>
                <a href="{{ route('bundle-offer.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;"><i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Create New Offer</a>
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
                                <th>Image</th>
                                <th class="sortable" data-column="name">Name</th>
                                <th class="sortable" data-column="title">Title</th>
                                <th>Dates</th>
                                <th class="sortable" data-column="status">Status</th>
                                <th>Free Delivery</th>
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
                <div class="text-muted" id="pagination-info"></div>
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
        destroy: id => `{{ url('bundle-offer') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };


    function fetchData() {
        $.get("{{ route('ajax.bundle-offer.data') }}", {
            page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection
        }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="7" class="text-center">No offers found.</td></tr>';
            } else {
                res.data.forEach((offer, i) => {
                    const statusBadge = offer.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    const editUrl = `{{ url('bundle-offer') }}/${offer.id}/edit`;
                  const defaultImageUrl = `https://placehold.co/50x50/eee/ccc?text=No+Img`;
                    const imageUrl = offer.image ? `{{ asset('/') }}public/${offer.image}` : defaultImageUrl;
                    // START: NEW BADGE
                    const freeDeliveryBadge = offer.is_free_delivery == 1 
                        ? '<span class="badge bg-success">Yes</span>' 
                        : '<span class="badge bg-secondary">No</span>';
                    // END: NEW BADGE
                    // Format dates without time
                    const startDate = offer.startdate ? new Date(offer.startdate).toLocaleDateString('en-CA') : 'N/A'; // en-CA gives YYYY-MM-DD format
                    const endDate = offer.enddate ? new Date(offer.enddate).toLocaleDateString('en-CA') : 'N/A';
                    
                    // Correctly calculate the serial number
                    const serialNumber = (res.current_page - 1) * res.per_page + i + 1;

                    rows += `<tr>
                        <td>${serialNumber}</td>
                        <td><img src="${imageUrl}" width="50" class="img-thumbnail"></td>
                        <td>${offer.name}</td>
                        <td>${offer.title}</td>
                        <td>${startDate} - ${endDate}</td>
                        <td>${statusBadge}</td>
                        <td>${freeDeliveryBadge}</td>
                        <td>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                           

                             <form action="${routes.destroy(offer.id)}" method="POST" class="d-inline">
                            <input type="hidden" name="_token" value="${routes.csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>
                        </form>

                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
            // Check if 'from' and 'to' exist before displaying
            const paginationInfo = res.from && res.to ? `Showing ${res.from} to ${res.to} of ${res.total} entries` : '';
            $('#pagination-info').text(paginationInfo);


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

    fetchData(); // Initial data load
});
</script>
@endsection
