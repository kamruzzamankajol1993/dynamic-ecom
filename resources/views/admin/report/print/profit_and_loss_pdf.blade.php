<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profit & Loss Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1, h2 { margin: 0; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td { padding: 8px; border-bottom: 1px solid #eee; }
        .data-table .text-end { text-align: right; }
        .table-section-header { font-weight: bold; background-color: #f2f2f2; border-top: 2px solid #333; }
        .total-row { font-weight: bold; border-top: 1px solid #333; }
        .grand-total-row { font-weight: bold; font-size: 14px; background-color: #e3f2fd; border-top: 2px solid #333; border-bottom: 2px solid #333; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Your Company Name</h1>
        <h2>Profit & Loss Statement</h2>
        <p>From {{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</p>
    </div>

    <table class="data-table">
        <tbody>
            <tr class="table-section-header"><td colspan="2">Revenues</td></tr>
            @foreach($revenues as $item)
            <tr><td>{{ $item['name'] }}</td><td class="text-end">৳{{ number_format($item['amount'], 2) }}</td></tr>
            @endforeach
            <tr class="total-row"><td>Total Revenue</td><td class="text-end">৳{{ number_format($total_revenue, 2) }}</td></tr>
            
            <tr class="table-section-header"><td colspan="2" style="padding-top: 20px;">Expenses</td></tr>
            @foreach($expenses as $item)
            <tr><td>{{ $item['name'] }}</td><td class="text-end">৳{{ number_format($item['amount'], 2) }}</td></tr>
            @endforeach
            <tr class="total-row"><td>Total Expense</td><td class="text-end">৳{{ number_format($total_expense, 2) }}</td></tr>

            <tr class="grand-total-row">
                <td>Net Profit / (Loss)</td>
                <td class="text-end">৳{{ number_format($net_profit, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>