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
class ReportController extends Controller
{
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
    public function salesReportData(Request $request)
    {
        $query = Order::query();

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

        $query->whereBetween('created_at', [$startDate, $endDate]);

        // Fetch paginated data for the table
        $orders = $query->with('customer')->latest()->paginate(15);

        // Fetch summary data for the cards
        $summary = DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('SUM(discount) as total_discount'),
                DB::raw('SUM(shipping_cost) as total_shipping')
            )->first();
        
        // Fetch data for the chart
        $chartDataQuery = DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
                DB::raw('SUM(total_amount) as total')
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
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_details.quantity) as total_products_sold'),
                DB::raw('SUM(order_details.subtotal) as total_sales_value')
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
        
        $revenueQuery = Order::query();
        $expenseQuery = Expense::query();

        if ($filter === 'monthly') {
            $revenueQuery->whereYear('created_at', $year)->whereMonth('created_at', $month);
            $expenseQuery->whereYear('expense_date', $year)->whereMonth('expense_date', $month);
        } elseif ($filter === 'yearly') {
            $revenueQuery->whereYear('created_at', $year);
            $expenseQuery->whereYear('expense_date', $year);
        } elseif ($filter === 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $revenueQuery->whereBetween('created_at', [$startDate, $endDate]);
            $expenseQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }

        $totalRevenue = $revenueQuery->sum('total_amount');
        $totalExpense = $expenseQuery->sum('amount');
        $netIncome = $totalRevenue - $totalExpense;

        // Data for the table (grouped by date)
        $revenueByDate = $revenueQuery->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw('SUM(total_amount) as total')
            )->groupBy('date')->pluck('total', 'date');

        $expenseByDate = $expenseQuery->select(
                DB::raw("DATE(expense_date) as date"),
                DB::raw('SUM(amount) as total')
            )->groupBy('date')->pluck('total', 'date');

        $dates = $revenueByDate->keys()->merge($expenseByDate->keys())->unique()->sort();

        $tableData = [];
        foreach ($dates as $date) {
            $revenue = $revenueByDate->get($date, 0);
            $expense = $expenseByDate->get($date, 0);
            $tableData[] = [
                'date' => Carbon::parse($date)->format('d M, Y'),
                'revenue' => $revenue,
                'expense' => $expense,
                'net_income' => $revenue - $expense,
            ];
        }

        return response()->json([
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'net_income' => $netIncome,
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
                DB::raw('SUM(shipping_cost) as delivery_charge')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // 2. Get Monthly Production Cost Data
        $productionCostData = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->whereYear('orders.created_at', $queryYear)
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
            
            $incomeFromSales = $sellingPrice - $productionCost - $deliveryCharge;
            $netProfit = $incomeFromSales - $monthlyExpense;

            // Add to report only if there is some activity
            if ($sellingPrice > 0 || $productionCost > 0 || $monthlyExpense > 0) {
                 $reportData[] = [
                    'month' => $monthName,
                    'orders' => $sales->total_orders ?? 0,
                    'selling_price' => $sellingPrice,
                    'production_cost' => $productionCost,
                    'delivery_charge' => $deliveryCharge,
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
