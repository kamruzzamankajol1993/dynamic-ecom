<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FooterBanner; // <<< MODIFIED: Use the new model
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class FooterBannerController extends Controller
{
    public function index()
    {
        // <<< MODIFIED: Fetch the single record from the new table >>>
        $banner = FooterBanner::first(); // The seeder ensures this record exists
        $bannerImage = $banner ? $banner->image : null;
        return view('admin.footer_banner.index', compact('bannerImage'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp',
        ]);

        // <<< MODIFIED: Find the single record to update >>>
        $banner = FooterBanner::firstOrFail(); // Or find(1)
        
        // Delete the old image if it exists
        if ($banner->image) {
            $this->deleteImage($banner->image);
        }

        // Upload the new image and update the record
        $path = $this->uploadImage($request->file('image'));
        $banner->update(['image' => $path]);

        return redirect()->back()->with('success', 'Footer banner updated successfully.');
    }

    private function uploadImage($image)
    {
        $imageName = 'footer-banner-' . Str::uuid() . '.' . 'webp';
        $directory = 'uploads/banners';
        $destinationPath = public_path($directory);

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        Image::read($image->getRealPath())->resize(1200, 200)->save($destinationPath . '/' . $imageName);
        
        return $directory . '/' . $imageName;
    }

    private function deleteImage($path)
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}