<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(document).ready(function() {
    const tableBody = $('#table-body');
    const paginationContainer = $('#pagination-container');
    const paginationInfo = $('#pagination-info');
    const spinner = $('.loading-spinner');
    const searchInput = $('#search-input');
    let currentFilter = 'weekly';
    let customStartDate, customEndDate;
    let searchTimeout;

    function fetchData(page = 1, searchQuery = '') {
        spinner.show();
        tableBody.empty();

        let url = `{{ route('report.category.data') }}?page=${page}&filter=${currentFilter}`;
        if (currentFilter === 'custom' && customStartDate && customEndDate) {
            url += `&start_date=${customStartDate}&end_date=${customEndDate}`;
        }
        if (searchQuery) {
            url += `&search=${searchQuery}`;
        }

        $.get(url, function(response) {
            spinner.hide();
            renderTable(response);
            renderPagination(response);
            paginationInfo.text(`Showing ${response.from || 0} to ${response.to || 0} of ${response.total} entries`);
        }).fail(() => spinner.hide());
    }

    function renderTable(response) {
        if (response.data.length === 0) {
            tableBody.html('<tr><td colspan="4" class="text-center">No data found for this period.</td></tr>');
            return;
        }
        const startSl = (response.current_page - 1) * response.per_page;
        response.data.forEach((category, index) => {
            const row = `
                <tr>
                    <td>${startSl + index + 1}</td>
                    <td>${category.category_name}</td>
                    <td class="text-center">${category.total_products_sold}</td>
                    <td class="text-end"><strong>${new Intl.NumberFormat().format(category.total_sales_value)}</strong></td>
                </tr>`;
            tableBody.append(row);
        });
    }

    function renderPagination(response) {
        // Using the same beautiful pagination logic from previous scripts
        paginationContainer.empty();
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
    
    // Event Listeners
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        fetchData(1, searchInput.val());
    });

    $('#date-range-picker').daterangepicker({
        opens: 'left'
    }, function(start, end, label) {
        $('.filter-btn').removeClass('active');
        currentFilter = 'custom';
        customStartDate = start.format('YYYY-MM-DD');
        customEndDate = end.format('YYYY-MM-DD');
        fetchData(1, searchInput.val());
    });
    
    searchInput.on('keyup', function() {
        clearTimeout(searchTimeout);
        const searchQuery = $(this).val();
        searchTimeout = setTimeout(function() {
            fetchData(1, searchQuery);
        }, 500);
    });

    paginationContainer.on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            fetchData(page, searchInput.val());
        }
    });

    // Initial Fetch
    $('.filter-btn[data-filter="weekly"]').addClass('active');
    fetchData();
});
</script>
