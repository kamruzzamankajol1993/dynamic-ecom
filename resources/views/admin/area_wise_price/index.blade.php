@extends('admin.master.master')
@section('title', 'Area Wise Price List')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Area Wise Price List</h2>
            <a href="{{ route('area-wise-price.create') }}" class="btn text-white" style="background-color: var(--primary-color);">
                <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New Entry
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                @include('flash_message')

                <div class="d-flex justify-content-end mb-3">
                    <div class="col-md-4">
                         <input type="text" class="form-control" id="searchInput" placeholder="Search by label or area...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Label</th>
                                <th>Area</th>
                                <th>Delivery Days</th>
                                <th>Price</th>
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
                <div class="text-muted"></div>
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
    var currentPage = 1,
        searchQuery = '';

    var routes = {
        fetch: "{{ route('area-wise-price.data') }}",
        // The destroy route will be built dynamically
        baseUrl: "{{ url('area-wise-price') }}",
        csrf: "{{ csrf_token() }}"
    };

    // --- Debounce function to delay execution ---
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    function fetchData() {
        const loaderHtml = `
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>`;
        $('#tableBody').html(loaderHtml);

        $.get(routes.fetch, {
            page: currentPage,
            search: searchQuery
        }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="6" class="text-center">No records found.</td></tr>';
            } else {
                res.data.forEach((item, i) => {
                    const editUrl = `${routes.baseUrl}/${item.id}/edit`;
                    const priceFormatted = parseFloat(item.price).toFixed(2);
                    
                    rows += `<tr>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td>${item.label}</td>
                        <td>${item.area}</td>
                        <td>${item.days}</td>
                        <td>${priceFormatted}</td>
                        <td>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);

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

    // --- Event handlers ---
    $('#searchInput').on('keyup', debounce(function() {
        searchQuery = $(this).val();
        currentPage = 1;
        fetchData();
    }, 400));

    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        currentPage = $(this).data('page');
        fetchData();
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        const destroyUrl = `${routes.baseUrl}/${id}`;

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: destroyUrl,
                    method: 'POST', // Use POST for deletion with method spoofing
                    data: {
                        _token: routes.csrf,
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        fetchData(); 
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