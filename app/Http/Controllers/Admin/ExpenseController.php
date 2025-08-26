<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display the main expense management view.
     */
    public function index()
    {
        // Fetch categories to populate the dropdowns in the modals
        $categories = ExpenseCategory::where('status', 1)->get();
        return view('admin.expense.index', compact('categories'));
    }

    /**
     * Provide data for the AJAX-powered table.
     */
    public function data(Request $request)
    {
        $query = Expense::with('category');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('category', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            })->orWhere('description', 'LIKE', "%{$searchTerm}%");
        }

        $expenses = $query->latest()->paginate(10);
        return response()->json($expenses);
    }

    /**
     * Store a new expense record.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $expense = Expense::create($request->all());
        return response()->json(['success' => 'Expense created successfully.', 'expense' => $expense]);
    }

    /**
     * Fetch a single expense for editing.
     */
    public function edit(Expense $expense)
    {
        return response()->json($expense);
    }

    /**
     * Update an existing expense record.
     */
    public function update(Request $request, Expense $expense)
    {
        $validator = Validator::make($request->all(), [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $expense->update($request->all());
        return response()->json(['success' => 'Expense updated successfully.', 'expense' => $expense]);
    }

    /**
     * Delete an expense record.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(['success' => 'Expense deleted successfully.']);
    }
}
