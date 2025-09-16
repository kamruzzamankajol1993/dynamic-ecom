<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        return view('admin.banks.index');
    }

    public function data(Request $request)
    {
        $query = Bank::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('branch', 'like', '%' . $request->search . '%')
                  ->orWhere('account_number', 'like', '%' . $request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $banks = $query->paginate(10);

        return response()->json([
            'data' => $banks->items(),
            'total' => $banks->total(),
            'from' => $banks->firstItem(),
            'current_page' => $banks->currentPage(),
            'last_page' => $banks->lastPage(),
        ]);
    }

    public function create()
    {
        return view('admin.banks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255|unique:banks',
            'address' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        Bank::create($validated);

        return redirect()->route('banks.index')->with('success', 'Bank created successfully.');
    }

    public function show(Bank $bank)
    {
        return view('admin.banks.edit', compact('bank'));
    }

    public function edit(Bank $bank)
    {
        return view('admin.banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255|unique:banks,account_number,' . $bank->id,
            'address' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $bank->update($validated);

        return redirect()->route('banks.index')->with('success', 'Bank updated successfully.');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return response()->json(['message' => 'Bank deleted successfully.']);
    }
}