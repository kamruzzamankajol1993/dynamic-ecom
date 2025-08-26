@extends('admin.master.master')

@section('title')

Service Management | {{ $ins_name }}

@endsection


@section('css')
<style>

    .table-bordered {
    border: 1px solid #ccc;
    border-collapse: collapse;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #ccc;
    padding: 8px 12px;
    text-align: left;
}
    </style>
<style>
      
        /* Pagination Styling */
        .pagination-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 1.5rem;
        }
        .pagination-container .page-item {
            margin: 0 0.25rem;
        }
        .pagination-container .page-link {
            border-radius: 0; /* Removed roundness */
            color: #4f46e5; /* Primary color */
            border-color: #e0e0e0;
            transition: all 0.2s ease-in-out;
            box-shadow: none; /* Removed shadow */
            padding: 0.5rem 0.75rem; /* Adjust padding */
        }
        .pagination-container .page-link:hover {
            background-color: #e0e0e0;
            border-color: #e0e0e0;
            box-shadow: none; /* Removed shadow */
        }
        .pagination-container .page-item.active .page-link {
            background-color: #4f46e5; /* Primary color */
            border-color: #4f46e5;
            color: #fff;
            font-weight: 600; /* Bolder active link */
            box-shadow: none; /* Removed shadow */
        }
        .pagination-container .page-item.disabled .page-link {
            color: #b0b0b0;
            pointer-events: none;
            background-color: #f8f9fa;
            border-color: #e0e0e0;
            box-shadow: none;
        }

   


    /* Image thumbnails in table */
    .table img.square-img { /* New class for square images */
        border-radius: 0; /* Removed roundness */
        border: 2px solid #ddd;
        box-shadow: none; /* Removed shadow */
    }

   

    /* Breadcrumb styling adjustments */
    .breadcrumb-with-buttons {
        margin-bottom: 24px;
        justify-content: space-between;
        align-items: center;
    }

    .breadcrumb {
        margin-bottom: 0 !important;
    }
    .breadcrumb li a, .breadcrumb li span {
        font-size: 15px;
    }
    .breadcrumb li .ph-caret-right {
        color: #adb5bd;
    }
    .text-main-600 {
        color: #4f46e5;
        font-weight: 600;
    }
    .hover-text-main-600:hover {
        color: #4f46e5;
    }

    /* Card styling */
    .card {
        border: none;
        border-radius: 0; /* Removed roundness */
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        transition: all 0.3s ease-in-out;
    }
    .card:hover {
        box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.15);
    }
    .card-header {
        background-color: #4f46e5;
        color: #fff;
        padding: 1rem 1.5rem;
        border-top-left-radius: 0; /* Removed roundness */
        border-top-right-radius: 0; /* Removed roundness */
        font-weight: 600;
    }

    /* Input group and select styling */
    .input-group .form-control,
    .input-group .btn {
        border-color: #ced4da;
        box-shadow: none !important;
        border-radius: 0 !important; /* Removed roundness */
    }
    .input-group .form-control:focus,
    .input-group .btn:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.25rem rgba(0,123,255,.25) !important;
    }
    /* Specific rounded-start/end removed as overall input-group rules apply */

    .flex-align {
        display: flex;
        align-items: center;
    }
   
    .text-13 { font-size: 0.8125rem; }
    .text-15 { font-size: 0.9375rem; }
    .text-lg { font-size: 1.125rem; }

</style>

@endsection


@section('body')

