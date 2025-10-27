@extends('admin.master.master')

@section('title')
Dashboard
@endsection

@section('css')
<style>
    /* --- Card Icon (Original) --- */
    .card-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        color: white;
    }
    .card-header-custom {
        background-color: var(--primary-color);
        color: white;
        border-bottom: none;
    }
    .product-list-item img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    
    /* --- START: NEW STYLES --- */

    /* Filter Buttons (like screenshot) */
    .filter-btn {
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #6c757d;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease-in-out;
    }
    .filter-btn:hover {
        background-color: #f8f9fa;
        text-decoration: none;
    }
    .filter-btn.active {
        background-color: #212529; /* Black background */
        color: #fff; /* White text */
        border-color: #212529;
    }
    .filter-btn:focus {
        box-shadow: none;
    }

    /* New Summary Card Style (like screenshot) */
    .summary-card .card-body {
        padding: 1.25rem;
    }
    .summary-card-icon {
        flex-shrink: 0;
        margin-right: 1rem;
    }
    .summary-card-icon i {
        width: 32px; 
        height: 32px;
        stroke-width: 2.5;
    }
    .summary-card h6 {
        font-size: 0.85rem;
        margin-bottom: 0.25rem !important;
    }
    .summary-card h4 {
        font-size: 1.35rem;
        font-weight: 600;
        margin-bottom: 0 !important;
    }
    /* --- END: NEW STYLES --- */
</style>
@endsection

