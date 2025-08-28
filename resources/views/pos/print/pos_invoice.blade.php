<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .container { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        h3, p { margin: 0; }
        hr { border: 0; border-top: 1px dashed #000; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center">Your Company Name</h3>
        <p class="text-center" style="font-size: 8pt;">123 Main Street, Dhaka<br>Phone: 0123456789</p>
        <hr>
        <p>Invoice #: {{ $order->invoice_no }}<br>
           Date: {{ $order->created_at->format('d/m/Y h:i A') }}<br>
           Customer: {{ $order->customer->name }}</p>
        <hr>
        <table>
            <thead>
                <tr>
                    <th style="text-align: left;">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderDetails as $detail)
                <tr>
                    <td>
                        {{ $detail->product->name ?? $detail->color }}
                        @if($detail->size !== 'Bundle Offer')
                            <br><small>{{ $detail->color }}, {{ $detail->size }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td class="text-right">৳{{ number_format($detail->subtotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <table>
            <tr><td>Subtotal:</td><td class="text-right">৳{{ number_format($order->subtotal) }}</td></tr>
            <tr><td>Discount:</td><td class="text-right">- ৳{{ number_format($order->discount) }}</td></tr>
             @if($order->shipping_cost > 0)
        <tr>
            <td>Shipping Cost:</td>
            <td class="text-right">৳{{ number_format($order->shipping_cost, 2) }}</td>
        </tr>
        @endif
            <tr><td><strong>Total:</strong></td><td class="text-right"><strong>৳{{ number_format($order->total_amount) }}</strong></td></tr>
            <tr><td>Paid:</td><td class="text-right">৳{{ number_format($order->total_pay) }}</td></tr>
            <tr><td>Due:</td><td class="text-right">৳{{ number_format($order->due) }}</td></tr>
              @if($order->cod > 0)
            <tr><td>COD Amount:</td><td class="text-right">৳{{ number_format($order->cod) }}</td></tr>
            @endif
        </table>
        <hr>
        <p class="text-center">Thank you for your purchase!</p>
    </div>
</body>
</html>