<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroLeftSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use App\Models\Product;  // <<< 1. IMPORT PRODUCT MODEL
use App\Models\Category;
use App\Models\BundleOffer;
class HeroLeftSliderController extends Controller
{
    public function index()
    {
        $sliders = HeroLeftSlider::with('linkable')->latest()->get();
        return view('admin.hero_left_slider.index', compact('sliders'));
    }

      public function create()
    {
        $products = Product::where('status', 1)->orderBy('name')->get(['name', 'id']);
        $categories = Category::where('status', 1)->orderBy('name')->get(['name', 'id']);
        $bundleOffers = BundleOffer::where('status', 1)->orderBy('name')->get(['name', 'id']); // <<< FETCH BUNDLE OFFERS
        return view('admin.hero_left_slider.create', compact('products', 'categories', 'bundleOffers')); // <<< PASS TO VIEW
    }

    public function store(Request $request)
    {
        $request->validate([
        'title' => 'required|string|max:255',
        'subtitle' => 'nullable|string|max:255',
        // dimension validation added here
        'image' => 'required|image|mimes:jpeg,png,jpg,webp|dimensions:width=680,height=695',
        'link_type' => 'required|in:category,product,bundle_offer',
        'category_id' => 'nullable|required_if:link_type,category|exists:categories,id',
        'product_id' => 'nullable|required_if:link_type,product|exists:products,id',
        'bundle_offer_id' => 'nullable|required_if:link_type,bundle_offer|exists:bundle_offers,id',
    ], [
        // Custom error message for dimensions
        'image.dimensions' => 'The image must be exactly 680x695 pixels.'
    ]);

        $path = $this->uploadImage($request->file('image'));
        
        $linkId = null;
        $linkableType = null;

        // <<< LOGIC TO HANDLE ALL LINK TYPES >>>
        switch ($request->link_type) {
            case 'product':
                $linkId = $request->product_id;
                $linkableType = Product::class;
                break;
            case 'category':
                $linkId = $request->category_id;
                $linkableType = Category::class;
                break;
            case 'bundle_offer':
                $linkId = $request->bundle_offer_id;
                $linkableType = BundleOffer::class;
                break;
        }

        HeroLeftSlider::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $path,
            'linkable_id' => $linkId,
            'linkable_type' => $linkableType,
        ]);

        return redirect()->route('hero-left-slider.index')->with('success', 'Slider created successfully.');
    }

    public function edit(HeroLeftSlider $heroLeftSlider)
    {
        $products = Product::where('status', 1)->orderBy('name')->get(['name', 'id']);
        $categories = Category::where('status', 1)->orderBy('name')->get(['name', 'id']);
        $bundleOffers = BundleOffer::where('status', 1)->orderBy('name')->get(['name', 'id']); // <<< FETCH BUNDLE OFFERS
        return view('admin.hero_left_slider.edit', compact('heroLeftSlider', 'products', 'categories', 'bundleOffers')); // <<< PASS TO VIEW
    }

    public function update(Request $request, HeroLeftSlider $heroLeftSlider)
    {
        $request->validate([
        'title' => 'required|string|max:255',
        'subtitle' => 'nullable|string|max:255',
        // dimension validation added here (nullable because it's update)
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|dimensions:width=680,height=695',
        'link_type' => 'required|in:category,product,bundle_offer',
        'category_id' => 'nullable|required_if:link_type,category|exists:categories,id',
        'product_id' => 'nullable|required_if:link_type,product|exists:products,id',
        'bundle_offer_id' => 'nullable|required_if:link_type,bundle_offer|exists:bundle_offers,id',
        'status' => 'required|boolean',
    ], [
        'image.dimensions' => 'The image must be exactly 680x695 pixels.'
    ]);

        $path = $heroLeftSlider->image;
        if ($request->hasFile('image')) {
            $this->deleteImage($heroLeftSlider->image);
            $path = $this->uploadImage($request->file('image'));
        }
        
        $linkId = null;
        $linkableType = null;
        
        // <<< LOGIC TO HANDLE ALL LINK TYPES >>>
        switch ($request->link_type) {
            case 'product':
                $linkId = $request->product_id;
                $linkableType = Product::class;
                break;
            case 'category':
                $linkId = $request->category_id;
                $linkableType = Category::class;
                break;
            case 'bundle_offer':
                $linkId = $request->bundle_offer_id;
                $linkableType = BundleOffer::class;
                break;
        }

        $heroLeftSlider->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $path,
            'linkable_id' => $linkId,
            'linkable_type' => $linkableType,
            'status' => $request->status,
        ]);

        return redirect()->route('hero-left-slider.index')->with('success', 'Slider updated successfully.');
    }
    

    

  

    public function destroy(HeroLeftSlider $heroLeftSlider)
    {
        $this->deleteImage($heroLeftSlider->image);
        $heroLeftSlider->delete();
        return redirect()->route('hero-left-slider.index')->with('success', 'Slider deleted successfully.');
    }

    private function uploadImage($image)
    {
        $imageName = Str::uuid() . '.' . 'webp';
        $directory = 'uploads/hero-sliders';
        $destinationPath = public_path($directory);

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        Image::read($image->getRealPath())->resize(680, 695)->save($destinationPath . '/' . $imageName);
        return $directory . '/' . $imageName;
    }

    private function deleteImage($path)
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}