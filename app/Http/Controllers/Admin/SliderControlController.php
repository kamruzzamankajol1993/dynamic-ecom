<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SliderControl;
use App\Models\Product;

class SliderControlController extends Controller
{
    public function index()
    {
        $mainSlider = SliderControl::firstOrCreate(
            ['section_key' => 'main_slider'],
            ['title' => 'Main Slider (2 Products)', 'product_ids' => []]
        );
        $topBanner = SliderControl::firstOrCreate(
            ['section_key' => 'top_banner'],
            ['title' => 'Top Banner (1 Product)', 'product_ids' => []]
        );
        $bottomBanners = SliderControl::firstOrCreate(
            ['section_key' => 'bottom_banners'],
            ['title' => 'Bottom Banners (2 Products)', 'product_ids' => []]
        );

        $mainSlider->products = Product::find($mainSlider->product_ids ?? []);
        $topBanner->products = Product::find($topBanner->product_ids ?? []);
        $bottomBanners->products = Product::find($bottomBanners->product_ids ?? []);

        return view('admin.slider-control.index', compact('mainSlider', 'topBanner', 'bottomBanners'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'main_slider_products' => 'nullable|array|max:2',
            'top_banner_products' => 'nullable|array|max:1',
            'bottom_banners_products' => 'nullable|array|max:2',
        ]);

        SliderControl::updateOrCreate(
            ['section_key' => 'main_slider'],
            ['product_ids' => $request->main_slider_products ?? []]
        );
        SliderControl::updateOrCreate(
            ['section_key' => 'top_banner'],
            ['product_ids' => $request->top_banner_products ?? []]
        );
        SliderControl::updateOrCreate(
            ['section_key' => 'bottom_banners'],
            ['product_ids' => $request->bottom_banners_products ?? []]
        );

        return redirect()->back()->with('success', 'Slider settings updated successfully!');
    }

    
         public function searchProducts(Request $request)
    {
        $term = $request->get('term');

        if (empty($term)) {
            return response()->json([]);
        }

        $products = Product::where('name', 'LIKE', "%{$term}%")
                           ->orWhere('product_code', 'LIKE', "%{$term}%")
                           ->orderByRaw("CASE 
                                WHEN name LIKE '{$term}%' THEN 1
                                WHEN product_code LIKE '{$term}%' THEN 2
                                ELSE 3
                            END")
                           ->limit(10)
                           ->get(['id', 'name', 'product_code']);
        
        return response()->json($products);
    }
    }

