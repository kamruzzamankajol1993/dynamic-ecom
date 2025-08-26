<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(document).ready(function() {
    const tableBody = $('#table-body');
    const spinner = $('.loading-spinner');
    const summaryCards = $('#summary-cards');
    const monthFilter = $('#month-filter');
    const yearFilter = $('#year-filter');
    let currentFilter = 'monthly';
    let customStartDate, customEndDate;

    // Populate Year and Month Filters
    const currentYear = new Date().getFullYear();
    for (let i = currentYear; i >= currentYear - 5; i--) {
        yearFilter.append(new Option(i, i));
    }
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    months.forEach((month, index) => {
        monthFilter.append(new Option(month, index + 1));
    });
    monthFilter.val(new Date().getMonth() + 1);

    function fetchData() {
        spinner.show();
        tableBody.empty();

        let data = { filter: currentFilter };
        if (currentFilter === 'monthly') {
            data.month = monthFilter.val();
            data.year = yearFilter.val();
        } else if (currentFilter === 'yearly') {
            data.year = yearFilter.val();
        } else if (currentFilter === 'custom') {
            data.start_date = customStartDate;
            data.end_date = customEndDate;
        }

        $.get("{{ route('report.income.data') }}", data, function(response) {
            spinner.hide();
            renderSummary(response.summary);
            renderTable(response.table_data);
        }).fail(() => spinner.hide());
    }

    function renderSummary(summary) {
        const cards = `
            <div class="col-md-4"><div class="summary-card bg-success"><h5>Total Revenue</h5><h2>${new Intl.NumberFormat().format(summary.total_revenue || 0)}</h2></div></div>
            <div class="col-md-4"><div class="summary-card bg-danger"><h5>Total Expense</h5><h2>${new Intl.NumberFormat().format(summary.total_expense || 0)}</h2></div></div>
            <div class="col-md-4"><div class="summary-card bg-info"><h5>Net Income</h5><h2>${new Intl.NumberFormat().format(summary.net_income || 0)}</h2></div></div>
        `;
        summaryCards.html(cards);
    }

    function renderTable(data) {
        if (data.length === 0) {
            tableBody.html('<tr><td colspan="4" class="text-center">No data found for this period.</td></tr>');
            return;
        }
        data.forEach(item => {
            const netIncomeClass = item.net_income >= 0 ? 'text-success' : 'text-danger';
            const row = `
                <tr>
                    <td>${item.date}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(item.revenue)}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(item.expense)}</td>
                    <td class="text-end"><strong class="${netIncomeClass}">${new Intl.NumberFormat().format(item.net_income)}</strong></td>
                </tr>`;
            tableBody.append(row);
        });
    }
    
    // Event Listeners
    monthFilter.add(yearFilter).on('change', function() {
        currentFilter = 'monthly';
        if ($(this).is('#year-filter') && monthFilter.is(':hidden')) {
            currentFilter = 'yearly';
        }
        fetchData();
    });

    $('#date-range-picker').daterangepicker({ opens: 'left' }, function(start, end) {
        currentFilter = 'custom';
        customStartDate = start.format('YYYY-MM-DD');
        customEndDate = end.format('YYYY-MM-DD');
        fetchData();
    });

    // Initial Fetch
    fetchData();
});
</script>
