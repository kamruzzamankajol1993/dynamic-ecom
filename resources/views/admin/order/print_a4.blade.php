<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
       body { 
        /* Use the key defined in 'fontdata' above */
        font-family: 'nikosh', sans-serif; 
        font-size: 12px; 
        color: #333; 
    }
        .invoice-box { max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .address-table { width: 100%; margin-bottom: 30px; }
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
        <table style="width: 100%; vertical-align: top; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    @if($companyInfo && $companyInfo->logo)
                        <img src="{{ asset('/') }}{{$front_logo_name}}" style="height: 30px; margin-bottom: 10px;" alt="Company Logo">
                    @endif
                    <address style="margin: 0; line-height: 1.5;">
                        <strong>{{ $front_ins_name ?? '' }}</strong><br>
                        {{ $front_ins_add ?? '' }}<br>
                        Phone: {{ $front_ins_phone ?? '' }}
                        {{-- Conditionally display the secondary phone number --}}
                        @if(!empty($front_ins_phone_one))
                            / {{ $front_ins_phone_one }}
                        @endif
                        <br>
                        Email: {{ $front_ins_email ?? '' }}<br>
                        Website: spotlightattires.com
                    </address>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right; line-height: 1.5;">
                    <h2 style="margin: 0 0 10px 0;">INVOICE</h2>
                    <strong>Invoice #:</strong> {{ $order->invoice_no }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}<br>
                    <strong>Status:</strong> {{ strtoupper($order->status) }}
                </td>
            </tr>
        </table>

        <table class="address-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>Billed To:</strong><br>
                    {{ $order->customer->name }}<br>
                    {{ $order->customer->address }}<br>
                    {{ $order->customer->phone }}
                     @if($order->customer->secondary_phone)
                        <br>{{ $order->customer->secondary_phone }} (Secondary)
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <strong>Shipped To:</strong><br>
                    {{ $order->customer->name }}<br>
                    {{ $order->shipping_address }}
                </td>
            </tr>
        </table>

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
                    <td style="text-align: right;">
                        @if(isset($detail->discount) && $detail->discount > 0 && isset($detail->after_discount_price))
                <span style="text-decoration: line-through; color: #777;">
                    {{ number_format($detail->unit_price, 2) }}
                </span>
                <br>
                <strong>
                    {{ number_format($detail->after_discount_price / $detail->quantity, 2) }}
                </strong>
            @elseif($detail->product && $detail->product->base_price > $detail->unit_price)
                <span style="text-decoration: line-through; color: #777;">
                    {{ number_format($detail->product->base_price, 2) }}
                </span>
                <br>
                <strong>{{ number_format($detail->unit_price, 2) }}</strong>
            @else
                {{ number_format($detail->unit_price, 2) }}
            @endif</td>
                    <td style="text-align: right;">@if(isset($detail->discount) && $detail->discount > 0)
                {{ number_format($detail->after_discount_price, 2) }}
            @else
                {{ number_format($detail->subtotal, 2) }}
            @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-right">

                @php
                    $hasLineItemDiscounts = false;
                    $trueOriginalSubtotal = 0;

                    foreach ($order->orderDetails as $detail) {
                        // $trueOriginalSubtotal is the sum of (qty * original unit price)
                        $trueOriginalSubtotal += $detail->after_discount_price; 
                        
                        if (isset($detail->discount) && $detail->discount > 0) {
                            $hasLineItemDiscounts = true;
                        }
                    }
                @endphp

                <table class="summary-table">
                    @if($hasLineItemDiscounts)
                        {{-- If line-item discounts exist, show the *original* subtotal --}}
                        {{-- and hide the main discount row. --}}
                        <tr>
                            <td>Subtotal:</td>
                            <td style="text-align: right;">{{ number_format($trueOriginalSubtotal, 2) }}</td>
                        </tr>
                    @else
                        {{-- Otherwise, use the default behavior --}}
                        <tr>
                            <td>Subtotal:</td>
                            <td style="text-align: right;">{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        
                        {{-- Only show the main discount if it exists AND no line-item discounts were found --}}
                        @if($order->discount > 0)
                        <tr>
                            <td>Discount:</td>
                            <td style="text-align: right;">- {{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif
                    @endif
                    {{-- NEW: Reward Point Discount --}}
                    @if($order->reward_point_discount > 0)
                    <tr>
                        <td>Reward Discount:</td>
                        <td style="text-align: right;">- {{ number_format($order->reward_point_discount, 2) }}</td>
                    </tr>
                    @endif
                    {{-- -------------------------- --}}
                    <tr><td>Shipping:</td><td style="text-align: right;">{{ number_format($order->shipping_cost, 2) }}</td></tr>
                    <tr class="grand-total"><td>Grand Total:</td><td style="text-align: right;">{{ number_format($order->total_amount, 2) }}</td></tr>
                    <tr><td>Paid:</td><td style="text-align: right;">{{ number_format($order->total_pay, 2) }}</td></tr>
                    @if($order->cod > 0)
                        <tr><td>COD Amount:</td><td style="text-align: right;">{{ number_format($order->cod, 2) }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
        <div style="clear: both;"></div>

        @if(!empty($order->notes))
            <div class="notes-section" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
                <strong>Notes:</strong>
                <p style="margin-top: 5px; color: #555;">{{ $order->notes }}</p>
            </div>
        @endif

        <div class="footer">
            Thank you for your purchase!
        </div>
    </div>
</body>
</html>