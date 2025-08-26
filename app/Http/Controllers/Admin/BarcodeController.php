<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Milon\Barcode\DNS1D;
use Mpdf\Mpdf;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('admin.barcode.index');
    }

    // AJAX method to search for products
    public function search(Request $request)
    {
        $term = $request->get('term');
        $products = Product::where('name', 'LIKE', "%{$term}%")
                           ->orWhere('product_code', 'LIKE', "%{$term}%")
                           ->limit(10)
                           ->get(['id', 'name', 'product_code', 'base_price']);
        return response()->json($products);
    }

      // This method now ONLY generates HTML for previewing and printing
    public function print(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'paper_size' => 'required|string',
        ]);

        $productsData = [];
        $barcodeGenerator = new DNS1D();

        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            if ($product) {
                for ($i = 0; $i < $productData['qty']; $i++) {
                    $productsData[] = [
                        'name' => $product->name,
                        'price' => $product->discount_price ?? $product->base_price,
                        'code' => $product->product_code,
                        'barcode_html' => $barcodeGenerator->getBarcodeHTML($product->product_code, 'C128', 1, 25)
                    ];
                }
            }
        }
        
        $options = [
            'show_store_name' => $request->boolean('show_store_name'),
            'show_product_name' => $request->boolean('show_product_name'),
            'show_price' => $request->boolean('show_price'),
            'show_border' => $request->boolean('show_border'),
            'paper_size' => $request->paper_size,
            // Add custom dimensions to the options array
            'paper_width' => $request->paper_width,
            'paper_height' => $request->paper_height,
        ];

        $html = view('admin.barcode.print_preview', [
            'products' => $productsData,
            'options' => $options,
        ])->render();

        return response($html);
    }
}
