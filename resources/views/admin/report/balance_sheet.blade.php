@extends('admin.master.master')
@section('title', 'Balance Sheet Report')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
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
        <h2 class="mb-4">Balance Sheet</h2>
        <div class="card shadow-sm mb-4 no-print">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">As of Date</label>
                        <input type="text" id="end-date" class="form-control datepicker" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end mb-3">
                        <button class="btn btn-primary w-100" id="generate-report-btn">Generate</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="report-container"></div>
    </div>
</main>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    flatpickr(".datepicker", {
        dateFormat: "Y-m-d", // Ensures the date format matches what the backend expects
    });

    const endDateInput = $('#end-date');
    const generateBtn = $('#generate-report-btn');
    const reportContainer = $('#report-container');

    generateBtn.on('click', function() {
        generateBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        reportContainer.html('');

        $.ajax({
            url: "{{ route('balance_sheet.generate') }}",
            type: 'GET',
            data: { end_date: endDateInput.val() },
            success: function(data) {
                renderReport(data, endDateInput.val());
            },
            error: function() { Swal.fire('Error', 'Failed to generate the report.', 'error'); },
            complete: function() { generateBtn.prop('disabled', false).html('Generate'); }
        });
    });

    function renderReport(data, endDate) {
        const formatDate = (dateStr) => new Date(dateStr + 'T00:00:00').toLocaleDateString('en-GB');
        const formatCurrency = (num) => parseFloat(num).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        let assetsHtml = '', liabilitiesHtml = '', equityHtml = '';
        let totalAssets = 0, totalLiabilities = 0, totalEquity = 0;

        data.assets.forEach(item => { assetsHtml += `<tr><td>${item.name}</td><td class="text-end">${formatCurrency(item.amount)}</td></tr>`; totalAssets += parseFloat(item.amount); });
        data.liabilities.forEach(item => { liabilitiesHtml += `<tr><td>${item.name}</td><td class="text-end">${formatCurrency(item.amount)}</td></tr>`; totalLiabilities += parseFloat(item.amount); });
        data.equity.forEach(item => { equityHtml += `<tr><td>${item.name}</td><td class="text-end">${formatCurrency(item.amount)}</td></tr>`; totalEquity += parseFloat(item.amount); });

        const printUrl = new URL("{{ route('balance_sheet.print') }}");
        printUrl.searchParams.append('end_date', endDate);

        const reportHtml = `
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center no-print">
                    <h5 class="mb-0">Balance Sheet as of ${formatDate(endDate)}</h5>
                    <a href="${printUrl.href}" target="_blank" class="btn btn-warning btn-sm">
                        <i class="fa fa-print me-2"></i>Print PDF
                    </a>
                </div>
                <div class="card-body print-area">
                    {{-- This is the completed HTML section --}}
                    <div class="text-center mb-4 d-none d-print-block">
                        <h3>Balance Sheet</h3>
                        <p><strong>As of Date:</strong> ${formatDate(endDate)}</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Assets</h5>
                            <table class="table table-sm">
                                <tbody>${assetsHtml}</tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td>Total Assets</td>
                                        <td class="text-end">৳${formatCurrency(totalAssets)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Liabilities</h5>
                            <table class="table table-sm">
                                <tbody>${liabilitiesHtml}</tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td>Total Liabilities</td>
                                        <td class="text-end">৳${formatCurrency(totalLiabilities)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <h5 class="mt-4">Equity</h5>
                            <table class="table table-sm">
                                <tbody>${equityHtml}</tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td>Total Equity</td>
                                        <td class="text-end">৳${formatCurrency(totalEquity)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <table class="table mt-4">
                                <tfoot class="table-primary fw-bold fs-5">
                                    <tr>
                                        <td>Total Liabilities & Equity</td>
                                        <td class="text-end">৳${formatCurrency(totalLiabilities + totalEquity)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
        reportContainer.html(reportHtml);
    }
    
    // The old window.print() handler is no longer needed and can be removed.
});
</script>
@endsection