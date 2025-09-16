<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Book Report - {{ $account->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1, h2 { margin: 0; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .data-table .text-end { text-align: right; }
        .data-table thead { background-color: #f2f2f2; font-weight: bold; }
        .data-table tfoot { font-weight: bold; background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Your Company Name</h1>
        <h2>Bank Book Report</h2>
        <p><strong>Account:</strong> {{ $account->name }} ({{ $account->code }})</p>
        <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }}</p>
    </div>

    <table class="data-table">
        <thead>
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
            @php $runningBalance = $opening_balance; @endphp
            <tr>
                <td colspan="5"><strong>Opening Balance</strong></td>
                <td class="text-end"><strong>৳{{ number_format($runningBalance, 2) }}</strong></td>
            </tr>
            @foreach($transactions as $entry)
                @php
                    $debit = $entry->type === 'debit' ? $entry->amount : 0;
                    $credit = $entry->type === 'credit' ? $entry->amount : 0;
                    $runningBalance += ($debit - $credit);
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($entry->transaction->date)->format('d-m-Y') }}</td>
                    <td>{{ $entry->transaction->voucher_no }}</td>
                    <td>{{ $entry->transaction->description }}</td>
                    <td class="text-end">{{ $debit > 0 ? number_format($debit, 2) : '' }}</td>
                    <td class="text-end">{{ $credit > 0 ? number_format($credit, 2) : '' }}</td>
                    <td class="text-end">{{ number_format($runningBalance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end"><strong>Closing Balance</strong></td>
                <td class="text-end"><strong>৳{{ number_format($runningBalance, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>