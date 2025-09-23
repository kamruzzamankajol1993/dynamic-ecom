<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    public function index(): View
    {
        // Pass all categories to the view for the parent dropdown
        $categories = Category::orderBy('name')->get();
        return view('admin.category.index', compact('categories'));
    }

      public function data(Request $request)
    {
        $query = Category::with('parent');

        if ($request->filled('search')) {
            $query->where('name', 'like', $request->search . '%');
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $categories = $query->paginate(10); // You can change this number and it will still work

        return response()->json([
            'data' => $categories->items(),
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'per_page' => $categories->perPage(), // <-- Add this line
        ]);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id', // Validate parent_id
        ]);

         $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'cat_'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            Image::read($image->getRealPath())->resize(50, 50)->save($destinationPath.'/'.$imageName);
            $path = 'uploads/categories/'.$imageName;
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id, // Save parent_id
            'image' => $path,
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id', // Validate parent_id
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

         $path = $category->image;
        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $image = $request->file('image');
            $imageName = 'cat_'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            
            Image::read($image->getRealPath())->resize(50, 50)->save($destinationPath.'/'.$imageName);
            $path = 'uploads/categories/'.$imageName;
        }

        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id, // Update parent_id
            'status' => $request->status,
            'image' => $path,
        ]);

        return response()->json(['message' => 'Category updated successfully']);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }
        $category->delete();
        return redirect()->route('category.index')->with('success', 'Category deleted successfully!');
    }
}