<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\OpeningBalance;
use App\Models\TransactionEntry;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
class BankBookController extends Controller
{
    /**
     * Display the bank book report page.
     */
    public function index()
    {
        return view('admin.report.bank_book');
    }

    public function printReport(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // --- Re-using the same calculation logic from generateReport ---
        $openingBalanceRecord = OpeningBalance::where('account_id', $account->id)->first();
        $openingBalance = $openingBalanceRecord ? ($openingBalanceRecord->type === 'debit' ? $openingBalanceRecord->amount : -$openingBalanceRecord->amount) : 0;
        
        $debitsBefore = TransactionEntry::where('account_id', $account->id)->where('type', 'debit')->whereHas('transaction', function ($q) use ($startDate) { $q->where('date', '<', $startDate); })->sum('amount');
        $creditsBefore = TransactionEntry::where('account_id', $account->id)->where('type', 'credit')->whereHas('transaction', function ($q) use ($startDate) { $q->where('date', '<', $startDate); })->sum('amount');
        $openingBalance += ($debitsBefore - $creditsBefore);

        $transactions = TransactionEntry::with('transaction')
            ->where('account_id', $account->id)
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->oldest('id')
            ->get();
        // --- End of calculation logic ---
        
        $data = [
            'account' => $account,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
        ];

        $html = view('admin.report.print.bank_book_pdf', $data)->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']); // A4 Landscape
        $mpdf->WriteHTML($html);
        return $mpdf->Output('bank-book-report.pdf', 'I'); // 'I' for inline browser view
    }

    /**
     * Get the necessary data (bank accounts) for the report form.
     */
    public function getDependencies()
    {
        // This can be refined if you have a more specific way to identify bank accounts
        $bankAccounts = Account::where('type', 'Asset')
            ->where('is_active', true)
            ->where(function ($query) {
                // A simple way to find bank accounts is by name
                $query->where('name', 'like', '%Bank%')
                      ->orWhere('name', 'like', '%Card%');
            })
            ->get(['id', 'name', 'code']);

        return response()->json(['accounts' => $bankAccounts]);
    }

    /**
     * Generate the bank book report data.
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $accountId = $validated['account_id'];
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // 1. Calculate Opening Balance
        $openingBalanceRecord = OpeningBalance::where('account_id', $accountId)->first();
        $openingBalance = $openingBalanceRecord ? ($openingBalanceRecord->type === 'debit' ? $openingBalanceRecord->amount : -$openingBalanceRecord->amount) : 0;

        $debitsBefore = TransactionEntry::where('account_id', $accountId)
            ->where('type', 'debit')
            ->whereHas('transaction', function ($query) use ($startDate) {
                $query->where('date', '<', $startDate);
            })->sum('amount');

        $creditsBefore = TransactionEntry::where('account_id', $accountId)
            ->where('type', 'credit')
            ->whereHas('transaction', function ($query) use ($startDate) {
                $query->where('date', '<', $startDate);
            })->sum('amount');
        
        $openingBalance += ($debitsBefore - $creditsBefore);

        // 2. Fetch Transactions within the date range
        $transactions = TransactionEntry::with('transaction')
            ->where('account_id', $accountId)
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->oldest('id')
            ->get();

        return response()->json([
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
        ]);
    }
}