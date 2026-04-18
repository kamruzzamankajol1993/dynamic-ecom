<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode Print Preview</title>
    <style>
        /* =========================================
           ১. ৩৮x২৫ মিমি বা কাস্টম সিঙ্গেল লেবেল প্রিন্টার লজিক
           ========================================= */
        @if($options['paper_size'] == 'single-38-25' || $options['paper_size'] == 'custom')
            @page { 
                size: {{ $options['paper_size'] == 'single-38-25' ? '38mm 25mm' : ($options['paper_width'] ?? 38).'mm '.($options['paper_height'] ?? 25).'mm' }}; 
                margin: 0; 
            }
            body { margin: 0; padding: 0; font-family: sans-serif; background: white; }
            
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
                overflow: hidden; 
            }

            .store-name { font-weight: bold; font-size: 9px; line-height: 1; margin-bottom: 1px; }
            .product-name { font-size: 8px; font-weight: 600; line-height: 1.1; margin: 0.5mm 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }
            .variant-info { font-size: 7.5px; font-weight: bold; margin: 0.3mm 0; }
            .price { font-weight: bold; font-size: 9px; margin-top: 0.5mm; }

            .barcode-wrapper {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-top: 1px;
            }

            /* PNG বারকোড ইমেজের জন্য বিশেষ স্টাইল */
            .barcode-wrapper img {
                max-width: 100%; /* স্টিকারের উইডথ অনুযায়ী অটো ছোট হবে */
                height: 22px;    /* উচ্চতা ফিক্সড রাখা হয়েছে */
                display: block;
                object-fit: contain;
            }

            .barcode-text {
                font-size: 8px; 
                font-weight: bold;
                margin-top: 1px;
                letter-spacing: 0.5px;
            }

        /* =========================================
           ২. A4 এবং মাল্টিপল লেবেল লজিক (অপরিবর্তিত)
           ========================================= */
        @else
            @page { margin: 5mm; }
            body { font-family: sans-serif; margin: 0; padding: 0; }
            .barcode-container { display: flex; flex-wrap: wrap; gap: 2mm; }
            .barcode-item {
                text-align: center; border: {{ $options['show_border'] ? '1px dotted #ccc' : 'none' }};
                padding: 2mm; margin-bottom: 2mm; display: flex; flex-direction: column; align-items: center;
            }
            .barcode-wrapper img { max-width: 150px; height: auto; }
            .barcode-text { font-size: 10px; font-weight: bold; margin-top: 2px; }
        @endif
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
                        {{ $product['color'] }} {{ !empty($product['color']) && !empty($product['size']) ? '-' : '' }} {{ $product['size'] }}
                    </div>
                @endif

                @if($options['show_price'])
                    <div class="price">Price: {{ number_format($product['price'], 2) }}</div>
                @endif

                <div class="barcode-wrapper">
                    {!! $product['barcode_html'] !!}
                    {{-- মানুষের পড়ার জন্য হিউম্যান রিডেবল কোড --}}
                    <div class="barcode-text">{{ $product['human_code'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>