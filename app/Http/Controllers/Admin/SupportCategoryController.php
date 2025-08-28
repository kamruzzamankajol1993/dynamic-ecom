<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportPageCategory;
use Illuminate\Http\Request;

class SupportCategoryController extends Controller
{
    public function index()
    {
        return view('admin.support.categories.index');
    }

    public function data(Request $request)
    {
        $query = SupportPageCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', $request->search . '%');
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

    public function create()
    {
        return view('admin.support.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:support_ticket_categories',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        SupportPageCategory::create($request->all());

        return redirect()->route('support-categories.index')->with('success', 'Category created successfully.');
    }

    public function show(SupportPageCategory $support_category)
    {
        // Redirect to edit page as a detailed show page is not necessary for a simple category
        return redirect()->route('support-categories.edit', $support_category->id);
    }

    public function edit(SupportPageCategory $support_category)
    {
        return view('support.categories.edit', ['category' => $support_category]);
    }

    public function update(Request $request, SupportPageCategory $support_category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:support_ticket_categories,name,' . $support_category->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $support_category->update($request->all());

        return redirect()->route('support-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(SupportPageCategory $support_category)
    {
        $support_category->delete();
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}