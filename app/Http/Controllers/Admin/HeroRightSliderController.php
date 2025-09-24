<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroRightSlider;
use App\Models\BundleOffer;
use App\Models\Category;
use App\Models\ExtraCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class HeroRightSliderController extends Controller
{
    public function index()
    {
        $sliders = HeroRightSlider::all()->keyBy('position');
        $bundleOffers = BundleOffer::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();
        $extraCategories = ExtraCategory::where('status', 1)->get();

        return view('admin.hero_right_slider.index', compact('sliders', 'bundleOffers', 'categories', 'extraCategories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'top.title' => 'required|string',
            'top.image' => 'nullable|image',
            'top.bundle_offer_id' => 'required|exists:bundle_offers,id',

            'bottom_left.title' => 'required|string',
            'bottom_left.image' => 'nullable|image',
            'bottom_left.link_type' => 'required|in:category,extracategory',
            'bottom_left.link_id' => 'required|integer',

            'bottom_right.title' => 'required|string',
            'bottom_right.image' => 'nullable|image',
            'bottom_right.link_type' => 'required|in:category,extracategory',
            'bottom_right.link_id' => 'required|integer',
        ]);

        $this->updateSection('top', $request->input('top'), $request->file('top.image'));
        $this->updateSection('bottom_left', $request->input('bottom_left'), $request->file('bottom_left.image'));
        $this->updateSection('bottom_right', $request->input('bottom_right'), $request->file('bottom_right.image'));

        return redirect()->back()->with('success', 'Hero Right Section updated successfully.');
    }

    private function updateSection($position, $data, $imageFile)
    {
        $slider = HeroRightSlider::where('position', $position)->firstOrFail();
        $updateData = [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'status' => isset($data['status']) ? 1 : 0,
        ];

        if ($imageFile) {
            $this->deleteImage($slider->image);
            if ($position === 'top') {
                $updateData['image'] = $this->uploadImage($imageFile, 800, 400); // Example dimensions for top
            } else {
                $updateData['image'] = $this->uploadImage($imageFile, 330, 300); // Specific dimensions for bottom
            }
        }

        if ($position === 'top') {
            $updateData['bundle_offer_id'] = $data['bundle_offer_id'];
        } else {
            $updateData['linkable_type'] = $data['link_type'] === 'category' ? Category::class : ExtraCategory::class;
            $updateData['linkable_id'] = $data['link_id'];
        }
        
        $slider->update($updateData);
    }
    
    private function uploadImage($image, $width, $height)
    {
        $imageName = Str::uuid() . '.' . 'webp';
        $directory = 'uploads/hero-sliders';
        $destinationPath = public_path($directory);
        if (!File::isDirectory($destinationPath)) { File::makeDirectory($destinationPath, 0777, true, true); }
        Image::read($image->getRealPath())->resize($width, $height)->save($destinationPath . '/' . $imageName);
        return $directory . '/' . $imageName;
    }

    private function deleteImage($path)
    {
        if ($path && File::exists(public_path($path))) { File::delete(public_path($path)); }
    }
}