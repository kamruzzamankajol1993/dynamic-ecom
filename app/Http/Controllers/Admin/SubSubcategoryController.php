<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubSubcategory;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class SubSubcategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::where('status', 1)->latest()->get();
        return view('admin.sub-subcategory.index', compact('categories'));
    }

    // Method to fetch subcategories for the dependent dropdown
    public function getSubcategories($categoryId)
    {
        $subcategories = Subcategory::where('category_id', $categoryId)
                                    ->where('status', 1)
                                    ->get();
        return response()->json($subcategories);
    }

    public function data(Request $request)
    {
        $query = SubSubcategory::with('subcategory.category');

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%')
                  ->orWhereHas('subcategory', function ($q) use ($request) {
                      $q->where('name', 'like',$request->search . '%');
                  })
                  ->orWhereHas('subcategory.category', function ($q) use ($request) {
                      $q->where('name', 'like',$request->search . '%');
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
        // We need to load the parent category id for the edit modal
        $subSubcategory = SubSubcategory::with('subcategory')->findOrFail($id);
        $subSubcategory->category_id = $subSubcategory->subcategory->category_id;
        return response()->json($subSubcategory);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        SubSubcategory::create($request->all());
        return redirect()->back()->with('success', 'Sub-Subcategory created successfully!');
    }

    public function update(Request $request, $id)
    {
        $subSubcategory = SubSubcategory::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        $subSubcategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'subcategory_id' => $request->subcategory_id,
            'status' => $request->status,
        ]);
        return response()->json(['message' => 'Sub-Subcategory updated successfully']);
    }

    public function destroy($id)
    {
        SubSubcategory::findOrFail($id)->delete();
        return response()->json(['message' => 'Sub-Subcategory deleted successfully']);
    }
}
