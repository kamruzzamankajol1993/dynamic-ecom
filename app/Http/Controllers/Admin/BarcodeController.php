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
        
        // Product-এর সাথে variants এবং color রিলেশন লোড করা হচ্ছে
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
                        'color_id' => $variant->color_id, // POS-এর জন্য গুরুত্বপূর্ণ
                        'color_name' => $variant->color ? $variant->color->name : 'N/A',
                        'sizes' => $variant->detailed_sizes // JSON ডাটা প্রসেসিং
                    ];
                })
            ];
        });

        return response()->json($results);
    }

    /**
     * Generates Barcodes with Composite IDs for POS Scanner
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
            // ১. মূল দাম (Base or Discount Price)
            $baseDisplayPrice = $product->discount_price ?? $product->base_price; 
            $finalPrice = $baseDisplayPrice;

            // ২. নতুন লজিক: কালার ভিত্তিক অ্যাডিশনাল প্রাইস যোগ করা
            if (!empty($productData['variant_id'])) {
                $variant = $product->variants->where('id', $productData['variant_id'])->first();
                
                if ($variant) {
                    // প্রোডাক্ট ভেরিয়েশন টেবিল থেকে কালার wise অতিরিক্ত দাম থাকলে তা যোগ হবে
                    $finalPrice += (float) ($variant->additional_price ?? 0);
                }
            }

            /* পুরোনো সাইজ ভিত্তিক লজিকটি নিচে রাখা হলো কিন্তু এটি আর ব্যবহৃত হবে না 
               যেহেতু আমরা সরাসরি ভেরিয়েন্টের additional_price নিচ্ছি।
            */

            // বারকোড ভ্যালু জেনারেশন
            $barcodeValue = $product->id;
            if (!empty($productData['variant_id']) && !empty($productData['size_id'])) {
                $barcodeValue .= '*' . $productData['variant_id'] . '*' . $productData['size_id'];
            }

            for ($i = 0; $i < $productData['qty']; $i++) {
                $productsData[] = [
                    'name' => $product->name,
                    'price' => $finalPrice, // কালার ভিত্তিক আপডেট করা দাম
                    'code' => $barcodeValue, 
                    'display_code' => $product->product_code, 
                    'color' => $productData['color'] ?? '',
                    'size' => $productData['size'] ?? '',
                    // HTML এর বদলে SVG ব্যবহার করা হলো এবং দাগগুলো মোটা করার জন্য 1 এর জায়গায় 1.2 দেওয়া হলো
// উচ্চতা 22 করা হলো এবং শেষের 'false' দিয়ে SVG-এর ভেতরের ডাবল টেক্সট বন্ধ করা হলো
'barcode_html' => $barcodeGenerator->getBarcodeSVG($barcodeValue, 'C128', 1, 22, 'black', false)
                ];
            }
        }
    }

    // বাকি কোড (Options এবং View Render) অপরিবর্তিত থাকবে...
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