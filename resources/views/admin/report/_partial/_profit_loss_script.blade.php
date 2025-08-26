<script>
$(document).ready(function() {
    const tableBody = $('#table-body');
    const spinner = $('.loading-spinner');
    const yearFilter = $('#year-filter');

    // Populate Year Filter
    const currentYear = new Date().getFullYear();
    for (let i = currentYear; i >= currentYear - 5; i--) {
        yearFilter.append(new Option(i, i));
    }

    function fetchData() {
        spinner.show();
        tableBody.empty();
        const year = yearFilter.val();

        $.get(`{{ route('report.profit_loss.data') }}?year=${year}`, function(response) {
            spinner.hide();
            renderTable(response.data);
        }).fail(() => {
            spinner.hide();
            tableBody.html('<tr><td colspan="9" class="text-center text-danger">Failed to load data.</td></tr>');
        });
    }

    function renderTable(data) {
        if (data.length === 0) {
            tableBody.html('<tr><td colspan="9" class="text-center">No data found for this year.</td></tr>');
            return;
        }
        data.forEach((item, index) => {
            const netProfitClass = item.net_profit >= 0 ? 'text-success' : 'text-danger';
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.month}</td>
                    <td class="text-center">${item.orders}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(item.selling_price)}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(item.production_cost)}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(item.delivery_charge)}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(item.income_from_sales)}</td>
                    <td class="text-end">${new Intl.NumberFormat().format(item.monthly_expense)}</td>
                    <td class="text-end"><strong class="${netProfitClass}">${new Intl.NumberFormat().format(item.net_profit)}</strong></td>
                </tr>`;
            tableBody.append(row);
        });
    }
    
    // Event Listeners
    yearFilter.on('change', fetchData);

    // Initial Fetch
    fetchData();
});
</script>
