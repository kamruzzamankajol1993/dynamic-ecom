<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
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

        switch ($filter) {
            case 'today':
                $totalSalesQuery->whereDate('created_at', $now->today());
                $newOrdersQuery->whereDate('created_at', $now->today());
                $newCustomersQuery->whereDate('created_at', $now->today());
                break;
            case 'this_year':
                $totalSalesQuery->whereYear('created_at', $now->year);
                $newOrdersQuery->whereYear('created_at', $now->year);
                $newCustomersQuery->whereYear('created_at', $now->year);
                break;
            case 'this_month':
            default:
                $totalSalesQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                $newOrdersQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                $newCustomersQuery->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                break;
        }

        $totalSales = $totalSalesQuery->sum('total_amount');
        $newOrdersCount = $newOrdersQuery->count();
        $newCustomersCount = $newCustomersQuery->count();
        $totalProducts = Product::count(); // This is always a total

        // --- Recent Orders Table ---
        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        // --- Sales Overview Chart Data ---
        $salesQuery = Order::select(
            DB::raw("DATE_FORMAT(created_at, '%b') as month"),
            DB::raw("SUM(total_amount) as total")
        )->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
         ->groupBy('month')->orderByRaw("MONTH(created_at)");

        $salesData = $salesQuery->get();
        $salesChartData = [['Month', 'Sales']];
        foreach ($salesData as $row) {
            $salesChartData[] = [$row->month, (int)$row->total];
        }

        // --- Sales by Category Chart Data ---
        $categorySales = OrderDetail::join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_details.subtotal) as total_sales')
            )
            ->groupBy('categories.name')->orderBy('total_sales', 'desc')->take(5)->get();
            
        $categoryChartData = [['Category', 'Sales']];
        foreach ($categorySales as $row) {
            $categoryChartData[] = [$row->category_name, (int)$row->total_sales];
        }

        return view('admin.dashboard.index', compact(
            'totalSales',
            'newOrdersCount',
            'totalProducts',
            'newCustomersCount',
            'recentOrders',
            'salesChartData',
            'categoryChartData',
            'filter' // Pass the active filter to the view
        ));
    }
}
