<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\HighlightProduct;

class HighlightProductController extends Controller
{
    public function index()
    {
        // Fetch all products for the select dropdowns
        $products = Product::where('status', 1)->select('id', 'name')->get();

        // Fetch the currently saved highlight products
        $firstSection = HighlightProduct::with('product')->where('section', 'first_section')->first();
        $secondSection = HighlightProduct::with('product')->where('section', 'second_section')->first();

        return view('admin.highlight-product.index', compact('products', 'firstSection', 'secondSection'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_section_product_id' => 'required|exists:products,id',
            'first_section_title' => 'nullable|string|max:255',
            'second_section_product_id' => 'required|exists:products,id',
            'second_section_title' => 'nullable|string|max:255',
        ]);

        // Using updateOrCreate to handle both creation and updates
        HighlightProduct::updateOrCreate(
            ['section' => 'first_section'],
            [
                'product_id' => $request->first_section_product_id,
                'title' => $request->first_section_title
            ]
        );

        HighlightProduct::updateOrCreate(
            ['section' => 'second_section'],
            [
                'product_id' => $request->second_section_product_id,
                'title' => $request->second_section_title
            ]
        );

        return redirect()->back()->with('success', 'Highlight products updated successfully.');
    }
}
