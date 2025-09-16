<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        return view('admin.accounts.index');
    }

    public function data(Request $request)
    {
        $query = Account::with('parent:id,name')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $accounts = $query->paginate(10);
        
        return response()->json([
            'data' => $accounts->items(),
            'total' => $accounts->total(),
            'from' => $accounts->firstItem(),
            'current_page' => $accounts->currentPage(),
            'last_page' => $accounts->lastPage(),
        ]);
    }

    public function create()
    {
        $parentAccounts = Account::where('is_active', true)->get(['id', 'name']);
        return view('admin.accounts.create', compact('parentAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:accounts,code',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'parent_id' => 'nullable|exists:accounts,id',
            'is_active' => 'required|boolean',
        ]);

        Account::create($validated);

        return redirect()->route('admin.accounts.index')->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        return view('admin.accounts.edit', compact('account'));
    }

    public function edit(Account $account)
    {
        $parentAccounts = Account::where('is_active', true)->where('id', '!=', $account->id)->get(['id', 'name']);
        return view('admin.accounts.edit', compact('account', 'parentAccounts'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:accounts,code,' . $account->id,
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'parent_id' => 'nullable|exists:accounts,id',
            'is_active' => 'required|boolean',
        ]);

        $account->update($validated);

        return redirect()->route('admin.accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        // Add logic to prevent deletion if it has children accounts
        if ($account->children()->count() > 0) {
            return response()->json(['message' => 'Cannot delete account with child accounts.'], 422);
        }
        $account->delete();
        return response()->json(['message' => 'Account deleted successfully.']);
    }

    public function allAccounts()
    {
        return Account::where('is_active', true)->get(['id', 'name']);
    }
}