<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for modals
    $('.select2-add').select2({ dropdownParent: $('#addModal') });
    $('.select2-edit').select2({ dropdownParent: $('#editModal') });

    // Initialize date pickers
    $("#add_expires_at, #edit_expires_at").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
    });

    const tableBody = $('#coupon-table-body');
    const paginationContainer = $('#pagination-container');
    const paginationInfo = $('#pagination-info');
    const spinner = $('.loading-spinner');
    const searchInput = $('#search-input');
    let searchTimeout;

    function fetchCoupons(page = 1, searchQuery = '') {
        spinner.show();
        tableBody.empty();

        let url = `{{ route('ajax.coupons.data') }}?page=${page}`;
        if (searchQuery) {
            url += `&search=${searchQuery}`;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                spinner.hide();
                renderTable(response);
                renderPagination(response);
                paginationInfo.text(`Showing ${response.from} to ${response.to} of ${response.total} entries`);
            },
            error: function() {
                spinner.hide();
                tableBody.html('<tr><td colspan="8" class="text-center text-danger">Failed to load data.</td></tr>');
                paginationInfo.text('');
            }
        });
    }

    function renderTable(response) {
        const coupons = response.data;
        if (coupons.length === 0) {
            tableBody.html('<tr><td colspan="8" class="text-center">No coupons found.</td></tr>');
            return;
        }

        const startSl = (response.current_page - 1) * response.per_page;

        coupons.forEach((coupon, index) => {
            const sl = startSl + index + 1;
            const statusBadge = coupon.status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            const value = coupon.type === 'percent' ? `${coupon.value}%` : new Intl.NumberFormat().format(coupon.value);
            const usage = `${coupon.times_used} / ${coupon.usage_limit || '∞'}`;
            const expiresAt = coupon.expires_at ? new Date(coupon.expires_at).toLocaleDateString('en-GB') : 'Never';
 // Define the showUrl for the new button
            let showUrl = "{{ route('coupon.show', ':id') }}";
            showUrl = showUrl.replace(':id', coupon.id);
            const row = `
                <tr>
                    <td>${sl}</td>
                    <td><strong>${coupon.code}</strong></td>
                    <td>${coupon.type.charAt(0).toUpperCase() + coupon.type.slice(1)}</td>
                    <td>${value}</td>
                    <td>${usage}</td>
                    <td>${expiresAt}</td>
                    <td>${statusBadge}</td>
                    <td>
                          <a href="${showUrl}" class="btn btn-sm btn-secondary"><i class="fa fa-eye"></i></a>
                        <button type="button" class="btn btn-sm btn-info edit-btn" data-id="${coupon.id}"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${coupon.id}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `;
            tableBody.append(row);
        });
    }

    function renderPagination(response) {
        paginationContainer.empty();
        const currentPage = response.current_page;
        const lastPage = response.last_page;

        if (lastPage <= 1) return;

        // Previous button
        let prevDisabled = currentPage === 1 ? 'disabled' : '';
        paginationContainer.append(`<li class="page-item ${prevDisabled}"><a class="page-link" data-page="${currentPage - 1}">‹</a></li>`);

        // Page number logic
        const pagesToShow = 5;
        let startPage = Math.max(1, currentPage - Math.floor(pagesToShow / 2));
        let endPage = Math.min(lastPage, startPage + pagesToShow - 1);

        if (endPage - startPage + 1 < pagesToShow) {
            startPage = Math.max(1, endPage - pagesToShow + 1);
        }

        if (startPage > 1) {
            paginationContainer.append('<li class="page-item"><a class="page-link" data-page="1">1</a></li>');
            if (startPage > 2) {
                paginationContainer.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationContainer.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" data-page="${i}">${i}</a></li>`);
        }

        if (endPage < lastPage) {
            if (endPage < lastPage - 1) {
                paginationContainer.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
            }
            paginationContainer.append(`<li class="page-item"><a class="page-link" data-page="${lastPage}">${lastPage}</a></li>`);
        }

        // Next button
        let nextDisabled = currentPage === lastPage ? 'disabled' : '';
        paginationContainer.append(`<li class="page-item ${nextDisabled}"><a class="page-link" data-page="${currentPage + 1}">›</a></li>`);
    }
    
    // Add Form Submission
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("coupon.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addModal').modal('hide');
                $('#addForm')[0].reset();
                $('.select2-add').val(null).trigger('change');
                Swal.fire('Success!', response.success, 'success');
                fetchCoupons();
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Please check the form for errors.', 'error');
            }
        });
    });

    // Edit Button Click
    tableBody.on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let url = "{{ route('coupon.edit', ':id') }}";
        url = url.replace(':id', id);

        $.get(url, function(data) {
            $('#edit_id').val(id);
            $('#edit_code').val(data.code);
            $('#edit_type').val(data.type);
            $('#edit_value').val(data.value);
            $('#edit_min_amount').val(data.min_amount);
            $('#edit_usage_limit').val(data.usage_limit);
            $('#edit_expires_at').val(data.expires_at ? data.expires_at.split('T')[0] : '');
            $('#edit_user_type').val(data.user_type);
            $('#edit_status').val(data.status ? 1 : 0);
            $('#edit_product_ids').val(data.product_ids || []).trigger('change');
            $('#edit_category_ids').val(data.category_ids || []).trigger('change');
            $('#editModal').modal('show');
        });
    });

    // Edit Form Submission
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let url = "{{ route('coupon.update', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#editModal').modal('hide');
                Swal.fire('Success!', response.success, 'success');
                const currentPage = paginationContainer.find('.active .page-link').data('page') || 1;
                fetchCoupons(currentPage, searchInput.val());
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Please check the form for errors.', 'error');
            }
        });
    });

    // Delete Button Click
    tableBody.on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let url = "{{ route('coupon.destroy', ':id') }}";
        url = url.replace(':id', id);

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                    success: function(response) {
                        Swal.fire('Deleted!', response.success, 'success');
                        const currentPage = paginationContainer.find('.active .page-link').data('page') || 1;
                        fetchCoupons(currentPage, searchInput.val());
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });

    // Search Input Logic
    searchInput.on('keyup', function() {
        clearTimeout(searchTimeout);
        const searchQuery = $(this).val();
        searchTimeout = setTimeout(function() {
            fetchCoupons(1, searchQuery);
        }, 500);
    });

    // Pagination Click Logic
    paginationContainer.on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const searchQuery = searchInput.val();
        if (page) {
            fetchCoupons(page, searchQuery);
        }
    });

    // Initial fetch
    fetchCoupons();
});
</script>
