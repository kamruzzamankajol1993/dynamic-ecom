@extends('admin.master.master')
@section('title', 'Product Deals')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Product Deal List</h2>
            <div class="d-flex align-items-center">
                <div id="bulkActionContainer" style="display: none;" class="ms-2">
    <div class="dropdown">
        <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Bulk Update Customization
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="bulkUpdateCustom(1)">Enable Customization</a></li>
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="bulkUpdateCustom(0)">Disable Customization</a></li>
        </ul>
    </div>
</div>
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
        <th><input type="checkbox" id="selectAll"></th> <th>Sl</th>
        <th class="sortable" data-column="title">Deal Title</th>
        <th>Custom</th> <th>Main Offer Name</th>
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
    // ১. গ্লোবাল ভেরিয়েবল এবং স্টেট ম্যানেজমেন্ট
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        destroy: id => `{{ url('offer-product') }}/${id}`,
        csrf: "{{ csrf_token() }}",
        bulkUpdate: "{{ route('ajax.offer-product.bulk-custom-update') }}"
    };

    // ২. ডাটা ফেচিং ফাংশন (উইন্ডো স্কোপে রাখা হয়েছে যাতে অন্য ফাংশন থেকে কল করা যায়)
    window.fetchData = function() {
        $.get("{{ route('ajax.offer-product.data') }}", {
            page: currentPage,
            search: searchTerm,
            sort: sortColumn,
            direction: sortDirection
        }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="7" class="text-center">No product deals found.</td></tr>';
            } else {
                res.data.forEach((deal, i) => {
                    const showUrl = `{{ url('offer-product') }}/${deal.id}`;
                    const editUrl = `{{ url('offer-product') }}/${deal.id}/edit`;
                    const imageHtml = deal.image 
                        ? `<img src="{{ asset('') }}${deal.image}" width="50" class="img-thumbnail">` 
                        : '';
                    const customBadge = deal.is_custom 
                        ? '<span class="badge bg-info text-white">Yes</span>' 
                        : '<span class="badge bg-secondary text-white">No</span>';

                    rows += `<tr>
                        <td><input type="checkbox" class="deal-checkbox" value="${deal.id}"></td>
                        <td>${res.from + i}</td>
                        <td>${imageHtml} ${deal.title}</td>
                        <td>${customBadge}</td>
                        <td>${deal.bundle_offer ? deal.bundle_offer.name : 'N/A'}</td>
                        <td>Buy ${deal.buy_quantity} / Get ${deal.get_quantity}</td>
                        <td>
                            <a href="${showUrl}" class="btn btn-sm btn-success"><i class="fa fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                            <form action="${routes.destroy(deal.id)}" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="${routes.csrf}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>
                            </form>
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
            
            // ডাটা লোড হওয়ার পর সিলেক্ট অল রিসেট এবং বাটন চেক করা
            $('#selectAll').prop('checked', false);
            toggleBulkButton();
        });
    }

    // ৩. বাল্ক আপডেট ফাংশন (গ্লোবাল স্কোপে যাতে HTML onclick কাজ করে)
    window.bulkUpdateCustom = function(status) {
        let ids = $('.deal-checkbox:checked').map(function() { return $(this).val(); }).get();
        
        if (ids.length === 0) {
            Swal.fire('Warning', 'Please select at least one deal.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `Update customization status for ${ids.length} deals?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Update',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.bulkUpdate,
                    method: 'POST',
                    data: {
                        _token: routes.csrf,
                        ids: ids,
                        status: status
                    },
                    success: function(res) {
                        Swal.fire('Updated!', res.message, 'success');
                        window.fetchData(); // টেবিল রিফ্রেশ
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong while updating.', 'error');
                    }
                });
            }
        });
    }

    // ৪. বাটন শো/হাইড লজিক
    function toggleBulkButton() {
        let selectedCount = $('.deal-checkbox:checked').length;
        if (selectedCount > 0) {
            $('#bulkActionContainer').fadeIn();
        } else {
            $('#bulkActionContainer').fadeOut();
        }
    }

    // ৫. ইভেন্ট লিসেনারস (Document Ready এর ভেতরে)
    $(document).ready(function() {
        
        // সিলেক্ট অল ফাংশনালিটি
        $(document).on('change', '#selectAll', function() {
            $('.deal-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkButton();
        });

        // সিঙ্গেল চেক বক্স চেঞ্জ
        $(document).on('change', '.deal-checkbox', function() {
            if ($('.deal-checkbox:checked').length === $('.deal-checkbox').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
            toggleBulkButton();
        });

        // সার্চ লজিক
        $('#searchInput').on('keyup', function () {
            searchTerm = $(this).val();
            currentPage = 1;
            window.fetchData();
        });

        // সর্টিং লজিক
        $(document).on('click', '.sortable', function (e) {
            e.preventDefault();
            let col = $(this).data('column');
            sortDirection = sortColumn === col ? (sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
            sortColumn = col;
            window.fetchData();
        });

        // পেজিনেশন ক্লিক
        $(document).on('click', '.page-link', function (e) {
            e.preventDefault();
            currentPage = $(this).data('page');
            window.fetchData();
        });

        // ডিলিট বাটন
        $(document).on('click', '.btn-delete', function () {
            const deleteButton = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the selected deal!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteButton.closest('form').submit();
                }
            });
        });

        // ইনিশিয়াল ডাটা লোড
        window.fetchData();
    });
</script>
@endsection
