@extends('admin.master.master')
@section('title', 'Profit & Loss Report')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Styles for printing the report */
    @media print {
        body * { visibility: hidden; }
        .print-area, .print-area * { visibility: visible; }
        .print-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .card-header { border-bottom: 1px solid #dee2e6 !important; }
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Profit & Loss Statement</h2>

        {{-- Filter Form --}}
        <div class="card shadow-sm mb-4 no-print">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="text" id="start-date" class="form-control datepicker" value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="text" id="end-date" class="form-control datepicker" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end mb-3">
                        <button class="btn btn-primary w-100" id="generate-report-btn">Generate</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Report Display Area --}}
        <div id="report-container">
            {{-- Report will be rendered here via AJAX --}}
        </div>
    </div>
</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function() {

    flatpickr(".datepicker", {
        dateFormat: "Y-m-d", // Ensures the date format matches what the backend expects
    });

    const startDateInput = $('#start-date');
    const endDateInput = $('#end-date');
    const generateBtn = $('#generate-report-btn');
    const reportContainer = $('#report-container');

    generateBtn.on('click', function() {
        generateBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        reportContainer.html('');

        $.ajax({
            url: "{{ route('profit_and_loss.generate') }}",
            type: 'GET',
            data: { 
                start_date: startDateInput.val(),
                end_date: endDateInput.val() 
            },
            success: function(data) {
                renderReport(data, startDateInput.val(), endDateInput.val());
            },
            error: function() { 
                Swal.fire('Error', 'Failed to generate the report.', 'error');
            },
            complete: function() {
                generateBtn.prop('disabled', false).html('Generate');
            }
        });
    });

    function renderReport(data, startDate, endDate) {
        const formatDate = (dateStr) => new Date(dateStr + 'T00:00:00').toLocaleDateString('en-GB');
        const formatCurrency = (num) => parseFloat(num).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});

         const printUrl = new URL("{{ route('profit_and_loss.print') }}");
        printUrl.searchParams.append('start_date', startDate);
        printUrl.searchParams.append('end_date', endDate);
        let revenuesHtml = '', expensesHtml = '';

        data.revenues.forEach(item => { revenuesHtml += `<tr><td>${item.name}</td><td class="text-end">${formatCurrency(item.amount)}</td></tr>`; });
        data.expenses.forEach(item => { expensesHtml += `<tr><td>${item.name}</td><td class="text-end">${formatCurrency(item.amount)}</td></tr>`; });

        const reportHtml = `
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center no-print">
                    <h5 class="mb-0">Profit & Loss Statement</h5>
                    <a href="${printUrl.href}" target="_blank" class="btn btn-warning btn-sm">
                        <i class="fa fa-print me-2"></i>Print PDF
                    </a>
                </div>
                <div class="card-body print-area">
                    <div class="text-center mb-4 d-none d-print-block">
                        <h3>Profit & Loss Statement</h3>
                        <p><strong>From:</strong> ${formatDate(startDate)} <strong>To:</strong> ${formatDate(endDate)}</p>
                    </div>
                    <table class="table">
                        <tbody>
                            <tr class="table-light"><td colspan="2"><strong>Revenues</strong></td></tr>
                            ${revenuesHtml}
                            <tr class="fw-bold"><td>Total Revenue</td><td class="text-end">৳${formatCurrency(data.total_revenue)}</td></tr>
                            
                            <tr class="table-light"><td colspan="2" class="pt-4"><strong>Expenses</strong></td></tr>
                            ${expensesHtml}
                            <tr class="fw-bold"><td>Total Expense</td><td class="text-end">৳${formatCurrency(data.total_expense)}</td></tr>

                            <tr class="table-primary fw-bold fs-5 border-top">
                                <td>Net Profit / (Loss)</td>
                                <td class="text-end">৳${formatCurrency(data.net_profit)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        reportContainer.html(reportHtml);
    }
    
    $(document).on('click', '#print-report-btn', () => window.print());
});
</script>
@endsection