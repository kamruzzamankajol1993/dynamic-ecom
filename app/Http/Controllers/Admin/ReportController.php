<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Expense; // Add this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Account;
use App\Models\OpeningBalance;
use App\Models\TransactionEntry;
use Mpdf\Mpdf;
class ReportController extends Controller
{

     // --- VIEWS ---
    public function generalLedgerIndex()
    {
        return view('admin.report.general_ledger');
    }

    public function balanceSheetIndex()
    {
        return view('admin.report.balance_sheet');
    }

    // --- AJAX DATA PROVIDERS ---

    public function getReportDependencies()
    {
        return response()->json([
            'accounts' => Account::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function generateGeneralLedger(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $accountId = $validated['account_id'];
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $openingBalance = $this->getAccountBalanceAsOf(Account::find($accountId), $startDate, true);

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
            'account' => Account::find($accountId),
        ]);
    }

    // --- NEW METHOD FOR PDF PRINTING ---
    public function printGeneralLedger(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $openingBalance = $this->getAccountBalanceAsOf($account, $startDate, true);

        $transactions = TransactionEntry::with('transaction')
            ->where('account_id', $account->id)
            ->whereHas('transaction', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->oldest('id')
            ->get();
        
        $data = [
            'account' => $account,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
        ];

        $html = view('admin.report.print.general_ledger_pdf', $data)->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']); // A4-Landscape
        $mpdf->WriteHTML($html);
        return $mpdf->Output('general-ledger.pdf', 'I'); // 'I' for inline view
    }
    
      public function generateBalanceSheet(Request $request)
    {
        $validated = $request->validate(['end_date' => 'required|date']);
        $data = $this->getBalanceSheetData($validated['end_date']);
        return response()->json($data);
    }
    
   

    
    // --- NEW METHOD FOR PDF PRINTING ---
    public function printBalanceSheet(Request $request)
    {
        $validated = $request->validate(['end_date' => 'required|date']);
        $endDate = $validated['end_date'];

        $data = $this->getBalanceSheetData($endDate);
        $data['endDate'] = $endDate; // Pass the date to the view

        $html = view('admin.report.print.balance_sheet_pdf', $data)->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);
        return $mpdf->Output('balance-sheet.pdf', 'I'); // 'I' for inline view
    }


    // --- HELPER FUNCTIONS ---

    // NEW private helper to get all data for the balance sheet
    private function getBalanceSheetData(string $endDate): array
    {
        $totalRevenue = $this->calculateAccountTypeTotal('Revenue', $endDate);
        $totalExpense = $this->calculateAccountTypeTotal('Expense', $endDate);
        $netProfit = $totalRevenue - $totalExpense;

        $assets = $this->getAccountBalancesByType('Asset', $endDate);
        $liabilities = $this->getAccountBalancesByType('Liability', $endDate);
        $equity = $this->getAccountBalancesByType('Equity', $endDate);
        
        $equity[] = ['name' => 'Current Period Profit/Loss', 'amount' => $netProfit];

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
        ];
    }

    // --- HELPER FUNCTIONS ---

    private function getAccountBalancesByType($type, $endDate)
    {
        $accounts = Account::where('type', $type)->where('is_active', true)->get();
        $balances = [];
        foreach ($accounts as $account) {
            $balance = $this->getAccountBalanceAsOf($account, $endDate);
            if ($balance != 0) {
                // For liabilities and equity, balance is typically shown as positive
                $balances[] = ['name' => $account->name, 'amount' => abs($balance)];
            }
        }
        return $balances;
    }

    private function calculateAccountTypeTotal($type, $endDate)
    {
        $accounts = Account::where('type', $type)->where('is_active', true)->get();
        $total = 0;
        foreach ($accounts as $account) {
            $total += $this->getAccountBalanceAsOf($account, $endDate);
        }
        return $total;
    }

    private function getAccountBalanceAsOf($account, $asOfDate, $isOpening = false)
    {
        $opening = OpeningBalance::where('account_id', $account->id)->first();
        $balance = $opening ? ($opening->type === 'debit' ? $opening->amount : -$opening->amount) : 0;
        
        $dateOperator = $isOpening ? '<' : '<=';

        $debits = TransactionEntry::where('account_id', $account->id)->where('type', 'debit')->whereHas('transaction', function ($q) use ($asOfDate, $dateOperator) { $q->where('date', $dateOperator, $asOfDate); })->sum('amount');
        $credits = TransactionEntry::where('account_id', $account->id)->where('type', 'credit')->whereHas('transaction', function ($q) use ($asOfDate, $dateOperator) { $q->where('date', $dateOperator, $asOfDate); })->sum('amount');

        if ($account->type === 'Asset' || $account->type === 'Expense') {
            $balance += ($debits - $credits);
        } else { // Liability, Equity, Revenue
            $balance += ($credits - $debits);
        }
        return $balance;
    }


