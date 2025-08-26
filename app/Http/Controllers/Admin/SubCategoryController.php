<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::where('status', 1)->latest()->get();
        return view('admin.subcategory.index', compact('categories'));
    }

    public function data(Request $request)
    {
        $query = Subcategory::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('category', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $subcategories = $query->paginate(10);

        return response()->json([
            'data' => $subcategories->items(),
            'total' => $subcategories->total(),
            'current_page' => $subcategories->currentPage(),
            'last_page' => $subcategories->lastPage(),
        ]);
    }

    public function show($id)
    {
        return response()->json(Subcategory::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        Subcategory::create($request->all());
        return redirect()->back()->with('success', 'Subcategory created successfully!');
    }

    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
            'status' => $request->status,
        ]);
        return response()->json(['message' => 'Subcategory updated successfully']);
    }

    public function destroy($id)
    {
        Subcategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Subcategory deleted successfully']);
    }
}
