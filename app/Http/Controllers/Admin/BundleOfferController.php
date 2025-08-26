<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BundleOffer;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
class BundleOfferController extends Controller
{
    public function index()
    {
        // The view will now fetch data via AJAX
        return view('admin.offerName.index');
    }

    
         public function data(Request $request)
    {
        $query = BundleOffer::query();

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%')
                  ->orWhere('title', 'like',$request->search . '%');
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $offers = $query->paginate(10);

        // The pagination object already contains all the necessary data.
        // No need to manually construct an array.
        return response()->json($offers);
    }
    

    public function create()
    {
        return view('admin.offerName.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'startdate' => 'nullable|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
        ]);

           $data = $request->except('_token', 'image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $directory = public_path('uploads/offers/');
            
            // Ensure the directory exists
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            // Resize and save the image using Intervention Image
            Image::read($image)->resize(660, 350, function ($constraint) {
                $constraint->aspectRatio();
            })->save($directory . $imageName);

            $data['image'] = 'uploads/offers/' . $imageName;
        }

        $data['slug'] = Str::slug($request->name);

        BundleOffer::create($data);

        return redirect()->route('bundle-offer.index')->with('success', 'offer created successfully.');
    }

    public function show(BundleOffer $bundleOffer)
    {
        $bundleOffer = BundleOffer::find($id);
        return view('admin.offerName.show', compact('bundleOffer'));
    }

    public function edit($id)
    {
        $bundleOffer = BundleOffer::find($id);
        return view('admin.offerName.edit', compact('bundleOffer'));
    }

    public function update(Request $request, BundleOffer $bundleOffer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'startdate' => 'nullable|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
        ]);

        $data = $request->except('_token', '_method', 'image');

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if (File::exists(public_path($bundleOffer->image))) {
                File::delete(public_path($bundleOffer->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $directory = public_path('uploads/offers/');

            // Ensure the directory exists
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            // Resize and save the new image using Intervention Image
            Image::read($image)->resize(660, 350, function ($constraint) {
                $constraint->aspectRatio();
            })->save($directory . $imageName);

            $data['image'] = 'uploads/offers/' . $imageName;
        }

        $data['slug'] = Str::slug($request->name);

        $bundleOffer->update($data);

        return redirect()->route('bundle-offer.index')->with('success', 'offer updated successfully.');
    }

    public function destroy(BundleOffer $bundleOffer)
    {
         // Delete the image file when the offer is deleted
        if (File::exists(public_path($bundleOffer->image))) {
            File::delete(public_path($bundleOffer->image));
        }
        $bundleOffer->delete();
        return response()->json(['message' => 'Offer deleted successfully.']);
    }

    // AJAX method for product search
  public function searchProducts(Request $request)
    {
        $term = $request->get('term');
        $excludeIds = $request->get('exclude', []);

        $products = Product::where(function ($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('product_code', 'LIKE', "%{$term}%");
            })
            ->whereNotIn('id', $excludeIds)
            ->limit(10)
            ->get(['id', 'name', 'base_price', 'product_code']);

        return response()->json($products);
    }
}