    // --- VIEWS ---
    public function trialBalanceIndex()
    {
        return view('admin.report.trial_balance');
    }
    
   


    // --- AJAX DATA PROVIDERS ---

    public function generateTrialBalance(Request $request)
    {
        $validated = $request->validate([
            'end_date' => 'required|date',
        ]);
        
        $endDate = $validated['end_date'];
        $accounts = Account::where('is_active', true)->get();
        $trialBalance = [];

        foreach ($accounts as $account) {
            // Get the final balance of the account as of the end date
            $balance = $this->getAccountBalanceAsOf($account, $endDate);

            // Only include accounts with a non-zero balance
            if ($balance != 0) {
                $trialBalance[] = [
                    'account_name' => $account->name,
                    'account_code' => $account->code,
                    // Asset & Expense balances are debits. Liability, Equity, Revenue are credits.
                    'debit' => ($account->type === 'Asset' || $account->type === 'Expense') ? $balance : 0,
                    'credit' => ($account->type === 'Liability' || $account->type === 'Equity' || $account->type === 'Revenue') ? abs($balance) : 0,
                ];
            }
        }

        return response()->json($trialBalance);
    }
// --- NEW METHOD FOR PDF PRINTING ---
    public function printTrialBalance(Request $request)
    {
        $validated = $request->validate([
            'end_date' => 'required|date',
        ]);

        $endDate = $validated['end_date'];
        $accounts = Account::where('is_active', true)->get();
        $trialBalance = [];

        foreach ($accounts as $account) {
            $balance = $this->getAccountBalanceAsOf($account, $endDate);
            if ($balance != 0) {
                $trialBalance[] = [
                    'account_name' => $account->name,
                    'account_code' => $account->code,
                    'debit' => ($account->type === 'Asset' || $account->type === 'Expense') ? $balance : 0,
                    'credit' => ($account->type === 'Liability' || $account->type === 'Equity' || $account->type === 'Revenue') ? abs($balance) : 0,
                ];
            }
        }
        
        $data = [
            'endDate' => $endDate,
            'trialBalance' => $trialBalance,
        ];

        $html = view('admin.report.print.trial_balance_pdf', $data)->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);
        return $mpdf->Output('trial-balance.pdf', 'I'); // 'I' for inline view
    }

    // --- VIEWS ---
    public function profitAndLossIndex()
    {
        return view('admin.report.profit_and_loss');
    }
    
    


    // --- AJAX DATA PROVIDERS ---

    public function generateProfitAndLoss(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $totalRevenue = $this->calculateAccountTypeTotalForPeriod('Revenue', $startDate, $endDate);
        $totalExpense = $this->calculateAccountTypeTotalForPeriod('Expense', $startDate, $endDate);

        return response()->json([
            'revenues' => $this->getAccountBalancesForPeriod('Revenue', $startDate, $endDate),
            'total_revenue' => $totalRevenue,
            'expenses' => $this->getAccountBalancesForPeriod('Expense', $startDate, $endDate),
            'total_expense' => $totalExpense,
            'net_profit' => $totalRevenue - $totalExpense,
        ]);
    }


    // --- NEW METHOD FOR PDF PRINTING ---
    public function printProfitAndLoss(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'revenues' => $this->getAccountBalancesForPeriod('Revenue', $startDate, $endDate),
            'total_revenue' => $this->calculateAccountTypeTotalForPeriod('Revenue', $startDate, $endDate),
            'expenses' => $this->getAccountBalancesForPeriod('Expense', $startDate, $endDate),
            'total_expense' => $this->calculateAccountTypeTotalForPeriod('Expense', $startDate, $endDate),
        ];
        
        $data['net_profit'] = $data['total_revenue'] - $data['total_expense'];

        $html = view('admin.report.print.profit_and_loss_pdf', $data)->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);
        return $mpdf->Output('profit-and-loss.pdf', 'I'); // 'I' for inline view
    }
    
    
    
    // --- HELPER FUNCTIONS ---

    // New helper to get balances for a specific period
    private function getAccountBalancesForPeriod($type, $startDate, $endDate)
    {
        $accounts = Account::where('type', $type)->where('is_active', true)->get();
        $balances = [];
        foreach ($accounts as $account) {
            $balance = $this->getAccountBalanceForPeriod($account, $startDate, $endDate);
            if ($balance != 0) {
                $balances[] = ['name' => $account->name, 'amount' => abs($balance)];
            }
        }
        return $balances;
    }

    // New helper to calculate total for a period
    private function calculateAccountTypeTotalForPeriod($type, $startDate, $endDate)
    {
        $accounts = Account::where('type', $type)->where('is_active', true)->get();
        $total = 0;
        foreach ($accounts as $account) {
            $total += $this->getAccountBalanceForPeriod($account, $startDate, $endDate);
        }
        return abs($total);
    }

    // New helper to get the balance of a single account for a period
    private function getAccountBalanceForPeriod($account, $startDate, $endDate)
    {
        $debits = TransactionEntry::where('account_id', $account->id)
            ->where('type', 'debit')
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })->sum('amount');

        $credits = TransactionEntry::where('account_id', $account->id)
            ->where('type', 'credit')
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            })->sum('amount');

        if ($account->type === 'Expense') {
            return $debits - $credits;
        } else { // Revenue, Liability, Equity
            return $credits - $debits;
        }
    }
    /**
     * Display the sales report view.
     */
    public function salesReport()
    {
        return view('admin.report.sales_report');
    }

    /**
     * Fetch sales data for the report via AJAX.
     */
    // --- START: UPDATED salesReportData METHOD ---
    public function salesReportData(Request $request)
    {
        // Set date range based on the filter
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        switch ($request->filter) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }
        
        // Date range array for whereBetween
        $dateRange = [$startDate, $endDate];

        // 1. Fetch paginated data for the table
        $orders = Order::query()
            ->where('status', 'delivered')
            ->whereBetween('created_at', $dateRange)
            ->with('customer')
            ->latest()
            ->paginate(15);

        // 2. Fetch summary data for the cards
        $summary = DB::table('orders')
            ->where('status', 'delivered')
            ->whereBetween('created_at', $dateRange)
            ->select(
                DB::raw('COUNT(id) as total_orders'),
                // --- START: MODIFIED total_sales calculation ---
                DB::raw('SUM(subtotal - IFNULL(discount, 0)) as total_sales'), // Corrected Sales Calculation
                // --- END: MODIFIED total_sales calculation ---
                DB::raw('SUM(discount) as total_discount'),
                DB::raw('SUM(shipping_cost) as total_shipping')
            )->first();
        
        // 3. Calculate Total Production Cost
        $totalProductionCost = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', $dateRange)
            ->sum(DB::raw('IFNULL(products.purchase_price, 0) * order_details.quantity'));
            
        // 4. Calculate Total Expense
        $totalExpense = DB::table('expenses')
            ->whereBetween('expense_date', $dateRange)
            ->sum('amount');

        // 5. Add new calculations to summary object
        $summary->total_sales = $summary->total_sales ?? 0; // Ensure it's not null
        $summary->totalProductionCost = $totalProductionCost;
        $summary->totalGrossProfit = $summary->total_sales - $totalProductionCost; // Gross Profit now uses the corrected sales
        $summary->totalExpense = $totalExpense;
        $summary->totalNetIncome = $summary->totalGrossProfit - $totalExpense; // Net Income reflects corrected Gross Profit
        
        // 6. Fetch data for the chart
        $chartDataQuery = DB::table('orders')
            ->where('status', 'delivered')
            ->whereBetween('created_at', $dateRange)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
                // --- START: MODIFIED chart calculation ---
                DB::raw('SUM(subtotal - IFNULL(discount, 0)) as total') // Corrected Chart Calculation
                 // --- END: MODIFIED chart calculation ---
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartData = [['Date', 'Sales']];
        foreach ($chartDataQuery as $row) {
            $chartData[] = [Carbon::parse($row->date)->format('d M'), (float)$row->total];
        }

        return response()->json([
            'table_data' => $orders,
            'summary' => $summary,
            'chart_data' => $chartData
        ]);
    }
    // --- END: UPDATED salesReportData METHOD ---

    /**
     * Display the customer sales report view.
     */
    public function customerReport()
    {
        return view('admin.report.customer_report');
    }

    /**
     * Fetch customer sales data for the report via AJAX.
     */
    public function customerReportData(Request $request)
    {
        // Set date range based on the filter
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        switch ($request->filter) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }

        $query = Customer::join('orders', 'customers.id', '=', 'orders.customer_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'delivered')
            ->select(
                'customers.id',
                'customers.name',
                'customers.phone',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderBy('total_spent', 'desc');
        
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('customers.name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('customers.phone', 'LIKE', "%{$searchTerm}%");
            });
        }

        $customers = $query->paginate(15);

        return response()->json($customers);
    }

    /**
     * Display the category sales report view.
     */
    public function categoryReport()
    {
        return view('admin.report.category_report');
    }

    /**
     * Fetch category sales data for the report via AJAX.
     */
    /**
     * Fetch category sales data for the report via AJAX.
     */
    public function categoryReportData(Request $request)
    {
        // Set date range based on the filter
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        switch ($request->filter) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
        }

        $query = Category::join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'delivered')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_details.quantity) as total_products_sold'),
                // --- START: MODIFIED total_sales_value calculation ---
                DB::raw("SUM(
                    CASE 
                        WHEN orders.subtotal > 0 THEN order_details.subtotal - (order_details.subtotal / orders.subtotal) * IFNULL(orders.discount, 0)
                        ELSE order_details.subtotal 
                    END
                ) as total_sales_value")
                // --- END: MODIFIED total_sales_value calculation ---
            )
            ->groupBy('categories.name')
            ->orderBy('total_sales_value', 'desc');

        if ($request->filled('search')) {
            $query->where('categories.name', 'LIKE', "%{$request->search}%");
        }

        $categories = $query->paginate(15);

        return response()->json($categories);
    }

    /**
     * Display the income report view.
     */
    public function incomeReport()
    {
        return view('admin.report.income_report');
    }

    /**
     * Fetch income data for the report via AJAX.
     */
    public function incomeReportData(Request $request)
    {
        $filter = $request->input('filter', 'monthly');
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        $startDate = null;
        $endDate = null;
        $dateRange = null; // Initialize dateRange

        // --- Determine Date Range ---
        if ($filter === 'monthly') {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        } elseif ($filter === 'yearly') {
            $startDate = Carbon::create($year, 1, 1)->startOfYear();
            $endDate = Carbon::create($year, 12, 31)->endOfYear();
        } elseif ($filter === 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Default to monthly if filter is invalid
             $startDate = Carbon::now()->startOfMonth();
             $endDate = Carbon::now()->endOfMonth();
        }
        $dateRange = [$startDate, $endDate]; // Set the date range array

        // --- Base Queries ---
        // Base query for Sales and COGS (delivered orders)
        $orderQuery = Order::query()->where('status', 'delivered')->whereBetween('created_at', $dateRange);
        // Base query for Expenses
        $expenseQuery = Expense::query()->whereBetween('expense_date', $dateRange);

        // --- Calculate Summary Totals ---
        // 1. Calculate Total Sales
        $totalSales = (clone $orderQuery)->sum(DB::raw('subtotal - IFNULL(discount, 0)'));

        // 2. Calculate Total Cost of Goods Sold (COGS)
        $totalCOGS = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', $dateRange)
            ->sum(DB::raw('IFNULL(products.purchase_price, 0) * order_details.quantity'));

        // 3. Calculate Total Revenue (Gross Profit)
        $totalRevenueGrossProfit = $totalSales - $totalCOGS;

        // 4. Calculate Total Expense
        $totalExpense = (clone $expenseQuery)->sum('amount');

        // 5. Calculate Net Income
        $totalNetIncome = $totalRevenueGrossProfit - $totalExpense;

        // --- Calculate Data for Table (Grouped by Date) ---
        // Sales By Date
        $salesByDate = (clone $orderQuery)->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw('SUM(subtotal - IFNULL(discount, 0)) as total_sales')
            )->groupBy('date')->pluck('total_sales', 'date');

        // COGS By Date
        $cogsByDate = DB::table('order_details')
             ->join('orders', 'order_details.order_id', '=', 'orders.id')
             ->join('products', 'order_details.product_id', '=', 'products.id')
             ->where('orders.status', 'delivered')
             ->whereBetween('orders.created_at', $dateRange)
             ->select(
                 DB::raw("DATE(orders.created_at) as date"),
                 DB::raw('SUM(IFNULL(products.purchase_price, 0) * order_details.quantity) as total_cogs')
             )->groupBy('date')->pluck('total_cogs', 'date');

        // Expense By Date
        $expenseByDate = (clone $expenseQuery)->select(
                DB::raw("DATE(expense_date) as date"),
                DB::raw('SUM(amount) as total_expense')
            )->groupBy('date')->pluck('total_expense', 'date');

        // Combine dates from all sources
        $dates = $salesByDate->keys()
                ->merge($cogsByDate->keys())
                ->merge($expenseByDate->keys())
                ->unique()
                ->sort();

        // Build the table data array
        $tableData = [];
        foreach ($dates as $date) {
            $sales = $salesByDate->get($date, 0);
            $cogs = $cogsByDate->get($date, 0);
            $revenueGrossProfit = $sales - $cogs; // Daily Gross Profit
            $expense = $expenseByDate->get($date, 0);
            $netIncome = $revenueGrossProfit - $expense; // Daily Net Income

            $tableData[] = [
                'date' => Carbon::parse($date)->format('d M, Y'),
                'revenue' => $revenueGrossProfit, // Use Gross Profit as 'Revenue' for the table
                'expense' => $expense,
                'net_income' => $netIncome,
            ];
        }

        // Return JSON response
        return response()->json([
            'summary' => [
                'total_revenue' => $totalRevenueGrossProfit, // Send Gross Profit as 'total_revenue'
                'total_expense' => $totalExpense,
                'net_income' => $totalNetIncome,
                // Optionally send other values if needed by the JS
                'total_sales' => $totalSales,
                'total_cogs' => $totalCOGS,
            ],
            'table_data' => $tableData,
        ]);
    }

    /**
     * Display the profit & loss report view.
     */
    public function profitLossReport()
    {
        return view('admin.report.profit_loss_report');
    }

    /**
     * Fetch profit & loss data for the report via AJAX.
     */
    public function profitLossReportData(Request $request)
    {
        $queryYear = $request->input('year', Carbon::now()->year);

        // 1. Get Monthly Sales Data
        $salesData = Order::whereYear('created_at', $queryYear)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_amount) as selling_price'),
                DB::raw('SUM(shipping_cost) as delivery_charge'),
                DB::raw('SUM(IFNULL(discount, 0)) as total_discount') // <<< ADDED THIS
            )
            ->where('status', 'delivered')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // 2. Get Monthly Production Cost Data
        $productionCostData = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->whereYear('orders.created_at', $queryYear)
            ->where('orders.status', 'delivered')
            ->select(
                DB::raw("DATE_FORMAT(orders.created_at, '%Y-%m') as month"),
                DB::raw('SUM(order_details.quantity * products.purchase_price) as production_cost')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // 3. Get Monthly Expense Data
        $expenseData = Expense::whereYear('expense_date', $queryYear)
            ->select(
                DB::raw("DATE_FORMAT(expense_date, '%Y-%m') as month"),
                DB::raw('SUM(amount) as monthly_expense')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // 4. Combine all data
        $reportData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = $queryYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthName = Carbon::createFromDate($queryYear, $i, 1)->format('F Y');

            $sales = $salesData->get($monthKey);
            $production = $productionCostData->get($monthKey);
            $expense = $expenseData->get($monthKey);

            $sellingPrice = $sales->selling_price ?? 0;
            $productionCost = $production->production_cost ?? 0;
            $deliveryCharge = $sales->delivery_charge ?? 0;
            $monthlyExpense = $expense->monthly_expense ?? 0;
            $totalDiscount = $sales->total_discount ?? 0; // <<< ADDED THIS
            
            $incomeFromSales = $sellingPrice - $productionCost - $deliveryCharge;
            $netProfit = $incomeFromSales - $monthlyExpense;

            // Add to report only if there is some activity
            if ($sellingPrice > 0 || $productionCost > 0 || $monthlyExpense > 0 || $totalDiscount > 0) {
                 $reportData[] = [
                    'month' => $monthName,
                    'orders' => $sales->total_orders ?? 0,
                    'selling_price' => $sellingPrice,
                    'production_cost' => $productionCost,
                    'delivery_charge' => $deliveryCharge,
                    'total_discount' => $totalDiscount, // <<< ADDED THIS
                    'income_from_sales' => $incomeFromSales,
                    'monthly_expense' => $monthlyExpense,
                    'net_profit' => $netProfit,
                ];
            }
        }
        
        // Sort by month descending for display
        $reportData = array_reverse($reportData);

        return response()->json(['data' => $reportData]);
    }
}
