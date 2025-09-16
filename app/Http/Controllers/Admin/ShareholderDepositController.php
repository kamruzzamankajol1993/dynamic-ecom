<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShareholderDeposit;
use App\Models\Transaction;
use App\Models\TransactionEntry;
use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShareholderDepositController extends Controller
{
    public function index()
    {
        return view('admin.shareholder_deposits.index');
    }

    public function data(Request $request)
    {
        $query = ShareholderDeposit::with('shareholder:id,name')->latest();

        if ($request->filled('search')) {
            $query->whereHas('shareholder', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $deposits = $query->paginate(10);
        
        return response()->json([
            'data' => $deposits->items(),
            'total' => $deposits->total(),
            'from' => $deposits->firstItem(),
            'current_page' => $deposits->currentPage(),
            'last_page' => $deposits->lastPage(),
        ]);
    }

    public function create()
    {
        $shareholders = User::where('is_shareholder', true)->where('status', true)->get(['id', 'name']);
        $cashAccounts = Account::where('is_active', true)->where('type', 'Asset')->get(['id', 'name', 'code']);
        $equityAccounts = Account::where('is_active', true)->where('type', 'Equity')->get(['id', 'name', 'code']);
        return view('admin.shareholder_deposits.create', compact('shareholders', 'cashAccounts', 'equityAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shareholder_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'cash_account_id' => 'required|exists:accounts,id',
            'equity_account_id' => 'required|exists:accounts,id',
        ]);

        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'voucher_type' => 'journal',
                'voucher_no' => 'SD-' . time(),
                'date' => $validated['date'],
                'description' => 'Shareholder Deposit: ' . User::find($validated['shareholder_id'])->name,
                'created_by' => Auth::id(),
            ]);

            TransactionEntry::create(['transaction_id' => $transaction->id, 'account_id' => $validated['cash_account_id'], 'type' => 'debit', 'amount' => $validated['amount']]);
            TransactionEntry::create(['transaction_id' => $transaction->id, 'account_id' => $validated['equity_account_id'], 'type' => 'credit', 'amount' => $validated['amount']]);
            
            ShareholderDeposit::create(array_merge($validated, ['transaction_id' => $transaction->id]));
        });

        return redirect()->route('shareholder-deposits.index')->with('success', 'Deposit created successfully.');
    }

    public function edit(ShareholderDeposit $shareholder_deposit)
    {
        $shareholders = User::where('is_shareholder', true)->where('status', true)->get(['id', 'name']);
        $cashAccounts = Account::where('is_active', true)->where('type', 'Asset')->get(['id', 'name', 'code']);
        $equityAccounts = Account::where('is_active', true)->where('type', 'Equity')->get(['id', 'name', 'code']);
        return view('admin.shareholder_deposits.edit', ['deposit' => $shareholder_deposit, 'shareholders' => $shareholders, 'cashAccounts' => $cashAccounts, 'equityAccounts' => $equityAccounts]);
    }

    public function update(Request $request, ShareholderDeposit $shareholder_deposit)
    {
        $validated = $request->validate([
            'shareholder_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'cash_account_id' => 'required|exists:accounts,id',
            'equity_account_id' => 'required|exists:accounts,id',
        ]);

        DB::transaction(function () use ($validated, $shareholder_deposit) {
            if ($shareholder_deposit->transaction_id) {
                Transaction::find($shareholder_deposit->transaction_id)->delete();
            }

            $newTransaction = Transaction::create([
                'voucher_type' => 'journal',
                'voucher_no' => 'SD-U-' . time(),
                'date' => $validated['date'],
                'description' => 'Updated Shareholder Deposit: ' . User::find($validated['shareholder_id'])->name,
                'created_by' => Auth::id(),
            ]);

            TransactionEntry::create(['transaction_id' => $newTransaction->id, 'account_id' => $validated['cash_account_id'], 'type' => 'debit', 'amount' => $validated['amount']]);
            TransactionEntry::create(['transaction_id' => $newTransaction->id, 'account_id' => $validated['equity_account_id'], 'type' => 'credit', 'amount' => $validated['amount']]);
            
            $shareholder_deposit->update(array_merge($validated, ['transaction_id' => $newTransaction->id]));
        });

        return redirect()->route('shareholder-deposits.index')->with('success', 'Deposit updated successfully.');
    }

    public function destroy(ShareholderDeposit $shareholder_deposit)
    {
        // The transaction is deleted automatically by the cascade constraint
        $shareholder_deposit->delete();
        return response()->json(['message' => 'Deposit deleted successfully.']);
    }
}