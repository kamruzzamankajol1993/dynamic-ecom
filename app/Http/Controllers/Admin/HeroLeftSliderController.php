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
class HeroLeftSliderController extends Controller
{
    public function index()
    {
        $sliders = HeroLeftSlider::with('linkable')->latest()->get();
        return view('admin.hero_left_slider.index', compact('sliders'));
    }

     public function create()
    {
        // <<< 3. FETCH PRODUCTS AND CATEGORIES FOR THE DROPDOWN >>>
        $products = Product::where('status', 1)->orderBy('name')->get(['name', 'id']);
        $categories = Category::where('status', 1)->orderBy('name')->get(['name', 'id']);
        return view('admin.hero_left_slider.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        // <<< MODIFIED: New, more robust validation logic >>>
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp',
            'link_type' => 'required|in:category,product',
            'category_id' => 'nullable|required_if:link_type,category|exists:categories,id',
    'product_id' => 'nullable|required_if:link_type,product|exists:products,id',
        ]);

        $path = $this->uploadImage($request->file('image'));
        
        // <<< MODIFIED: Determine linkable_id and linkable_type on the server >>>
        $linkId = $request->link_type === 'product' ? $request->product_id : $request->category_id;
        $linkableType = $request->link_type === 'product' ? Product::class : Category::class;

        HeroLeftSlider::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $path,
            'linkable_id' => $linkId,
            'linkable_type' => $linkableType,
        ]);

        return redirect()->route('hero-left-slider.index')->with('success', 'Slider created successfully.');
    }

    public function update(Request $request, HeroLeftSlider $heroLeftSlider)
    {
        // <<< MODIFIED: New, more robust validation logic >>>
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'link_type' => 'required|in:category,product',
           'category_id' => 'nullable|required_if:link_type,category|exists:categories,id',
    'product_id' => 'nullable|required_if:link_type,product|exists:products,id',
        ]);

        $path = $heroLeftSlider->image;
        if ($request->hasFile('image')) {
            $this->deleteImage($heroLeftSlider->image);
            $path = $this->uploadImage($request->file('image'));
        }
        
        // <<< MODIFIED: Determine linkable_id and linkable_type on the server >>>
        $linkId = $request->link_type === 'product' ? $request->product_id : $request->category_id;
        $linkableType = $request->link_type === 'product' ? Product::class : Category::class;

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
    

    public function edit(HeroLeftSlider $heroLeftSlider)
    {
        // <<< 4. FETCH PRODUCTS AND CATEGORIES FOR THE DROPDOWN >>>
        $products = Product::where('status', 1)->orderBy('name')->get(['name', 'id']);
        $categories = Category::where('status', 1)->orderBy('name')->get(['name', 'id']);
        return view('admin.hero_left_slider.edit', compact('heroLeftSlider', 'products', 'categories'));
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