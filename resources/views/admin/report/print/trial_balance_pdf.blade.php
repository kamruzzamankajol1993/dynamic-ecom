<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trial Balance</title>
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
        <h2>Trial Balance</h2>
        <p>As of {{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trialBalance as $item)
                <tr>
                    <td>{{ $item['account_code'] }}</td>
                    <td>{{ $item['account_name'] }}</td>
                    <td class="text-end">{{ $item['debit'] > 0 ? number_format($item['debit'], 2) : '' }}</td>
                    <td class="text-end">{{ $item['credit'] > 0 ? number_format($item['credit'], 2) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-end"><strong>Total</strong></td>
                <td class="text-end"><strong>৳{{ number_format(array_sum(array_column($trialBalance, 'debit')), 2) }}</strong></td>
                <td class="text-end"><strong>৳{{ number_format(array_sum(array_column($trialBalance, 'credit')), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>