<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background: #fff;
        }
        .invoice-header {
            width: 100%;
            margin-bottom: 40px;
        }
        .invoice-header td {
            padding: 5px;
            vertical-align: top;
        }
        .company-details {
            text-align: left;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h1 {
            font-size: 45px;
            line-height: 45px;
            color: #333;
            margin: 0;
        }
        .billing-details {
            margin-bottom: 40px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #f7f7f7;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
            padding: 8px;
            text-align: left;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .items-table .total td {
            border-top: 2px solid #ddd;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            width: 50%;
            float: right;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 5px;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <table class="invoice-header">
        <tr>
            <td class="company-details">
                <h2>Your Company Name</h2>
                <p>
                    123 Main Street<br>
                    Dhaka, Bangladesh 1205<br>
                    support@yourcompany.com
                </p>
            </td>
            <td class="invoice-details">
                <h1>INVOICE</h1>
                <p>
                    <strong>Invoice #:</strong> {{ $order->invoice_no }}<br>
                    <strong>Created:</strong> {{ $order->created_at->format('F j, Y') }}<br>
                    <strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}
                </p>
            </td>
        </tr>
    </table>

    <div class="billing-details">
        <h3>Bill To:</h3>
        <p>
            <strong>{{ $order->customer->name }}</strong><br>
            {{ $order->customer->address ?? 'N/A' }}<br>
            {{ $order->customer->phone ?? 'N/A' }}
        </p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Description</th>
                <th class="text-center">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $detail->product->name ?? $detail->color }}</strong><br>
                        @if($detail->size !== 'Bundle Offer')
                            <small>Color: {{ $detail->color }}, Size: {{ $detail->size }}</small>
                        @else
                             <small>{{ $detail->size }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td class="text-right">৳{{ number_format($detail->unit_price, 2) }}</td>
                    <td class="text-right">৳{{ number_format($detail->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">৳{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Discount:</td>
            <td class="text-right">- ৳{{ number_format($order->discount, 2) }}</td>
        </tr>
        @if($order->shipping_cost > 0)
            <tr><td>Shipping Cost:</td><td class="text-right">৳{{ number_format($order->shipping_cost) }}</td></tr>
            @endif
        <tr>
            <td><strong>Grand Total:</strong></td>
            <td class="text-right"><strong>৳{{ number_format($order->total_amount, 2) }}</strong></td>
        </tr>
         <tr>
            <td>Paid Amount:</td>
            <td class="text-right">৳{{ number_format($order->total_pay, 2) }}</td>
        </tr>
         <tr>
            <td>Amount Due:</td>
            <td class="text-right">৳{{ number_format($order->due, 2) }}</td>
        </tr>
          @if($order->cod > 0)
         <tr>
            <td>COD Amount:</td>
            <td class="text-right">৳{{ number_format($order->cod, 2) }}</td>
        </tr>
         @endif
    </table>
    
    <div style="clear: both;"></div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>If you have any questions, please contact us at support@yourcompany.com.</p>
    </div>
</div>

</body>
</html>