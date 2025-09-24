<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExtraCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExtraCategoryController extends Controller
{
    public function index()
    {
        return view('admin.extracategory.index');
    }

    public function data(Request $request)
    {
        $query = ExtraCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', $request->search . '%');
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
            'per_page' => $categories->perPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:extra_categories,name']);

        ExtraCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Extra Category created successfully!');
    }

    public function show($id)
    {
        return response()->json(ExtraCategory::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $category = ExtraCategory::findOrFail($id);
        $request->validate(['name' => 'required|string|unique:extra_categories,name,' . $category->id]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Extra Category updated successfully']);
    }

    public function destroy($id)
    {
        ExtraCategory::findOrFail($id)->delete();
        return redirect()->route('extracategory.index')->with('success', 'Extra Category deleted successfully!');
    }
}