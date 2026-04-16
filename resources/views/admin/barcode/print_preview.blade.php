<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode Print Preview</title>
  <style>
        /* =========================================
           ১. নতুন লজিক: POS / Single Label Printer (38x25mm অথবা Custom)
           ========================================= */
       /* =========================================
   ১. নতুন লজিক: POS / Single Label Printer (38x25mm অথবা Custom)
   ========================================= */
@if($options['paper_size'] == 'single-38-25' || $options['paper_size'] == 'custom')
    @page { 
        size: {{ $options['paper_size'] == 'single-38-25' ? '38mm 25mm' : ($options['paper_width'] ?? 38).'mm '.($options['paper_height'] ?? 25).'mm' }}; 
        margin: 0; 
    }
    body { 
        margin: 0; 
        padding: 0; 
        font-family: sans-serif; 
    }
    
    .barcode-container { 
        display: flex; 
        flex-direction: column;
        align-items: center; 
    }
    
    .barcode-item {
        width: {{ $options['paper_size'] == 'single-38-25' ? '38mm' : ($options['paper_width'] ?? 38).'mm' }};
        height: {{ $options['paper_size'] == 'single-38-25' ? '25mm' : ($options['paper_height'] ?? 25).'mm' }};
        margin: 0 auto;
        padding: 1mm;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center; 
        text-align: center; 
        border: {{ $options['show_border'] ? '1px dotted #ccc' : 'none' }};
        page-break-after: always;
        page-break-inside: avoid;
        font-size: 8px;
        overflow: hidden; 
    }
    
    .barcode-item:last-child { page-break-after: auto; }

    /* জাদুকরী কোড: টেক্সট অনেক বড় হলেও ২ লাইনে যাবে না, ডট ডট (...) হয়ে যাবে */
    .store-name, .product-name, .price, .variant-info {
        width: 100%; 
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.1;
        text-align: center; 
    }
    
    .store-name { font-weight: bold; margin-bottom: 1px; font-size: 9.5px; }
    .product-name { margin: 0.5mm 0; font-weight: 600; font-size: 8.5px; }
    .variant-info { font-size: 8px; color: #333; font-weight: bold; text-transform: capitalize; margin: 0.5mm 0; }
    .price { font-weight: bold; margin-top: 0.5mm; font-size: 9px; }

    .barcode-svg { 
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-top: 1.5px;
        width: 100%;
    }

    /* SVG এর সাইজ ফিক্স করা হলো যাতে ওভারল্যাপ না হয় */
    .barcode-svg svg {
        max-width: 100% !important;
        width: auto !important;
        height: 22px !important; 
        
    }
    
    /* ব্যাকআপ ফিক্স: যদি লাইব্রেরি জোর করে টেক্সট বসায়, তবে CSS তা লুকিয়ে ফেলবে */
    .barcode-svg svg text {
        display: none !important;
    }

    .barcode-text {
        font-size: 7.5px; 
        font-weight: bold;
        margin-top: 1px;
        letter-spacing: 0.2px;
    }

        /* =========================================
           ২. পুরোনো লজিক: A4 এবং অন্যান্য মাল্টিপল লেবেল (অপরিবর্তিত)
           ========================================= */
        @else
            @page { margin: 5mm; }
            body { font-family: sans-serif; margin: 0; padding: 0; }
            
            .barcode-container {
                display: flex; flex-wrap: wrap; justify-content: flex-start; align-content: flex-start;
            }

            .barcode-item {
                text-align: center; page-break-inside: avoid; box-sizing: border-box;
                display: inline-flex; flex-direction: column; justify-content: center; align-items: center;
                border: {{ $options['show_border'] ? '1px dotted #ccc' : 'none' }};
                overflow: hidden; margin: 1mm; padding: 1.5mm;
            }

            .barcode-container.a4-40 .barcode-item { width: 25.47mm; height: 45.69mm; font-size: 7px; }
            .barcode-container.a4-40 .barcode-svg { transform: scale(0.8); transform-origin: top center; margin-top: 1mm; }

            .barcode-container.a4-30 .barcode-item { width: 66.67mm; height: 25.4mm; font-size: 8px; }
            .barcode-container.a4-30 .barcode-svg { transform: scale(0.85); transform-origin: top center; margin-top: 1mm; }

            .barcode-container.thermal-label .barcode-item { width: 50.8mm; height: 25.4mm; border: none; font-size: 9px; }
            .barcode-container.thermal-label .barcode-svg { transform: scale(0.95); transform-origin: top center; margin-top: 1mm; }
        @endif

        /* =========================================
           ৩. কমন স্টাইলস (সবার জন্য)
           ========================================= */
        .store-name, .product-name, .price, .variant-info {
            width: 100%; 
            white-space: normal; 
            word-wrap: break-word; 
            line-height: 1.1;
            text-align: center; /* নিশ্চিত করা হলো টেক্সট সেন্টারে থাকবে */
        }
        
        .store-name { font-weight: bold; margin-bottom: 1px; font-size: 1.1em; }
        .product-name { margin: 0.5mm 0; font-weight: 500; }
        .variant-info { font-size: 0.9em; color: #333; font-weight: bold; text-transform: capitalize; margin: 0.5mm 0; }
        .price { font-weight: bold; margin-top: 0.5mm; }
    </style>
</head>
<body>
    <div class="barcode-container {{ $options['paper_size'] }}">
        @foreach($products as $product)
            <div class="barcode-item">
                @if($options['show_store_name'])
                    <div class="store-name">{{ $ins_name }}</div>
                @endif

                @if($options['show_product_name'])
                    <div class="product-name">{{ $product['name'] }}</div>
                @endif

                @if($options['show_variant'] && (!empty($product['color']) || !empty($product['size'])))
                    <div class="variant-info">
                        {{ $product['color'] }} @if(!empty($product['color']) && !empty($product['size'])) - @endif {{ $product['size'] }}
                    </div>
                @endif

                @if($options['show_price'])
                    <div class="price">Price: {{ number_format($product['price'], 2) }}</div>
                @endif

                <div class="barcode-svg">
    {!! $product['barcode_html'] !!}
    <div class="barcode-text">{{ $product['code'] }}</div>
</div>
            </div>
        @endforeach
    </div>
</body>
</html>