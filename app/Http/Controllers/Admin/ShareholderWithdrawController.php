<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShareholderWithdraw;
use App\Models\Transaction;
use App\Models\TransactionEntry;
use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShareholderWithdrawController extends Controller
{
    public function index()
    {
        return view('admin.shareholder_withdraws.index');
    }

    public function data(Request $request)
    {
        $query = ShareholderWithdraw::with('shareholder:id,name')->latest();

        if ($request->filled('search')) {
            $query->whereHas('shareholder', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $withdrawals = $query->paginate(10);
        
        return response()->json([
            'data' => $withdrawals->items(),
            'total' => $withdrawals->total(),
            'from' => $withdrawals->firstItem(),
            'current_page' => $withdrawals->currentPage(),
            'last_page' => $withdrawals->lastPage(),
        ]);
    }

    public function create()
    {
        $shareholders = User::where('is_shareholder', true)->where('status', true)->get(['id', 'name']);
        $assetAccounts = Account::where('is_active', true)->where('type', 'Asset')->get(['id', 'name', 'code']);
        $equityAccounts = Account::where('is_active', true)->where('type', 'Equity')->get(['id', 'name', 'code']);
        return view('admin.shareholder_withdraws.create', compact('shareholders', 'assetAccounts', 'equityAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shareholder_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'debit_account_id' => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
        ]);

        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create([
                'voucher_type' => 'journal',
                'voucher_no' => 'SW-' . time(),
                'date' => $validated['date'],
                'description' => 'Shareholder Withdraw: ' . User::find($validated['shareholder_id'])->name,
                'created_by' => Auth::id(),
            ]);

            TransactionEntry::create(['transaction_id' => $transaction->id, 'account_id' => $validated['debit_account_id'], 'type' => 'debit', 'amount' => $validated['amount']]);
            TransactionEntry::create(['transaction_id' => $transaction->id, 'account_id' => $validated['credit_account_id'], 'type' => 'credit', 'amount' => $validated['amount']]);
            
            ShareholderWithdraw::create([
                'shareholder_id' => $validated['shareholder_id'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'note' => $validated['note'],
                'transaction_id' => $transaction->id,
            ]);
        });

        return redirect()->route('shareholder-withdraws.index')->with('success', 'Withdrawal created successfully.');
    }

    public function edit(ShareholderWithdraw $shareholder_withdraw)
    {
        $shareholders = User::where('is_shareholder', true)->where('status', true)->get(['id', 'name']);
        $assetAccounts = Account::where('is_active', true)->where('type', 'Asset')->get(['id', 'name', 'code']);
        $equityAccounts = Account::where('is_active', true)->where('type', 'Equity')->get(['id', 'name', 'code']);
        
        $transaction = $shareholder_withdraw->transaction()->with('entries')->first();
        $debitAccountId = $transaction->entries->where('type', 'debit')->first()->account_id ?? null;
        $creditAccountId = $transaction->entries->where('type', 'credit')->first()->account_id ?? null;

        return view('admin.shareholder_withdraws.edit', [
            'withdraw' => $shareholder_withdraw, 
            'shareholders' => $shareholders, 
            'assetAccounts' => $assetAccounts, 
            'equityAccounts' => $equityAccounts,
            'debitAccountId' => $debitAccountId,
            'creditAccountId' => $creditAccountId,
        ]);
    }

    public function update(Request $request, ShareholderWithdraw $shareholder_withdraw)
    {
        $validated = $request->validate([
            'shareholder_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'debit_account_id' => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
        ]);

        DB::transaction(function () use ($validated, $shareholder_withdraw) {
            if ($shareholder_withdraw->transaction_id) {
                Transaction::find($shareholder_withdraw->transaction_id)->delete();
            }

            $newTransaction = Transaction::create([
                'voucher_type' => 'journal',
                'voucher_no' => 'SW-U-' . time(),
                'date' => $validated['date'],
                'description' => 'Updated Shareholder Withdraw: ' . User::find($validated['shareholder_id'])->name,
                'created_by' => Auth::id(),
            ]);

            TransactionEntry::create(['transaction_id' => $newTransaction->id, 'account_id' => $validated['debit_account_id'], 'type' => 'debit', 'amount' => $validated['amount']]);
            TransactionEntry::create(['transaction_id' => $newTransaction->id, 'account_id' => $validated['credit_account_id'], 'type' => 'credit', 'amount' => $validated['amount']]);
            
            $shareholder_withdraw->update([
                'shareholder_id' => $validated['shareholder_id'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'note' => $validated['note'],
                'transaction_id' => $newTransaction->id,
            ]);
        });

        return redirect()->route('shareholder-withdraws.index')->with('success', 'Withdrawal updated successfully.');
    }

    public function destroy(ShareholderWithdraw $shareholder_withdraw)
    {
        $shareholder_withdraw->delete();
        return response()->json(['message' => 'Withdrawal deleted successfully.']);
    }
}