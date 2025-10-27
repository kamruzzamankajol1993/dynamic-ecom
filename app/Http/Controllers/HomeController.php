<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Expense; // <-- IMPORTED
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

      /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     public function index(Request $request)
    {
        $filter = $request->get('filter', 'this_month'); // Default to 'this_month'
        $now = Carbon::now();

        // --- Summary Cards Data ---
        $totalSalesQuery = Order::where('status', 'delivered');
        $newOrdersQuery = Order::query();
        $newCustomersQuery = Customer::query();
        
        $totalProductionCostQuery = OrderDetail::join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.status', 'delivered');

        // --- START: NEW Expense Query ---
        $totalExpenseQuery = Expense::query();
        // --- END: NEW Expense Query ---

        switch ($filter) {
            case 'today':
                $totalSalesQuery->whereDate('orders.created_at', $now->today());
                $newOrdersQuery->whereDate('created_at', $now->today());
                $newCustomersQuery->whereDate('created_at', $now->today());
                $totalProductionCostQuery->whereDate('orders.created_at', $now->today());
                $totalExpenseQuery->whereDate('expense_date', $now->today()); // <-- ADDED
                break;
            case 'this_year':
                $totalSalesQuery->whereYear('orders.created_at', $now->year);
                $newOrdersQuery->whereYear('created_at', $now->year);
                $newCustomersQuery->whereYear('created_at', $now->year);
                $totalProductionCostQuery->whereYear('orders.created_at', $now->year);
                $totalExpenseQuery->whereYear('expense_date', $now->year); // <-- ADDED
                break;
            case 'this_month':
            default:
                $totalSalesQuery->whereMonth('orders.created_at', $now->month)->whereYear('orders.created_at', $now->year);
                $newOrdersQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                $newCustomersQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                $totalProductionCostQuery->whereMonth('orders.created_at', $now->month)->whereYear('orders.created_at', $now->year);
                $totalExpenseQuery->whereMonth('expense_date', $now->month)->whereYear('expense_date', $now->year); // <-- ADDED
                break;
        }

        // --- START: MODIFIED Calculations ---
        $totalSales = $totalSalesQuery->sum(DB::raw('subtotal - IFNULL(discount, 0)'));
        
        $totalProductionCost = $totalProductionCostQuery->sum(DB::raw('IFNULL(products.purchase_price, 0) * order_details.quantity'));
        
        $totalGrossProfit = $totalSales - $totalProductionCost; // <-- RENAMED
        
        $totalExpense = $totalExpenseQuery->sum('amount'); // <-- CALCULATED
        
        $totalNetIncome = $totalGrossProfit - $totalExpense; // <-- CALCULATED

        $newOrdersCount = $newOrdersQuery->count();
        $newCustomersCount = $newCustomersQuery->count();
        $totalProducts = Product::count();
        // --- END: MODIFIED Calculations ---


        // --- Recent Orders Table ---
        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        // --- Sales Overview Chart Data ---
        $salesQuery = Order::select(
            DB::raw("DATE_FORMAT(created_at, '%b') as month"),
            DB::raw("SUM(subtotal - IFNULL(discount, 0)) as total")
        )->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
        ->where('status', 'delivered')
         ->groupBy('month')->orderByRaw("MONTH(created_at) DESC");

        $salesData = $salesQuery->get();
        $salesChartData = [['Month', 'Sales']];
        foreach ($salesData as $row) {
            $salesChartData[] = [$row->month, (int)$row->total];
        }

        // --- Sales by Category Chart Data ---
        $categorySales = OrderDetail::join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'delivered')
            ->select(
                'categories.name as category_name',
                DB::raw("SUM(
                    CASE 
                        WHEN orders.subtotal > 0 THEN order_details.subtotal - (order_details.subtotal / orders.subtotal) * IFNULL(orders.discount, 0)
                        ELSE order_details.subtotal 
                    END
                ) as total_sales")
            )
            ->groupBy('categories.name')->orderBy('total_sales', 'desc')->take(5)->get();
            
        $categoryChartData = [['Category', 'Sales']];
        foreach ($categorySales as $row) {
            $categoryChartData[] = [$row->category_name, (int)$row->total_sales];
        }

        // --- Monthly Comparison Data ---
        $currentMonthSales = Order::where('status', 'delivered')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum(DB::raw('subtotal - IFNULL(discount, 0)'));

        $previousMonth = $now->copy()->subMonthNoOverflow();
        $previousMonthSales = Order::where('status', 'delivered')
            ->whereYear('created_at', $previousMonth->year)
            ->whereMonth('created_at', $previousMonth->month)
            ->sum(DB::raw('subtotal - IFNULL(discount, 0)'));

        $salesPercentageChange = 0;
        if ($previousMonthSales > 0) {
            $salesPercentageChange = (($currentMonthSales - $previousMonthSales) / $previousMonthSales) * 100;
        } elseif ($currentMonthSales > 0) {
            $salesPercentageChange = 100.0; // 100% increase if previous was 0
        }

        $monthComparisonChartData = [
            ['Month', 'Sales', ['role' => 'style']],
            ['This Month', (int)$currentMonthSales, 'color: #0d6efd'],
            ['Previous Month', (int)$previousMonthSales, 'color: #6c757d']
        ];
        
        // --- Top Selling Products Query ---
        $topSellingProducts = OrderDetail::join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->select(
                'products.id',
                'products.name',
                'products.thumbnail_image',
                DB::raw('SUM(order_details.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.thumbnail_image')
            ->orderBy('total_sold', 'DESC')
            ->take(6)
            ->get();

        // --- Top Viewed Products Query ---
        $topViewedProducts = Product::select('id', 'name', 'thumbnail_image', 'view_count')
            ->orderBy('view_count', 'DESC')
            ->take(6)
            ->get();


        return view('admin.dashboard.index', compact(
            'totalSales',
            'newOrdersCount',
            'totalProducts',
            'newCustomersCount',
            'recentOrders',
            'salesChartData',
            'categoryChartData',
            'filter',
            'monthComparisonChartData',
            'salesPercentageChange',
            'currentMonthSales',
            'previousMonthSales',
            'topSellingProducts',
            'topViewedProducts',
            'totalProductionCost',
            'totalGrossProfit',     // <-- RENAMED
            'totalExpense',         // <-- ADDED
            'totalNetIncome'        // <-- ADDED
        ));
    }
}