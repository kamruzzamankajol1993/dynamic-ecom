@extends('admin.master.master')
@section('title', 'Trial Balance Report')

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
        <h2 class="mb-4">Trial Balance</h2>

        {{-- Filter Form --}}
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
    const endDateInput = $('#end-date');
    const generateBtn = $('#generate-report-btn');
    const reportContainer = $('#report-container');

    generateBtn.on('click', function() {
        generateBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        reportContainer.html('');

        $.ajax({
            url: "{{ route('trial_balance.generate') }}",
            type: 'GET',
            data: { end_date: endDateInput.val() },
            success: function(data) {
                renderReport(data, endDateInput.val());
            },
            error: function() { 
                Swal.fire('Error', 'Failed to generate the report.', 'error');
            },
            complete: function() {
                generateBtn.prop('disabled', false).html('Generate');
            }
        });
    });

    function renderReport(data, endDate) {
        const formatDate = (dateStr) => new Date(dateStr + 'T00:00:00').toLocaleDateString('en-GB');
        const formatCurrency = (num) => parseFloat(num).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        const printUrl = new URL("{{ route('trial_balance.print') }}");
        printUrl.searchParams.append('end_date', endDate);
        let tableRows = '';
        let totalDebit = 0;
        let totalCredit = 0;

        data.forEach(item => {
            tableRows += `
                <tr>
                    <td>${item.account_code || ''}</td>
                    <td>${item.account_name}</td>
                    <td class="text-end">${item.debit > 0 ? formatCurrency(item.debit) : ''}</td>
                    <td class="text-end">${item.credit > 0 ? formatCurrency(item.credit) : ''}</td>
                </tr>
            `;
            totalDebit += parseFloat(item.debit);
            totalCredit += parseFloat(item.credit);
        });

        const reportHtml = `
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center no-print">
                    <h5 class="mb-0">Trial Balance as of ${formatDate(endDate)}</h5>
                    <a href="${printUrl.href}" target="_blank" class="btn btn-warning btn-sm">
                        <i class="fa fa-print me-2"></i>Print PDF
                    </a>
                </div>
                <div class="card-body print-area">
                    <div class="text-center mb-4 d-none d-print-block">
                        <h3>Trial Balance</h3>
                        <p><strong>As of Date:</strong> ${formatDate(endDate)}</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="2" class="text-end">Total</td>
                                    <td class="text-end">৳${formatCurrency(totalDebit)}</td>
                                    <td class="text-end">৳${formatCurrency(totalCredit)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        `;
        reportContainer.html(reportHtml);
    }
    
    $(document).on('click', '#print-report-btn', () => window.print());
});
</script>
@endsection