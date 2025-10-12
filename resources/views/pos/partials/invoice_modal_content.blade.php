<div class="invoice-box">
    <h4 class="text-center">{{$front_ins_name}}</h4>
    <p class="text-center small">{{$front_ins_add}}<br>
        Phone: {{$front_ins_phone}}<br>
        Email: {{$front_ins_email}}<br>
        Website: spotlightattires.com
    </p>

    <hr>
    <p><strong>Invoice #:</strong> {{ $order->invoice_no }}<br>
       <strong>Date:</strong> {{ $order->created_at->format('d/m/Y') }}<br>
       <strong>Customer:</strong> {{ $order->customer->name }}</p>
    <hr>
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $detail)
                <tr>
                    <td>
                        {{ $detail->product->name ?? $detail->color }}
                        @if($detail->size !== 'Bundle Offer')
                            <small class="d-block text-muted">{{ $detail->color }}, {{ $detail->size }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td class="text-end">৳{{ number_format($detail->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <div class="d-flex justify-content-between"><span>Subtotal:</span> <span>৳{{ number_format($order->subtotal, 2) }}</span></div>
    <div class="d-flex justify-content-between"><span>Discount:</span> <span>- ৳{{ number_format($order->discount, 2) }}</span></div>
    @if($order->shipping_cost > 0)
    <div class="d-flex justify-content-between"><span>Shipping Cost:</span> <span>৳{{ number_format($order->shipping_cost, 2) }}</span></div>
    @endif
    <div class="d-flex justify-content-between"><strong>Total:</strong> <strong>৳{{ number_format($order->total_amount, 2) }}</strong></div>
    <div class="d-flex justify-content-between"><span>Paid:</span> <span>৳{{ number_format($order->total_pay, 2) }}</span></div>
    <div class="d-flex justify-content-between"><span>Due:</span> <span>৳{{ number_format($order->due, 2) }}</span></div>
    @if($order->cod > 0)
    <div class="d-flex justify-content-between"><span>COD Amount:</span> <span>৳{{ number_format($order->cod, 2) }}</span></div>
    @endif
    <hr>
    <p class="text-center small">Thank you for your purchase!</p>
</div>