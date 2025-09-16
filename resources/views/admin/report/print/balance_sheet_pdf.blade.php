<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Balance Sheet</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        h1, h2 { margin: 0; }
        .content-table { width: 100%; border-collapse: collapse; }
        .content-table td { vertical-align: top; padding: 0 10px; }
        .data-table { width: 100%; margin-bottom: 20px; }
        .data-table th, .data-table td { padding: 5px; text-align: left; }
        .data-table .text-end { text-align: right; }
        .data-table tfoot { font-weight: bold; background-color: #f2f2f2; border-top: 2px solid #333; border-bottom: 2px solid #333; }
        .total-row { background-color: #e3f2fd; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Your Company Name</h1>
        <h2>Balance Sheet</h2>
        <p>As of {{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</p>
    </div>

    <table class="content-table">
        <tr>
            <td style="width: 50%;">
                <h3>Assets</h3>
                <table class="data-table">
                    <tbody>
                        @foreach($assets as $item)
                        <tr><td>{{ $item['name'] }}</td><td class="text-end">৳{{ number_format($item['amount'], 2) }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td>Total Assets</td><td class="text-end">৳{{ number_format(array_sum(array_column($assets, 'amount')), 2) }}</td></tr>
                    </tfoot>
                </table>
            </td>
            <td style="width: 50%;">
                <h3>Liabilities</h3>
                <table class="data-table">
                    <tbody>
                        @foreach($liabilities as $item)
                        <tr><td>{{ $item['name'] }}</td><td class="text-end">৳{{ number_format($item['amount'], 2) }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td>Total Liabilities</td><td class="text-end">৳{{ number_format(array_sum(array_column($liabilities, 'amount')), 2) }}</td></tr>
                    </tfoot>
                </table>

                <h3>Equity</h3>
                <table class="data-table">
                    <tbody>
                        @foreach($equity as $item)
                        <tr><td>{{ $item['name'] }}</td><td class="text-end">৳{{ number_format($item['amount'], 2) }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td>Total Equity</td><td class="text-end">৳{{ number_format(array_sum(array_column($equity, 'amount')), 2) }}</td></tr>
                    </tfoot>
                </table>

                <table class="data-table">
                    <tfoot class="total-row">
                        <tr>
                            <td>Total Liabilities & Equity</td>
                            <td class="text-end">৳{{ number_format(array_sum(array_column($liabilities, 'amount')) + array_sum(array_column($equity, 'amount')), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>