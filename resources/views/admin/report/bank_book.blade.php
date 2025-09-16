@extends('admin.master.master')
@section('title', 'Bank Book Report')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Styles for printing the report */
    @media print {
        body * {
            visibility: hidden;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .card-header {
            border-bottom: 1px solid #dee2e6 !important;
        }
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Bank Book Report</h2>

        {{-- Filter Form --}}
        <div class="card shadow-sm mb-4 no-print">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Select Bank Account</label>
                        <select id="account-select" class="form-control select2"></select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="text" id="start-date" class="form-control datepicker" value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="text" id="end-date" class="form-control datepicker" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end mb-3">
                        <button class="btn btn-primary w-100" id="generate-report-btn">
                            Generate
                        </button>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {

    flatpickr(".datepicker", {
        dateFormat: "Y-m-d", // Ensures the date format matches what the backend expects
    });
    const accountSelect = $('#account-select');
    const startDateInput = $('#start-date');
    const endDateInput = $('#end-date');
    const generateBtn = $('#generate-report-btn');
    const reportContainer = $('#report-container');

    // Initialize Select2
    accountSelect.select2({
        placeholder: "Select a bank account",
        allowClear: true
    });

    // Fetch accounts for the dropdown
    $.ajax({
        url: "{{ route('reports.bank_book.dependencies') }}",
        type: 'GET',
        success: function(data) {
            const options = data.accounts.map(acc => `<option value="${acc.id}">${acc.name} (${acc.code || 'N/A'})</option>`);
            accountSelect.html('<option></option>' + options.join(''));
        }
    });

    // Handle report generation
    generateBtn.on('click', function() {
        const accountId = accountSelect.val();
        const startDate = startDateInput.val();
        const endDate = endDateInput.val();

        if (!accountId) {
            Swal.fire('Validation Error', 'Please select a bank account.', 'warning');
            return;
        }

        generateBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        reportContainer.html('');

        $.ajax({
            url: "{{ route('reports.bank_book.generate') }}",
            type: 'GET',
            data: {
                account_id: accountId,
                start_date: startDate,
                end_date: endDate
            },
            success: function(data) {
                renderReport(data, accountSelect.find('option:selected').text());
            },
            error: function() {
                Swal.fire('Error', 'Failed to generate the report.', 'error');
            },
            complete: function() {
                generateBtn.prop('disabled', false).html('Generate');
            }
        });
    });

    // Function to render the report HTML
    function renderReport(data, accountName) {
        let runningBalance = data.opening_balance;
        let transactionRows = '';

        data.transactions.forEach(entry => {
            const debit = entry.type === 'debit' ? parseFloat(entry.amount) : 0;
            const credit = entry.type === 'credit' ? parseFloat(entry.amount) : 0;
            runningBalance += (debit - credit);
            
            const entryDate = new Date(entry.transaction.date).toLocaleDateString('en-GB');

            transactionRows += `
                <tr>
                    <td>${entryDate}</td>
                    <td>${entry.transaction.voucher_no}</td>
                    <td>${entry.transaction.description || ''}</td>
                    <td class="text-end text-success">${debit > 0 ? debit.toFixed(2) : ''}</td>
                    <td class="text-end text-danger">${credit > 0 ? credit.toFixed(2) : ''}</td>
                    <td class="text-end">${runningBalance.toFixed(2)}</td>
                </tr>
            `;
        });
 const printUrl = new URL("{{ route('reports.bank_book.print') }}");
        printUrl.searchParams.append('account_id', $('#account-select').val());
        printUrl.searchParams.append('start_date', $('#start-date').val());
        printUrl.searchParams.append('end_date', $('#end-date').val());
        const reportHtml = `
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center no-print">
                    <h5 class="mb-0">Report for: ${accountName}</h5>
                    <a href="${printUrl.href}" target="_blank" class="btn btn-warning btn-sm">
                        <i class="fa fa-print me-2"></i>Print PDF
                    </a>
                </div>
                <div class="card-body print-area">
                    <div class="text-center mb-4 d-none d-print-block">
                        <h3>Bank Book Report</h3>
                        <p><strong>Account:</strong> ${accountName}</p>
                        <p><strong>Period:</strong> ${startDateInput.val()} to ${endDateInput.val()}</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Voucher No</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="fw-bold">Opening Balance</td>
                                    <td class="text-end fw-bold">${data.opening_balance.toFixed(2)}</td>
                                </tr>
                                ${transactionRows}
                                <tr class="table-light">
                                    <td colspan="5" class="fw-bold text-end">Closing Balance</td>
                                    <td class="text-end fw-bold">${runningBalance.toFixed(2)}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        reportContainer.html(reportHtml);
    }
    
    // Handle print button click
    $(document).on('click', '#print-report-btn', function() {
        window.print();
    });
});
</script>
@endsection