<div class="dashboard-body">

    <div class="breadcrumb-with-buttons mb-4 flex-between flex-wrap gap-8">
        <!-- Breadcrumb Start -->
        <div class="breadcrumb mb-0">
            <ul class="flex-align gap-4">
                <li><a href="{{route('home')}}" class="text-secondary fw-normal text-15 hover-text-main-600">Home</a></li>
                <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                <li><span class="text-main-600 fw-normal text-15">Service Management</span></li>
            </ul>
        </div>
        <!-- Breadcrumb End -->

        <!-- Breadcrumb Right Start -->
        <div class="flex-align gap-8 flex-wrap">
            <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-0 ps-20 focus-border-main-600 bg-white"> {{-- Removed rounded-4 --}}
               
            </div>

            <div class="flex-align text-gray-500 text-13">
                @if (Auth::user()->can('serviceAdd'))
                    <a href="{{ route('service.create') }}" type="button" class="btn btn-primary "> {{-- Changed rounded-pill to square-corners --}}
                        <i class="fas fa-plus me-2"></i> Add New Service
                    </a>
                @endif
            </div>
        </div>
        <!-- Breadcrumb Right End -->
    </div>


    <div class="card overflow-hidden shadow-lg rounded-3"> {{-- Changed rounded-3 to rounded-0 --}}
        <div class="card-body">
            @include('flash_message')

            {{-- Search input moved here --}}
            <div class="d-flex justify-content-end mb-4">
                <div class="input-group" style="width: 300px;">
                    <input type="text" id="customerSearch" class="form-control rounded-0" placeholder="Search service..."> {{-- Changed rounded-start to rounded-0 --}}
                    {{-- Search button removed --}}
                </div>
            </div>
            <!-- add table here start --->
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col" style="">S.No.</th> {{-- Added Serial Number column --}}
                            <th scope="col">Image</th>
                         
                            <th scope="col" class="sortable" data-sort="phone">Title<i class="fas fa-sort float-end"></i></th>
                            <th scope="col" class="sortable" data-sort="email">Price<i class="fas fa-sort float-end"></i></th>
                            <th scope="col" class="sortable" data-sort="email">Status<i class="fas fa-sort float-end"></i></th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                        {{-- Customer data will be loaded here by JavaScript --}}
                    </tbody>
                </table>
            </div>
            <!-- add table here end --->

            {{-- Pagination Controls --}}
            <div class="pagination-container">
                <ul class="pagination mb-0" id="paginationControls">
                    {{-- Pagination links will be generated here by JavaScript --}}
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')


