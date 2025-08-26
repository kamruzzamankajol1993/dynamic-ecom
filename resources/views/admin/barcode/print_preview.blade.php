<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode Print</title>
    <style>
        /* General page setup */
        @page { 
            margin: 5mm; 
        }
        body { 
            font-family: sans-serif; 
            font-size: 10px; 
            margin: 0;
        }

        /* Main container for all barcode layouts */
        .barcode-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-content: flex-start;
        }

        /* --- Layout-Specific Styles --- */

        /* 40 per sheet Layout */
        .barcode-container.a4-40 .barcode-item {
            width: 25.47mm;
            height: 45.69mm;
            margin: 1mm;
            padding: 1.5mm;
            font-size: 6px;
            overflow: hidden;
        }
        
        /* 30 per sheet Layout */
        .barcode-container.a4-30 .barcode-item {
            width: 66.67mm;
            height: 25.4mm;
            margin: 1mm;
            padding: 1.5mm;
            font-size: 8px;
        }
        
        /* 24 per sheet Layout */
        .barcode-container.a4-24 .barcode-item {
            width: 62.99mm;
            height: 33.88mm;
            margin: 1mm;
            padding: 1.5mm;
            font-size: 8px;
        }

        /* 20 per sheet Layout */
        .barcode-container.a4-20 .barcode-item {
            width: 101.6mm;
            height: 25.4mm;
            margin: 1mm;
            padding: 1.5mm;
            font-size: 8px;
        }

        /* 18 per sheet Layout */
        .barcode-container.a4-18 .barcode-item {
            width: 63.5mm;
            height: 46.61mm;
            margin: 1mm;
            padding: 1.5mm;
            font-size: 8px;
        }

        /* 14 per sheet Layout */
        .barcode-container.a4-14 .barcode-item {
            width: 101.6mm;
            height: 33.78mm;
            margin: 1mm;
            padding: 1.5mm;
            font-size: 9px;
        }

        /* 12 per sheet Layout */
        .barcode-container.a4-12 .barcode-item {
            width: 63.5mm;
            height: 71.98mm;
            margin: 1mm;
            padding: 1.5mm;
            font-size: 9px;
        }

        /* 10 per sheet Layout */
        .barcode-container.a4-10 .barcode-item {
            width: 101.6mm;
            height: 50.8mm;
            margin: 1mm;
            padding: 1.5mm;
            font-size: 10px;
        }

        /* Thermal Label Layout (2x1 inch) */
        .barcode-container.thermal-label .barcode-item {
            width: 50.8mm;
            height: 25.4mm;
            padding: 2mm;
            margin: 0;
            border: none;
            font-size: 8px;
        }

        /* Custom size just flows */
        .barcode-container.custom .barcode-item {
             margin: 2mm;
             padding: 2mm;
        }

        /* --- Common Styles for Individual Items --- */
        .barcode-item {
            text-align: center;
            page-break-inside: avoid;
            box-sizing: border-box;
            display: inline-flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: {{ $options['show_border'] ? '1px dotted #ccc;' : 'none' }};
            overflow: hidden;
        }
        .store-name, .product-name, .price {
            width: 100%;
            white-space: normal; 
            word-wrap: break-word;
        }
        .store-name { font-weight: bold; }
        .product-name { margin: 0.5mm 0; }
        .price { font-weight: bold; }
        .barcode-svg {
            transform-origin: top center;
            margin-top: 1mm;
        }
        .barcode-container.a4-40 .barcode-svg,
        .barcode-container.a4-30 .barcode-svg,
        .barcode-container.a4-24 .barcode-svg,
        .barcode-container.a4-20 .barcode-svg {
            transform: scale(0.8);
        }
        .barcode-container.thermal-label .barcode-svg {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="barcode-container {{ $options['paper_size'] }}">
        @foreach($products as $product)
            <div class="barcode-item" 
                @if($options['paper_size'] == 'custom' && !empty($options['paper_width']) && !empty($options['paper_height']))
                    style="width: {{ $options['paper_width'] }}mm; height: {{ $options['paper_height'] }}mm;"
                @endif
            >
                @if($options['show_store_name'])
                    <div class="store-name">{{$ins_name}}</div>
                @endif
                @if($options['show_product_name'])
                    <div class="product-name">{{ $product['name'] }}</div>
                @endif
                 @if($options['show_price'])
                    <div class="price">Price: {{ $product['price'] }}</div>
                @endif
                <div class="barcode-svg">
                    {!! $product['barcode_html'] !!}
                    <div>{{ $product['code'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
