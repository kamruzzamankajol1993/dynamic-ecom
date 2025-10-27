<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
$(document).ready(function() {
    google.charts.load('current', {'packages':['corechart']});

    const tableBody = $('#table-body');
    const paginationContainer = $('#pagination-container');
    const paginationInfo = $('#pagination-info');
    const spinner = $('.loading-spinner');
    const summaryCards = $('#summary-cards'); // Ensure this ID exists in your main blade file
    let currentFilter = 'weekly'; // Default filter
    let customStartDate, customEndDate;

    function fetchData(page = 1) {
        spinner.show();
        tableBody.empty(); // Clear previous table data

        let url = `{{ route('report.sales.data') }}?page=${page}&filter=${currentFilter}`;
        if (currentFilter === 'custom' && customStartDate && customEndDate) {
            url += `&start_date=${customStartDate}&end_date=${customEndDate}`;
        }

        $.get(url, function(response) {
            spinner.hide();
            renderSummary(response.summary);
            renderTable(response.table_data);
            renderPagination(response.table_data);
            // Redraw chart only if there's data
            if (response.chart_data && response.chart_data.length > 1) {
                google.charts.setOnLoadCallback(() => drawChart(response.chart_data));
            } else {
                 $('#sales_chart').html('<div class="text-center p-5 text-muted">Not enough data to display chart.</div>');
            }
            paginationInfo.text(`Showing ${response.table_data.from || 0} to ${response.table_data.to || 0} of ${response.table_data.total} entries`);
        }).fail(() => {
            spinner.hide();
            // Optionally display an error message
            tableBody.html('<tr><td colspan="7" class="text-center text-danger">Failed to load report data.</td></tr>');
            summaryCards.html('<p class="text-danger">Error loading summary.</p>');
        });
    }

    // --- START: UPDATED renderSummary Function ---
    function renderSummary(summary) {
        // Helper function for formatting currency
        const formatCurrency = (num) => `৳${parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        const cards = `
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="summary-card bg-primary">
                    <h5>Total Sales</h5>
                    <h2>${formatCurrency(summary.total_sales)}</h2>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="summary-card bg-danger">
                    <h5>Total Cost</h5>
                    <h2>${formatCurrency(summary.totalProductionCost)}</h2>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="summary-card bg-warning text-dark">
                    <h5>Total Expense</h5>
                    <h2>${formatCurrency(summary.totalExpense)}</h2>
                </div>
            </div>
             <div class="col-md-6 col-xl-3 mb-4">
                <div class="summary-card bg-success">
                    <h5>Net Income</h5>
                    <h2>${formatCurrency(summary.totalNetIncome)}</h2>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="summary-card bg-success">
                    <h5>Gross Profit</h5>
                    <h2>${formatCurrency(summary.totalGrossProfit)}</h2>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="summary-card bg-info">
                    <h5>Total Orders</h5>
                    <h2>${summary.total_orders || 0}</h2>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="summary-card bg-secondary">
                    <h5>Total Shipping</h5>
                    <h2>${formatCurrency(summary.total_shipping)}</h2>
                </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="summary-card bg-dark">
                    <h5>Total Discount</h5>
                    <h2>${formatCurrency(summary.total_discount)}</h2>
                </div>
            </div>
        `;
        summaryCards.html(cards);
    }
    // --- END: UPDATED renderSummary Function ---

    function renderTable(response) {
        if (!response || !response.data || response.data.length === 0) {
            tableBody.html('<tr><td colspan="7" class="text-center">No sales data found for this period.</td></tr>');
            return;
        }
        let tableRows = '';
        response.data.forEach(order => {
            const customerName = order.customer ? order.customer.name : 'N/A';
            const orderLink = `{{ url('order') }}/${order.id}`; // Generate the order link
            tableRows += `
                <tr>
                    <td>${new Date(order.created_at).toLocaleDateString('en-GB')}</td>
                    <td><a href="${orderLink}" target="_blank">#${order.invoice_no}</a></td>
                    <td>${customerName}</td>
                    <td class="text-end">${parseFloat(order.subtotal || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-end">${parseFloat(order.discount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-end">${parseFloat(order.shipping_cost || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-end"><strong>${parseFloat(order.total_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
                </tr>`;
        });
        tableBody.html(tableRows);
    }

    function renderPagination(response) {
        paginationContainer.empty();
        if (!response || !response.last_page || response.last_page <= 1) {
            paginationInfo.text(response.total > 0 ? `Showing ${response.from} to ${response.to} of ${response.total} entries` : 'No entries found');
            return; // No pagination needed if only one page or no data
        }

        const currentPage = response.current_page;
        const lastPage = response.last_page;

        // Previous button
        let prevDisabled = currentPage === 1 ? 'disabled' : '';
        paginationContainer.append(`<li class="page-item ${prevDisabled}"><a class="page-link" href="#" data-page="${currentPage - 1}">‹</a></li>`);

        // Page number logic (simplified example - consider adding ellipsis for many pages)
        const pagesToShow = 5;
        let startPage = Math.max(1, currentPage - Math.floor(pagesToShow / 2));
        let endPage = Math.min(lastPage, startPage + pagesToShow - 1);
        
        // Adjust if we are near the end
         if (endPage === lastPage && (endPage - startPage + 1 < pagesToShow)) {
            startPage = Math.max(1, lastPage - pagesToShow + 1);
        }

        if (startPage > 1) {
             paginationContainer.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
             if (startPage > 2) {
                 paginationContainer.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
             }
        }


        for (let i = startPage; i <= endPage; i++) {
            paginationContainer.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
        }

         if (endPage < lastPage) {
             if (endPage < lastPage - 1) {
                 paginationContainer.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
             }
             paginationContainer.append(`<li class="page-item"><a class="page-link" href="#" data-page="${lastPage}">${lastPage}</a></li>`);
         }

        // Next button
        let nextDisabled = currentPage === lastPage ? 'disabled' : '';
        paginationContainer.append(`<li class="page-item ${nextDisabled}"><a class="page-link" href="#" data-page="${currentPage + 1}">›</a></li>`);
    }

    function drawChart(chartData) {
        var data = google.visualization.arrayToDataTable(chartData);
        var options = {
            legend: { position: 'none' },
            chartArea: {'width': '90%', 'height': '80%'},
            hAxis: { textStyle: { color: '#555', fontSize: 12 } },
            vAxis: { gridlines: { color: '#eee' }, format: '৳#,##0.##' }, // Added currency format
            colors: ['#2b7f75'],
            curveType: 'function', // Makes it a smooth line chart
            pointSize: 5
        };
        // Changed to LineChart for better trend visualization
        var chart = new google.visualization.LineChart(document.getElementById('sales_chart'));
        chart.draw(data, options);
    }

    // Event Listeners
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        $('#date-range-picker').val(''); // Clear custom range input when preset is clicked
        fetchData();
    });

    $('#date-range-picker').daterangepicker({
        opens: 'left',
        autoUpdateInput: false, // Don't auto-update the input
         locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    }, function(start, end, label) {
        // This function runs when a date range is selected
        $('#date-range-picker').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        $('.filter-btn').removeClass('active');
        currentFilter = 'custom';
        customStartDate = start.format('YYYY-MM-DD');
        customEndDate = end.format('YYYY-MM-DD');
        fetchData();
    });

    // Handle clearing the date range picker
    $('#date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
         // Optionally reset to a default filter like 'weekly'
        currentFilter = 'weekly';
        $('.filter-btn').removeClass('active');
        $('.filter-btn[data-filter="weekly"]').addClass('active');
        fetchData();
    });


    paginationContainer.on('click', '.page-link', (e) => {
         e.preventDefault();
         const page = $(e.target).data('page');
         // Check if the clicked element is not disabled
         if (page && !$(e.target).parent().hasClass('disabled')) {
             fetchData(page);
         }
     });

    // Initial Fetch
    // Set default filter button active state on load
    $('.filter-btn[data-filter="' + currentFilter + '"]').addClass('active');
    fetchData(); // Fetch data for the default filter on page load

    // Redraw chart on window resize
    $(window).resize(function(){
        // Check if chart data exists before redrawing
        // You might need a global variable to store the last fetched chart data
        // For simplicity, just calling fetchData again is easier but less efficient
         fetchData();
    });
});
</script>