@extends('admin.master.master')

@section('title')
Dashboard
@endsection

@section('css')
<style>
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
    .filter-btn.active {
        background-color: var(--primary-color);
        color: white;
    }
</style>
@endsection

@section('body')
 <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h2 class="mb-0">Dashboard</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('home', ['filter' => 'today']) }}" class="btn btn-sm btn-outline-secondary filter-btn @if($filter == 'today') active @endif">Today</a>
                    <a href="{{ route('home', ['filter' => 'this_month']) }}" class="btn btn-sm btn-outline-secondary filter-btn @if($filter == 'this_month') active @endif">This Month</a>
                    <a href="{{ route('home', ['filter' => 'this_year']) }}" class="btn btn-sm btn-outline-secondary filter-btn @if($filter == 'this_year') active @endif">This Year</a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="card-icon bg-primary me-3">
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
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="card-icon bg-success me-3">
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
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="card-icon bg-info me-3">
                                <i data-feather="package"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Products</h6>
                                <h4 class="mb-0">{{ $totalProducts }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="card-icon bg-warning me-3">
                                <i data-feather="user-plus"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">New Customers</h6>
                                <h4 class="mb-0">{{ $newCustomersCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header card-header-custom">
                            Sales Overview (Last 6 Months)
                        </div>
                        <div class="card-body">
                            <div id="sales_chart" style="width: 100%; height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header card-header-custom">
                            Sales by Category
                        </div>
                        <div class="card-body">
                            <div id="category_chart" style="width: 100%; height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="card">
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
    </main>
@endsection

@section('script')
<!-- Google Charts Loader -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        drawSalesChart();
        drawCategoryChart();
    }

    function drawSalesChart() {
        var data = google.visualization.arrayToDataTable(@json($salesChartData));

        var options = {
            'hAxis': {title: 'Month'},
            'vAxis': {title: 'Sales (৳)', minValue: 0},
            'legend': { position: 'none' },
            'colors': ['#2b7f75']
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('sales_chart'));
        chart.draw(data, options);
    }

    function drawCategoryChart() {
        var data = google.visualization.arrayToDataTable(@json($categoryChartData));

        var options = {
            is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('category_chart'));
        chart.draw(data, options);
    }

    $(window).resize(function(){
        drawCharts();
    });
</script>
@endsection
