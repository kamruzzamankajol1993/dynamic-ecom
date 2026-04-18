<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SystemInformation;
use Milon\Barcode\DNS1D;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('admin.barcode.index');
    }

    /**
     * AJAX method to search for products with variants
     */
    public function search(Request $request)
    {
        $term = $request->get('term');
        
        $products = Product::with(['variants.color'])
                           ->where('name', 'LIKE', "%{$term}%")
                           ->orWhere('product_code', 'LIKE', "%{$term}%")
                           ->limit(10)
                           ->get();

        $results = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'product_code' => $product->product_code,
                'base_price' => $product->base_price,
                'discount_price' => $product->discount_price,
                'variants' => $product->variants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'color_id' => $variant->color_id,
                        'color_name' => $variant->color ? $variant->color->name : 'N/A',
                        'sizes' => $variant->detailed_sizes
                    ];
                })
            ];
        });

        return response()->json($results);
    }

    /**
     * Generates Barcodes with Composite IDs for POS Scanner (PNG Format for Auto-Scaling)
     */
    public function print(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'paper_size' => 'required|string',
        ]);

        $productsData = [];
        $barcodeGenerator = new DNS1D();

        foreach ($request->products as $productData) {
            $product = Product::with('variants')->find($productData['id']);
            
            if ($product) {
                // ১. মূল দাম 
                $baseDisplayPrice = $product->discount_price ?? $product->base_price; 
                $finalPrice = $baseDisplayPrice;

                // ২. কালার ভিত্তিক অ্যাডিশনাল প্রাইস
                if (!empty($productData['variant_id'])) {
                    $variant = $product->variants->where('id', $productData['variant_id'])->first();
                    if ($variant) {
                        $finalPrice += (float) ($variant->additional_price ?? 0);
                    }
                }

                // ৩. ১৬-ডিজিটের জিরো-প্যাডিং লজিক
                $pidStr = str_pad($product->id, 6, '0', STR_PAD_LEFT);

                if (!empty($productData['variant_id']) && !empty($productData['size_id'])) {
                    $vidStr = str_pad($productData['variant_id'], 6, '0', STR_PAD_LEFT);
                    $sidStr = str_pad($productData['size_id'], 4, '0', STR_PAD_LEFT);
                    
                    // স্টিকারে মানুষের পড়ার জন্য (যেমন: 409*504*1)
                    $humanReadableCode = $product->id . '*' . $productData['variant_id'] . '*' . $productData['size_id'];
                } else {
                    // ভেরিয়েন্ট না থাকলে
                    $vidStr = '000000';
                    $sidStr = '0000';
                    $humanReadableCode = (string) $product->id;
                }

                // স্ক্যানারের জন্য ১৬ ডিজিটের মেশিন কোড
                $machineCode = $pidStr . $vidStr . $sidStr; 

                for ($i = 0; $i < $productData['qty']; $i++) {
                    
                    // ফিক্স: Width Factor '1' করা হলো এবং Height '28' করা হলো
                    $barcodeBase64 = $barcodeGenerator->getBarcodePNG($machineCode, 'C128', 1, 28);
                    
                    // ফিক্স: image-rendering: pixelated; যোগ করা হলো যাতে দাগগুলো শার্প থাকে, ব্লার না হয়
                    $barcodeHtml = '<img src="data:image/png;base64,' . $barcodeBase64 . '" alt="barcode" style="max-width: 100%; height: 24px; object-fit: contain; margin: 0 auto; image-rendering: pixelated; image-rendering: -moz-crisp-edges;">';

                    $productsData[] = [
                        'name' => $product->name,
                        'price' => $finalPrice, 
                        'code' => $machineCode, 
                        'human_code' => $humanReadableCode, 
                        'display_code' => $product->product_code, 
                        'color' => $productData['color'] ?? '',
                        'size' => $productData['size'] ?? '',
                        'barcode_html' => $barcodeHtml
                    ];
                }
            }
        }

        $options = [
            'show_store_name' => $request->boolean('show_store_name'),
            'show_product_name' => $request->boolean('show_product_name'),
            'show_variant' => $request->boolean('show_variant'), 
            'show_price' => $request->boolean('show_price'),
            'show_border' => $request->boolean('show_border'),
            'paper_size' => $request->paper_size,
            'paper_width' => $request->paper_width,
            'paper_height' => $request->paper_height,
        ];

        $setting = \App\Models\SystemInformation::first();
        $ins_name = $setting->ins_name ?? 'VANTé';

        $html = view('admin.barcode.print_preview', [
            'products' => $productsData,
            'options' => $options,
            'ins_name' => $ins_name
        ])->render();

        return response($html);
    }
}