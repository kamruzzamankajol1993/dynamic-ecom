<script>
$(document).ready(function() {
    const tableBody = $('#history-table-body');
    const paginationContainer = $('#pagination-container');
    const paginationInfo = $('#pagination-info');
    const spinner = $('.loading-spinner');
    const searchInput = $('#search-input');
    let searchTimeout;

    function fetchCustomers(page = 1, searchQuery = '') {
        spinner.show();
        tableBody.empty();

        let url = `{{ route('reward.data') }}?page=${page}`;
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
                paginationInfo.text(`Showing ${response.from || 0} to ${response.to || 0} of ${response.total} entries`);
            },
            error: function() {
                spinner.hide();
                tableBody.html('<tr><td colspan="6" class="text-center text-danger">Failed to load data.</td></tr>');
                paginationInfo.text('');
            }
        });
    }

    function renderTable(response) {
        const customers = response.data;
        if (customers.length === 0) {
            tableBody.html('<tr><td colspan="6" class="text-center">No customers found.</td></tr>');
            return;
        }

        const startSl = (response.current_page - 1) * response.per_page;

        customers.forEach((customer, index) => {
            const sl = startSl + index + 1;
            let viewLogUrl = "{{ route('reward.customer.history', ':id') }}";
            viewLogUrl = viewLogUrl.replace(':id', customer.id);

            const row = `
                <tr>
                    <td>${sl}</td>
                    <td>${customer.name}</td>
                    <td>${customer.phone}</td>
                    <td><strong>${customer.reward_points}</strong></td>
                    <td>${customer.reward_point_logs_count}</td>
                    <td>
                        <a href="${viewLogUrl}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-eye"></i> View Log
                        </a>
                    </td>
                </tr>
            `;
            tableBody.append(row);
        });
    }

    function renderPagination(response) {
        paginationContainer.empty();
        // Using the same beautiful pagination logic from the coupon script
        const currentPage = response.current_page;
        const lastPage = response.last_page;

        if (lastPage <= 1) return;

        let prevDisabled = currentPage === 1 ? 'disabled' : '';
        paginationContainer.append(`<li class="page-item ${prevDisabled}"><a class="page-link" data-page="${currentPage - 1}">‹</a></li>`);

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

        let nextDisabled = currentPage === lastPage ? 'disabled' : '';
        paginationContainer.append(`<li class="page-item ${nextDisabled}"><a class="page-link" data-page="${currentPage + 1}">›</a></li>`);
    }

    // Search Input Logic
    searchInput.on('keyup', function() {
        clearTimeout(searchTimeout);
        const searchQuery = $(this).val();
        searchTimeout = setTimeout(function() {
            fetchCustomers(1, searchQuery);
        }, 500);
    });

    // Pagination Click Logic
    paginationContainer.on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const searchQuery = searchInput.val();
        if (page) {
            fetchCustomers(page, searchQuery);
        }
    });

    // Initial fetch
    fetchCustomers();
});
</script>