<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpeningBalance;
use App\Models\Account;
use Illuminate\Http\Request;

class OpeningBalanceController extends Controller
{
    public function index()
    {
        return view('admin.opening_balances.index');
    }

    public function data(Request $request)
    {
        $query = OpeningBalance::with('account:id,name,code')->latest();

        if ($request->filled('search')) {
            $query->whereHas('account', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $balances = $query->paginate(10);
        
        return response()->json([
            'data' => $balances->items(),
            'total' => $balances->total(),
            'from' => $balances->firstItem(),
            'current_page' => $balances->currentPage(),
            'last_page' => $balances->lastPage(),
        ]);
    }

    public function create()
    {
        // Get accounts that don't already have an opening balance
        $accountIdsWithBalance = OpeningBalance::pluck('account_id');
        $accounts = Account::where('is_active', true)
                            ->whereNotIn('id', $accountIdsWithBalance)
                            ->get(['id', 'name', 'code']);

        return view('admin.opening_balances.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id|unique:opening_balances,account_id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:debit,credit',
            'as_of_date' => 'required|date',
        ]);

        OpeningBalance::create($validated);

        return redirect()->route('opening-balances.index')->with('success', 'Opening Balance created successfully.');
    }

    public function edit(OpeningBalance $opening_balance)
    {
        // For editing, we show all accounts including the current one.
        $accountIdsWithBalance = OpeningBalance::where('account_id', '!=', $opening_balance->account_id)->pluck('account_id');
        $accounts = Account::where('is_active', true)
                            ->whereNotIn('id', $accountIdsWithBalance)
                            ->get(['id', 'name', 'code']);

        return view('admin.opening_balances.edit', ['balance' => $opening_balance, 'accounts' => $accounts]);
    }

    public function update(Request $request, OpeningBalance $opening_balance)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id|unique:opening_balances,account_id,' . $opening_balance->id,
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:debit,credit',
            'as_of_date' => 'required|date',
        ]);

        $opening_balance->update($validated);

        return redirect()->route('opening-balances.index')->with('success', 'Opening Balance updated successfully.');
    }

    public function destroy(OpeningBalance $opening_balance)
    {
        $opening_balance->delete();
        return response()->json(['message' => 'Opening Balance deleted successfully.']);
    }
}