<script>
    $(document).ready(function() {
        let currentPage = 1;
        const rowsPerPage = 10; // This should match the default perPage in your controller
        let currentSearchTerm = '';
        let currentSortBy = 'id'; // Default sort column
        let currentSortOrder = 'desc'; // Default sort order

      
        const customerIndexUrl = @json(route('service.index')); // Route to fetch all services
        const customerShowUrlPattern = @json(route('service.show', ['service' => ':id']));
        const customerEditUrlPattern = @json(route('service.edit', ['service' => ':id']));
        const customerDeleteUrlPattern = @json(route('service.destroy', ['service' => ':id']));


        // Function to load customer data from the controller via AJAX
        function loadCustomerData() {
            const params = {
                page: currentPage,
                per_page: rowsPerPage,
                search: currentSearchTerm,
                // You can uncomment and use these if you implement server-side sorting as well
                // sort_by: currentSortBy,
                // sort_order: currentSortOrder
            };

            $.ajax({
                url: customerIndexUrl, // Your customers.index route
                type: 'GET',
                data: params,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Laravel detects AJAX with this header
                },
                success: function(response) {
                    console.log("Customers data loaded:", response);
                    // Update current page, total, and last page from server response
                    currentPage = response.current_page;
                    const totalPages = response.last_page;

                    renderTable(response.data); // Pass only the current page's data
                    renderPagination(totalPages); // Pass total pages for pagination controls
                },
                error: function(xhr, status, error) {
                    console.error("Error loading customer data:", status, error, xhr.responseText);
                    $('#customerTableBody').html(`<tr><td colspan="6" class="text-center text-danger">Failed to load customer data. Please try again.</td></tr>`);
                    // Changed colspan to 6 because we added S.No. column
                }
            });
        }

        // Function to render table rows
        function renderTable(customers) {
            const $tableBody = $('#customerTableBody');
            $tableBody.empty(); // Clear existing rows

            if (customers.length === 0) {
                $tableBody.append(`<tr><td colspan="6" class="text-center">No customers found.</td></tr>`);
                // Changed colspan to 6 because we added S.No. column
                return;
            }

            let serialNumber = (currentPage - 1) * rowsPerPage + 1; // Initialize serial number

            customers.forEach(customer => {
                // Construct URLs using the patterns
                const showUrl = customerShowUrlPattern.replace(':id', customer.id);
                const editUrl = customerEditUrlPattern.replace(':id', customer.id);
                const deleteUrl = customerDeleteUrlPattern.replace(':id', customer.id);

                // Ensure image path is correctly asset()'d if stored as relative path
                const customerImage = customer.image ? `{{asset('/')}}${customer.image}` : 'https://placehold.co/50x50/DDDDDD/333333?text=IMG';


                const row = `
                    <tr>
                        <td>${serialNumber++}</td> {{-- Display and increment serial number --}}
                        <td><img src="${customerImage}" alt="${customer.title}" class="square-img" style="width: 50px; height: 50px; object-fit: cover;"></td> {{-- Changed rounded-circle to square-img --}}
                        
                        <td>${customer.title || 'N/A'}</td>
                        <td>${customer.price}</td>
                        <td>${customer.status}</td>
                        <td>
                            <a href="${showUrl}" class="btn btn-info btn-sm  btn-custom-sm" title="View Details"><i class="fas fa-eye"></i></a> {{-- Changed rounded-pill to square-corners --}}
                            <a href="${editUrl}" class="btn btn-warning btn-sm  btn-custom-sm" title="Edit"><i class="fas fa-edit"></i></a> {{-- Changed rounded-pill to square-corners --}}
                            <button type="button" class="btn btn-danger btn-sm btn-custom-sm delete-btn" data-id="${customer.id}" data-delete-url="${deleteUrl}" title="Delete"><i class="fas fa-trash"></i></button> {{-- Changed rounded-pill to square-corners --}}
                        </td>
                    </tr>
                `;
                $tableBody.append(row);
            });
        }

        // Function to render pagination controls based on server response
        function renderPagination(totalPages) {
            const $paginationControls = $('#paginationControls');
            $paginationControls.empty();

            if (totalPages <= 1) {
                return; // Hide pagination if only one page
            }

            // Previous button
            $paginationControls.append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>
            `);

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                $paginationControls.append(`
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }

            // Next button
            $paginationControls.append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>
            `);

            // Add click handler for pagination links
            $paginationControls.off('click', '.page-link').on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                if (page > 0 && page <= totalPages && page !== currentPage) {
                    currentPage = page;
                    loadCustomerData(); // Load data for the new page
                }
            });
        }

        // Search functionality
        let searchTimeout;
        $('#customerSearch').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearchTerm = searchTerm;
                currentPage = 1; // Reset to first page on new search
                loadCustomerData(); // Trigger server-side search
            }, 300); // Debounce search input
        });

        // Sorting functionality (server-side sorting)
        $('th.sortable').on('click', function() {
            const sortBy = $(this).data('sort');
            const currentOrder = $(this).data('sort-order');
            const newSortOrder = (currentOrder === 'asc' || !currentOrder) ? 'desc' : 'asc';

            // Update global sort parameters
            currentSortBy = sortBy;
            currentSortOrder = newSortOrder;

            // Reset icons for all sortable columns
            $('th.sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');

            // Update icon for the clicked column
            $(this).find('i').removeClass('fa-sort').addClass(newSortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
            $(this).data('sort-order', newSortOrder); // Store new sort order

            currentPage = 1; // Reset to first page on new sort
            loadCustomerData(); // Reload data with new sort parameters
        });


        // Delete functionality with SweetAlert
        $(document).on('click', '.delete-btn', function() {
            const customerId = $(this).data('id');
            const deleteUrl = $(this).data('delete-url');
            const customerName = $(this).closest('tr').find('td:nth-child(3)').text(); // Changed index to 3 for name

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete customer "${customerName}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                'Customer has been deleted.',
                                'success'
                            ).then(() => {
                                // Reload data from the server after deletion
                                loadCustomerData();
                            });
                        },
                        error: function(xhr) {
                            console.error("Error deleting customer:", xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the customer.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

       
      
        // Initial load of data from backend
        loadCustomerData();
    });
</script>
@endsection
