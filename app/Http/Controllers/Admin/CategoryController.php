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
        return view('admin.category.index');
    }

    public function data(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%');
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $categories = $query->paginate(10);

        return response()->json([
            'data' => $categories->items(),
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
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
        ]);

         $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'anim_cat_'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            Image::read($image->getRealPath())->resize(64, 64, function ($c) {
                $c->aspectRatio(); $c->upsize();
            })->save($destinationPath.'/'.$imageName);
            $path = 'uploads/categories/'.$imageName;
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $path,
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

         $path = $category->image;
        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path('uploads/'.$category->image))) {
                File::delete(public_path('uploads/'.$category->image));
            }
            $image = $request->file('image');
            $imageName = 'anim_cat_'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            
            Image::read($image->getRealPath())->resize(64, 64, function ($c) {
                $c->aspectRatio(); $c->upsize();
            })->save($destinationPath.'/'.$imageName);
            $path = 'uploads/categories/'.$imageName;
        }

        $category->update([
            'name' => $request->name,
            'status' => $request->status,
            'image' => $path,
        ]);

        return response()->json(['message' => 'Category updated successfully']);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
