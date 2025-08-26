<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimationCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class AnimationCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.animation-category.index');
    }

    public function data(Request $request)
    {
        $query = AnimationCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
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
        return response()->json(AnimationCategory::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:animation_categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'anim_cat_'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/animation_categories');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            Image::read($image->getRealPath())->resize(300, 300, function ($c) {
                $c->aspectRatio(); $c->upsize();
            })->save($destinationPath.'/'.$imageName);
            $path = 'uploads/animation_categories/'.$imageName;
        }

        AnimationCategory::create(['name' => $request->name, 'image' => $path]);
        return redirect()->back()->with('success', 'Animation Category created successfully!');
    }

    public function update(Request $request, $id)
    {
        $category = AnimationCategory::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:animation_categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $path = $category->image;
        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path('uploads/'.$category->image))) {
                File::delete(public_path('uploads/'.$category->image));
            }
            $image = $request->file('image');
            $imageName = 'anim_cat_'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/animation_categories');
            Image::read($image->getRealPath())->resize(300, 300, function ($c) {
                $c->aspectRatio(); $c->upsize();
            })->save($destinationPath.'/'.$imageName);
            $path = 'uploads/animation_categories/'.$imageName;
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $path,
            'status' => $request->status,
        ]);
        return response()->json(['message' => 'Animation Category updated successfully']);
    }

    public function destroy($id)
    {
        $category = AnimationCategory::findOrFail($id);
        if ($category->image && File::exists(public_path('uploads/'.$category->image))) {
            File::delete(public_path('uploads/'.$category->image));
        }
        $category->delete();
        return response()->json(['message' => 'Animation Category deleted successfully']);
    }
}