@section('body')
 <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h2 class="mb-0">Dashboard</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('home', ['filter' => 'today']) }}" class="btn filter-btn @if($filter == 'today') active @endif">Today</a>
                    <a href="{{ route('home', ['filter' => 'this_month']) }}" class="btn filter-btn @if($filter == 'this_month') active @endif">This Month</a>
                    <a href="{{ route('home', ['filter' => 'this_year']) }}" class="btn filter-btn @if($filter == 'this_year') active @endif">This Year</a>
                </div>
            </div>

            <p class="text-muted small mb-4">
                <i data-feather="info" style="width: 14px; height: 14px; vertical-align: text-top; margin-right: 2px;"></i>
                Note: All financial calculations (Sales, Cost, Profit), charts, and "Top Selling" products are based on orders with a 'Delivered' status.
            </p>


            <div class="row g-4 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 summary-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="summary-card-icon text-primary">
                                <i data-feather="dollar-sign"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Sales</h6>
                                <h4 class="mb-0">৳{{ number_format($totalSales, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 summary-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="summary-card-icon text-danger">
                                <i data-feather="shopping-bag"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Cost</h6>
                                <h4 class="mb-0">৳{{ number_format($totalProductionCost, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 summary-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="summary-card-icon text-success">
                                <i data-feather="trending-up"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Gross Profit</h6>
                                <h4 class="mb-0">৳{{ number_format($totalGrossProfit, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 summary-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="summary-card-icon text-warning">
                                <i data-feather="minus-circle"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Expense</h6>
                                <h4 class="mb-0">৳{{ number_format($totalExpense, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>

            <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 summary-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="summary-card-icon text-success">
                                <i data-feather="bar-chart-2"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Net Income</h6>
                                <h4 class="mb-0">৳{{ number_format($totalNetIncome, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>    
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 summary-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="summary-card-icon text-info">
                                <i data-feather="shopping-cart"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">New Orders</h6>
                                <h4 class="mb-0">{{ $newOrdersCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 summary-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="summary-card-icon text-warning">
                                <i data-feather="user-plus"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">New Customers</h6>
                                <h4 class="mb-0">{{ $newCustomersCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0 summary-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="summary-card-icon text-secondary">
                                <i data-feather="archive"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Products</h6>
                                <h4 class="mb-0">{{ $totalProducts }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header card-header-custom">
                            Sales Overview (Last 6 Months)
                        </div>
                        <div class="card-body">
                            <div id="sales_chart" style="width: 100%; height: 350px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header card-header-custom">
                            Monthly Sales Comparison
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row">
                                <div class="col-md-12 text-center mb-3">
                                    @if($salesPercentageChange > 0)
                                        <h3 class="text-success mb-1">
                                            <i data-feather="trending-up" class="me-1"></i> Up {{ number_format(abs($salesPercentageChange), 2) }}%
                                        </h3>
                                        <p class="text-muted mb-0">Sales are increasing this month.</p>
                                    @elseif($salesPercentageChange < 0)
                                        <h3 class="text-danger mb-1">
                                            <i data-feather="trending-down" class="me-1"></i> Down {{ number_format(abs($salesPercentageChange), 2) }}%
                                        </h3>
                                        <p class="text-muted mb-0">Sales are decreasing this month.</p>
                                    @else
                                        <h3 class="text-info mb-1">
                                            <i data-feather="minus" class="me-1"></i> No Change
                                        </h3>
                                        <p class="text-muted mb-0">Sales are flat compared to last month.</p>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <div id="month_comparison_chart" style="width: 100%; height: 200px;"></div>
                                </div>
                                <div class="col-6 text-center mt-3">
                                    <h6 class="mb-1">This Month</h6>
                                    <h4 class="text-primary mb-0">৳{{ number_format($currentMonthSales, 2) }}</h4>
                                </div>
                                <div class="col-6 text-center mt-3">
                                    <h6 class="text-muted mb-1">Previous Month</h6>
                                    <h4 class="text-muted mb-0">৳{{ number_format($previousMonthSales, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Orders</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Order ID</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-3">{{ $order->customer->name ?? 'N/A' }}</div>
                                                </div>
                                            </td>
                                            <td>#{{ $order->invoice_no }}</td>
                                            <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                            <td><span class="badge rounded-pill bg-info-soft text-info">{{ ucfirst($order->status) }}</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No recent orders found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header card-header-custom">
                            Sales by Category
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div id="category_chart" style="width: 100%; height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Top Selling Products</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Product</th>
                                            <th>Sold</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topSellingProducts as $product)
                                        @php
                                            // --- FIX: Corrected concatenation from '...' to '.' ---
                                            $imageUrl = (is_array($product->thumbnail_image) && !empty($product->thumbnail_image[0]))
                                                        ? asset('public/uploads/' . $product->thumbnail_image[0])
                                                        : 'https://placehold.co/40x40';
                                        @endphp
                                        <tr class="product-list-item">
                                            <td style="width: 50px;">
                                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}">
                                            </td>
                                            <td>
                                                <div style="font-size: 0.85rem; font-weight: 500;">{{ $product->name }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $product->total_sold }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No sales data found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Most Viewed Products</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Product</th>
                                            <th>Views</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topViewedProducts as $product)
                                        @php
                                            // --- FIX: Corrected concatenation from '...' to '.' ---
                                            $imageUrl = (is_array($product->thumbnail_image) && !empty($product->thumbnail_image[0]))
                                                        ? asset('public/uploads/' . $product->thumbnail_image[0])
                                                        : 'https://placehold.co/40x40';
                                        @endphp
                                        <tr class="product-list-item">
                                            <td style="width: 50px;">
                                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}">
                                            </td>
                                            <td>
                                                <div style="font-size: 0.85rem; font-weight: 500;">{{ $product->name }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $product->view_count }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No view data found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </main>
@endsection

@section('script')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        drawSalesChart();
        drawCategoryChart();
        drawMonthComparisonChart();
    }

    function drawSalesChart() {
        var data = google.visualization.arrayToDataTable(@json($salesChartData));

        var options = {
            'hAxis': {title: 'Month'},
            'vAxis': {title: 'Sales (৳)', minValue: 0},
            'legend': { position: 'none' },
            'colors': ['#2b7f75'],
            'height': 350
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('sales_chart'));
        chart.draw(data, options);
    }

    function drawCategoryChart() {
        var data = google.visualization.arrayToDataTable(@json($categoryChartData));

        var options = {
            is3D: true,
            'height': 300
        };

        var chart = new google.visualization.PieChart(document.getElementById('category_chart'));
        chart.draw(data, options);
    }

    function drawMonthComparisonChart() {
        var data = google.visualization.arrayToDataTable(@json($monthComparisonChartData));

        var options = {
            'vAxis': {minValue: 0, gridlines: {count: 0}, textPosition: 'none'},
            'legend': { position: 'none' },
            'bars': 'vertical',
            'bar': {groupWidth: "40%"},
            'hAxis': {
                textStyle: {
                    fontSize: 14,
                    color: '#333'
                }
            },
            'height': 200,
            'chartArea': {'width': '80%', 'height': '80%'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('month_comparison_chart'));
        chart.draw(data, options);
    }


    $(window).resize(function(){
        drawCharts();
        feather.replace(); // Re-run feather icons on resize
    });
</script>
@endsection