<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferBanner;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class OfferBannerController extends Controller
{
    public function index(): View
    {
        return view('admin.offer_banner.index');
    }

    public function data(Request $request)
    {
        $query = OfferBanner::query();

        if ($request->filled('search')) {
            $query->where('banner_type', 'like', $request->search . '%');
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $offerBanners = $query->paginate(10);

        return response()->json([
            'data' => $offerBanners->items(),
            'total' => $offerBanners->total(),
            'current_page' => $offerBanners->currentPage(),
            'last_page' => $offerBanners->lastPage(),
        ]);
    }

    public function create(): View
    {
        $bannerTypes = ['1st', '2nd', '3rd', '4th','5th','main'];
        $existingTypes = OfferBanner::pluck('banner_type')->toArray();
        $availableTypes = array_diff($bannerTypes, $existingTypes);

        return view('admin.offer_banner.create', compact('availableTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'banner_type' => 'required|in:1st,2nd,3rd,4th,5th,main|unique:offer_banners,banner_type',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'offer_banner_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/offer_banners');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            Image::read($image->getRealPath())->save($destinationPath . '/' . $imageName);
            $path = 'public/uploads/offer_banners/' . $imageName;
        }

        OfferBanner::create([
            'banner_type' => $request->banner_type,
            'image' => $path,
        ]);

        return redirect()->route('offer-banner.index')->with('success', 'Offer Banner created successfully!');
    }

    public function edit(OfferBanner $offerBanner): View
    {
        return view('admin.offer_banner.edit', compact('offerBanner'));
    }


    public function update(Request $request, OfferBanner $offerBanner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|boolean',
        ]);

        $path = $offerBanner->image;
        if ($request->hasFile('image')) {
            if ($offerBanner->image && File::exists(public_path($offerBanner->image))) {
                File::delete(public_path($offerBanner->image));
            }
            $image = $request->file('image');
            $imageName = 'offer_banner_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/offer_banners');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            
            Image::read($image->getRealPath())->save($destinationPath . '/' . $imageName);
            $path = 'public/uploads/offer_banners/' . $imageName;
        }

        $offerBanner->update([
            'status' => $request->status,
            'image' => $path,
        ]);

        return redirect()->route('offer-banner.index')->with('success', 'Offer Banner updated successfully!');
    }


    public function destroy(OfferBanner $offerBanner)
    {
        if ($offerBanner->image && File::exists(public_path($offerBanner->image))) {
            File::delete(public_path($offerBanner->image));
        }
        $offerBanner->delete();
        return response()->json(['message' => 'Offer Banner deleted successfully']);
    }
}
