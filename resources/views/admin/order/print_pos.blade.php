<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $order->invoice_no }}</title>
    <style>
        body { font-family: monospace; font-size: 10px; color: #000; margin: 0; padding: 5px; }
        .receipt-container { width: 100%; }
        .header { text-align: center; margin-bottom: 10px; }
        .header img { max-width: 60%; max-height: 40px; }
        .header h4 { margin: 5px 0 0; font-size: 12px; }
        .header p { margin: 2px 0; }
        .info, .items, .totals { border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
        .info p { margin: 2px 0; }
        .items table { width: 100%; }
        .items th, .items td { padding: 2px 0; }
        .items th { text-align: left; border-bottom: 1px dashed #000; }
        .items .price { text-align: right; }
        .totals table { width: 100%; }
        .totals td { padding: 1px 0; }
        .totals .label { text-align: left; }
        .totals .value { text-align: right; }
        .totals .grand-total { font-weight: bold; font-size: 12px; }
        .footer { text-align: center; margin-top: 10px; padding-top: 5px; border-top: 1px dashed #000; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            @if($companyInfo && $companyInfo->logo)
                <img src="{{ asset('/') }}public/black.png" alt="Logo">
            @endif
            <h4>{{ $companyInfo->ins_name ?? 'Your Company' }}</h4>
            <p>{{ $companyInfo->address ?? '' }}</p>
            <p>Phone: {{ $companyInfo->phone ?? '' }}</p>
        </div>

        <div class="info">
            <p>Order: #{{ $order->invoice_no }}</p>
            <p>Date: {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/y H:i') }}</p>
            <p>Customer: {{ $order->customer->name }}</p>
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="price">Qty</th>
                        <th class="price">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderDetails as $detail)
                    <tr>
                        <td colspan="3">{{ $detail->product->name }} ({{ $detail->size }})</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="price">{{ $detail->quantity }} x {{ number_format($detail->unit_price, 2) }}</td>
                        <td class="price">{{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <table>
                <tr><td class="label">Subtotal:</td><td class="value">{{ number_format($order->subtotal, 2) }}</td></tr>
                <tr><td class="label">Discount:</td><td class="value">-{{ number_format($order->discount, 2) }}</td></tr>
                <tr><td class="label">Shipping:</td><td class="value">{{ number_format($order->shipping_cost, 2) }}</td></tr>
                <tr class="grand-total"><td class="label">Total:</td><td class="value">{{ number_format($order->total_amount, 2) }}</td></tr>
                <tr><td class="label">Paid:</td><td class="value">{{ number_format($order->total_pay, 2) }}</td></tr>
                <tr class="grand-total"><td class="label">Due:</td><td class="value">{{ number_format($order->due, 2) }}</td></tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank You!</p>
        </div>
    </div>
</body>
</html>
