<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Receipt - {{ $order->invoice_no }}</title>
   <style>
        body {
            color: #333639;
            font-family: Arial, Helvetica, sans-serif;
        }

        @page  {
      size: 75mm 100mm;
      margin: 3px;
    }

        table {
            width: 100%;
        }

        .first_table tr td {
            width: 50%;
        }

        .first_table tr td:nth-child(1) img {
            height:70px;
            width:450px;
        }

        .first_table tr td:nth-child(2)
        {
            text-align: right;
        }

        .first_table tr td:nth-child(2) img {
           
        }

        .first_table tr td:nth-child(2) p {
            font-size:8px;
            padding:0;
            margin:0;
        }
        .first_table tr td:nth-child(2) h4 {
            font-size:12px;
            padding:0;
            margin:0;
        }

        hr{
            margin-bottom: 0;
            margin-top:0;
        }

        .second_table tr td {
            font-size: 13px;
            vertical-align: top;
        }

        .second_table tr td:nth-child(1) {
            width: 40%;
        }
        .second_table tr td p{
            margin: 0;
            padding: 2px;
        }

        .second_table tr td:nth-child(2)
        {
            font-weight: bold;
            width: 60%;
        }

        .third_table {
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 10px;
        }
        .third_table th {
            padding: 2px;
            text-align: left;
            background-color: #F8F9FA;
            border: 1px solid #e9ecef;
        }

        .third_table tr th:nth-child(2)
        {
            width: 40px;
        }

        .third_table td {
            border: 1px solid #e9ecef;
            padding: 4px;
        }

        .forth_table
        {
            font-size: 12px;
            vertical-align: top;
        }
        .forth_table tr td:nth-child(1)
        {
            width: 40%;
        }
        .forth_table tr td:nth-child(2)
        {
            width: 60%;
        }

        .inner-table tr td:nth-child(1)
        {
            width: 65%;
        }
    </style>
</head>
<body>
    {{-- The top part of the receipt is unchanged --}}
    <table class="first_table">
        <tr>
            <td>
            </td>
            <td>
                 @if($companyInfo && $companyInfo->logo)
                    <img src="{{ asset('/') }}{{$front_logo_name}}" style="height: 30px;" alt="Logo">
                @endif
                <p style="font-weight: bold;">Merchant No: {{ $front_ins_phone ?? '' }}</p>
                <p style="font-weight: bold;">{{ $front_ins_email ?? '' }}</p>
                <h4>spotlightattires.com</h4>
            </td>
        </tr>
    </table>
    <hr>
    <table class="second_table">
        <tr>
            <td>Customer Name</td>
            <td>:{{ $order->customer->name }}</td>
        </tr>
        <tr>
            <td>Mobile Number</td>
            <td>:{{ $order->customer->phone }}</td>
        </tr>
        <tr>
            <td>Address</td>
            <td>:{{ $order->shipping_address }}</td>
        </tr>
    </table>
    
    {{-- --- TABLE UPDATED --- --}}
    <table class="third_table">
        <thead>
        <tr>
           <th style="width:5%; text-align: center;">#</th>
           <th style="width:55%">Item</th>
           <th style="width:15%; text-align: center;">Rate</th>
           <th style="width:5%; text-align: center;">Q.T</th>
           <th style="width:20%; text-align: center;">Amount</th>
        </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $detail)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $detail->product->name }} ({{ $detail->size }})</td>
                <td style="text-align: center;">{{ number_format($detail->unit_price, 2) }}</td>
                <td style="text-align: center;">{{ $detail->quantity }}</td>
                <td style="text-align: center;">{{ number_format($detail->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{-- --- END OF UPDATE --- --}}

    <table class="forth_table">
        <tr>
        <td>
            <h4>COD Charge:{{ number_format($order->cod, 2) }}
            <br> <span style="font-size:8px;">Powered By ResNova Tech Limited</span>
            </h4>
        </td>
        <td>
          <table class="inner-table">
            <tr>
              <td>Sub Total</td>
              <td>{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
              <td>Ship.Charge</td>
              <td>{{ number_format($order->shipping_cost, 2) }}</td>
            </tr>
            @if($order->discount  == 0 )
    
            @else
            <tr>
                <td>Discount</td>
                <td>{{ number_format($order->discount, 2) }}</td>
              </tr>
              @endif
            <tr style="font-weight:bold">
              <td>Total</td>
              <td>{{ number_format($order->total_amount, 2) }}</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
     @if(!empty($order->notes))
     <table class="second_table">
        <tr>
            <td><b>Note:</b> {{ $order->notes }}</td>
        </tr>
    </table>
    @endif
</body>
</html>