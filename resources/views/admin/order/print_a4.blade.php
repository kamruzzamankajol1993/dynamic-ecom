<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .header { display: table; width: 100%; margin-bottom: 20px; }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-left { width: 60%; }
        .header-right { text-align: right; }
        .header-left img { max-height: 80px; max-width: 200px; }
        .details { margin-bottom: 30px; }
        .details-left, .details-right { display: inline-block; width: 49%; vertical-align: top; }
        .details-right { text-align: right; }
        .items-table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .items-table th { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; padding: 8px; }
        .items-table td { padding: 8px; border-bottom: 1px solid #eee; }
        .items-table tr.total td { border-top: 2px solid #eee; font-weight: bold; }
        .summary { width: 100%; margin-top: 20px; }
        .summary-right { float: right; width: 40%; }
        .summary-table { width: 100%; }
        .summary-table td { padding: 5px 0; }
        .summary-table .grand-total { font-weight: bold; font-size: 1.1em; }
        .footer { text-align: center; color: #777; margin-top: 30px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="header-left">
                @if($companyInfo && $companyInfo->logo)
                    <img src="{{ asset('/') }}public/black.png" alt="Company Logo">
                @endif
                <address>
                    <strong>{{ $companyInfo->ins_name ?? '' }}</strong><br>
                    {{ $companyInfo->address ?? '' }}<br>
                    Phone: {{ $companyInfo->phone ?? '' }}
                </address>
            </div>
            <div class="header-right">
                <h2>INVOICE</h2>
                <strong>Invoice #:</strong> {{ $order->invoice_no }}<br>
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}<br>
                <strong>Status:</strong> {{ strtoupper($order->status) }}
            </div>
        </div>

        <div class="details">
            <div class="details-left">
                <strong>Billed To:</strong><br>
                {{ $order->customer->name }}<br>
                {{ $order->customer->address }}<br>
                {{ $order->customer->phone }}
            </div>
            <div class="details-right">
                <strong>Shipped To:</strong><br>
                {{ $order->customer->name }}<br>
                {{ $order->shipping_address }}
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderDetails as $detail)
                <tr>
                    <td>{{ $detail->product->name }} ({{ $detail->color }} / {{ $detail->size }})</td>
                    <td style="text-align: center;">{{ $detail->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($detail->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($detail->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-right">
                <table class="summary-table">
                    <tr><td>Subtotal:</td><td style="text-align: right;">{{ number_format($order->subtotal, 2) }}</td></tr>
                    <tr><td>Discount:</td><td style="text-align: right;">- {{ number_format($order->discount, 2) }}</td></tr>
                    <tr><td>Shipping:</td><td style="text-align: right;">{{ number_format($order->shipping_cost, 2) }}</td></tr>
                    <tr class="grand-total"><td>Grand Total:</td><td style="text-align: right;">{{ number_format($order->total_amount, 2) }}</td></tr>
                    <tr><td>Paid:</td><td style="text-align: right;">{{ number_format($order->total_pay, 2) }}</td></tr>
                    <tr class="grand-total"><td>Due:</td><td style="text-align: right;">{{ number_format($order->due, 2) }}</td></tr>
                </table>
            </div>
        </div>
        <div style="clear: both;"></div>

        <div class="footer">
            Thank you for your purchase!
        </div>
    </div>
</body>
</html>
