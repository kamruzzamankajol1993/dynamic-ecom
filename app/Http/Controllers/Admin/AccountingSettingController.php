<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingDefault;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountingSettingController extends Controller
{
    public function index()
    {
        return view('admin.accounting_settings.index');
    }

    public function data(Request $request)
    {
        $query = AccountingDefault::with(['debitAccount:id,name', 'creditAccount:id,name'])->latest();

        if ($request->filled('search')) {
            $query->where('transaction_type', 'like', '%' . $request->search . '%');
        }

        $settings = $query->paginate(10);
        
        return response()->json([
            'data' => $settings->items(),
            'total' => $settings->total(),
            'from' => $settings->firstItem(),
            'current_page' => $settings->currentPage(),
            'last_page' => $settings->lastPage(),
        ]);
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->get(['id', 'name', 'code']);
        $existingTypes = AccountingDefault::pluck('transaction_type')->toArray();
        $transactionTypes = [
            'Sales - Paid(Cash)', 'Sales - Paid(Bank)', 'Sales - Due',
            'Purchase - Paid(Cash)', 'Purchase - Paid(Bank)', 'Purchase - Due',
            'Customer Payment', 'Supplier Payment', 'Sales Return', 'Purchase Return', 'Expense'
        ];
        // Filter out types that already have a setting
        $availableTypes = array_diff($transactionTypes, $existingTypes);

        return view('admin.accounting_settings.create', compact('accounts', 'availableTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|string|max:255|unique:accounting_defaults',
            'debit_account_id' => 'nullable|exists:accounts,id',
            'credit_account_id' => 'nullable|exists:accounts,id',
        ]);

        AccountingDefault::create($validated);

        return redirect()->route('accounting-settings.index')->with('success', 'Setting created successfully.');
    }

    public function edit(AccountingDefault $accounting_setting)
    {
        $accounts = Account::where('is_active', true)->get(['id', 'name', 'code']);
        return view('admin.accounting_settings.edit', ['setting' => $accounting_setting, 'accounts' => $accounts]);
    }

    public function update(Request $request, AccountingDefault $accounting_setting)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|string|max:255|unique:accounting_defaults,transaction_type,' . $accounting_setting->id,
            'debit_account_id' => 'nullable|exists:accounts,id',
            'credit_account_id' => 'nullable|exists:accounts,id',
        ]);

        $accounting_setting->update($validated);

        return redirect()->route('accounting-settings.index')->with('success', 'Setting updated successfully.');
    }

    public function destroy(AccountingDefault $accounting_setting)
    {
        $accounting_setting->delete();
        return response()->json(['message' => 'Setting deleted successfully.']);
    }
}