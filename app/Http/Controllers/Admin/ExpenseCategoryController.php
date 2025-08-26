<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        return view('admin.expense_category.index');
    }

    public function data(Request $request)
    {
        $query = ExpenseCategory::query();
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }
        $categories = $query->latest()->paginate(10);
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|string|unique:expense_categories,name']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $category = ExpenseCategory::create($request->all());
        return response()->json(['success' => 'Category created successfully.', 'category' => $category]);
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return response()->json($expenseCategory);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|string|unique:expense_categories,name,' . $expenseCategory->id]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $expenseCategory->update($request->all());
        return response()->json(['success' => 'Category updated successfully.', 'category' => $expenseCategory]);
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();
        return response()->json(['success' => 'Category deleted successfully.']);
    }
}
