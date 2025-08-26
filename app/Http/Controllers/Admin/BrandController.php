<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class BrandController extends Controller
{
    // You can add permission middleware here if you use a package like Spatie/laravel-permission
    function __construct()
    {
         $this->middleware('permission:brandView|brandAdd|brandUpdate|brandDelete', ['only' => ['index','data']]);
         $this->middleware('permission:brandAdd', ['only' => ['store']]);
         $this->middleware('permission:brandUpdate', ['only' => ['update']]);
         $this->middleware('permission:brandDelete', ['only' => ['destroy']]);
    }

     public function index(): View
    {
        // CommonController::addToLog('brandView'); // Assuming you have this helper
        return view('admin.brand.index');
    }

    public function data(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%');
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $brands = $query->paginate(10);

        return response()->json([
            'data' => $brands->items(),
            'total' => $brands->total(),
            'current_page' => $brands->currentPage(),
            'last_page' => $brands->lastPage(),
        ]);
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json($brand);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/brands');

            // Ensure the directory exists
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            // Use Intervention Image to resize and save
            Image::read($image->getRealPath())->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath.'/'.$imageName);

            $path = 'uploads/brands/'.$imageName;
        }

        Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'logo' => $path,
        ]);

        // CommonController::addToLog('brandStore');
        return redirect()->back()->with('success', 'Brand created successfully!');
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:brands,name,' . $brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $brand->logo;
        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($brand->logo && File::exists(public_path('uploads/'.$brand->logo))) {
                File::delete(public_path('uploads/'.$brand->logo));
            }

            $image = $request->file('logo');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/brands');

            // Use Intervention Image to resize and save
            Image::read($image->getRealPath())->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath.'/'.$imageName);

            $path = 'uploads/brands/'.$imageName;
        }

        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'logo' => $path,
            'status' => $request->status,
        ]);

        // CommonController::addToLog('brandUpdate');
        return response()->json(['message' => 'Brand updated successfully']);
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        // Delete logo from uploads
        if ($brand->logo && File::exists(public_path('uploads/'.$brand->logo))) {
            File::delete(public_path('uploads/'.$brand->logo));
        }

        $brand->delete();
        // CommonController::addToLog('brandDelete');
        return response()->json(['message' => 'Brand deleted successfully']);
    }
}
