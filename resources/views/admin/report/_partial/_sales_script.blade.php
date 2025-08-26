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
    const summaryCards = $('#summary-cards');
    let currentFilter = 'weekly';
    let customStartDate, customEndDate;

    function fetchData(page = 1) {
        spinner.show();
        tableBody.empty();

        let url = `{{ route('report.sales.data') }}?page=${page}&filter=${currentFilter}`;
        if (currentFilter === 'custom' && customStartDate && customEndDate) {
            url += `&start_date=${customStartDate}&end_date=${customEndDate}`;
        }

        $.get(url, function(response) {
            spinner.hide();
            renderSummary(response.summary);
            renderTable(response.table_data);
            renderPagination(response.table_data);
            google.charts.setOnLoadCallback(() => drawChart(response.chart_data));
            paginationInfo.text(`Showing ${response.table_data.from || 0} to ${response.table_data.to || 0} of ${response.table_data.total} entries`);
        }).fail(() => spinner.hide());
    }

    function renderSummary(summary) {
        const cards = `
            <div class="col-md-6 col-xl-3"><div class="summary-card bg-primary"><h5>Total Sales</h5><h2>${new Intl.NumberFormat().format(summary.total_sales || 0)}</h2></div></div>
            <div class="col-md-6 col-xl-3"><div class="summary-card bg-success"><h5>Total Orders</h5><h2>${summary.total_orders || 0}</h2></div></div>
            <div class="col-md-6 col-xl-3"><div class="summary-card bg-warning"><h5>Total Discount</h5><h2>${new Intl.NumberFormat().format(summary.total_discount || 0)}</h2></div></div>
            <div class="col-md-6 col-xl-3"><div class="summary-card bg-info"><h5>Shipping Charge</h5><h2>${new Intl.NumberFormat().format(summary.total_shipping || 0)}</h2></div></div>
        `;
        summaryCards.html(cards);
    }

    function renderTable(response) {
        if (response.data.length === 0) {
            tableBody.html('<tr><td colspan="7" class="text-center">No sales data found for this period.</td></tr>');
            return;
        }
        response.data.forEach(order => {
            const row = `
                <tr>
                    <td>${new Date(order.created_at).toLocaleDateString('en-GB')}</td>
                    <td><a href="/order/${order.id}">#${order.invoice_no}</a></td>
                    <td>${order.customer ? order.customer.name : 'N/A'}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(order.subtotal)}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(order.discount)}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(order.shipping_cost)}</td>
                    <td class="text-end"><strong>${new Intl.NumberFormat().format(order.total_amount)}</strong></td>
                </tr>`;
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

    function drawChart(chartData) {
        if (chartData.length <= 1) {
            $('#sales_chart').html('<div class="text-center p-5 text-muted">Not enough data to display chart.</div>');
            return;
        }
        var data = google.visualization.arrayToDataTable(chartData);
        var options = {
            legend: { position: 'none' },
            chartArea: {'width': '90%', 'height': '80%'},
            hAxis: { textStyle: { color: '#555', fontSize: 12 } },
            vAxis: { gridlines: { color: '#eee' } },
            colors: ['#2b7f75']
        };
        var chart = new google.visualization.AreaChart(document.getElementById('sales_chart'));
        chart.draw(data, options);
    }
    
    // Event Listeners
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        fetchData();
    });

    $('#date-range-picker').daterangepicker({
        opens: 'left'
    }, function(start, end, label) {
        $('.filter-btn').removeClass('active');
        currentFilter = 'custom';
        customStartDate = start.format('YYYY-MM-DD');
        customEndDate = end.format('YYYY-MM-DD');
        fetchData();
    });

    paginationContainer.on('click', '.page-link', (e) => { e.preventDefault(); fetchData($(e.target).data('page')); });

    // Initial Fetch
    $('.filter-btn[data-filter="weekly"]').addClass('active');
    fetchData();
});
</script>
