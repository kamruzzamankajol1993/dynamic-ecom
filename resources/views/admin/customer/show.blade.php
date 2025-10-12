@extends('admin.master.master')
@section('title', 'Customer Details')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Customer: {{ $customer->name }}</h2>
            <a href="{{ route('customer.index') }}" class="btn btn-secondary">Back to List</a>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="card-icon bg-primary text-white me-3">
                            <i data-feather="shopping-bag"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h4 class="mb-0">{{ $totalOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="card-icon bg-warning text-white me-3">
                            <i data-feather="loader"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Pending Orders</h6>
                            <h4 class="mb-0">{{ $pendingOrders }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="card-icon bg-success text-white me-3">
                            <i data-feather="dollar-sign"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Buy Amount</h6>
                            <h4 class="mb-0">৳{{ number_format($totalBuyAmount, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Last 12 Month Buy Chart</h5>
                    </div>
                    <div class="card-body">
                        <div id="buy_chart" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Orders</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->orders as $order)
                                    <tr>
                                        <td>#{{ $order->invoice_no }}</td>
                                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                                        <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                        <td><span class="badge bg-info-soft text-info">{{ ucfirst($order->status) }}</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No orders found for this customer.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Customer Information</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Name:</strong> {{ $customer->name }}</li>
                            <li class="list-group-item"><strong>Email:</strong> {{ $user->email ?? $customer->email ?? 'N/A' }}</li>
                            <li class="list-group-item"><strong>Phone:</strong> {{ $customer->phone }}</li>
                            <li class="list-group-item"><strong>Type:</strong> <span class="badge bg-info">{{ ucfirst($customer->type) }}</span></li>
                            <li class="list-group-item"><strong>Status:</strong>
                                @if($customer->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </li>
                            <li class="list-group-item"><strong>Member Since:</strong> {{ $customer->created_at->format('d M, Y') }}</li>
                        </ul>
                        <hr>
                        <h5 class="card-title mt-4">Addresses</h5>
                        @forelse($customer->addresses as $address)
                            <div class="mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <p class="mb-0">{{ $address->address }}</p>
                                @if($address->address_type)
                                    <small class="text-muted">Type: {{ $address->address_type }}</small>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted">{{ $customer->address }}</p>
                        @endforelse
                    </div>
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
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        // Use the dynamic data passed from the controller
        var data = google.visualization.arrayToDataTable(@json($chartData));

        var options = {
            legend: { position: 'none' },
            hAxis: { textStyle: { fontSize: 10 } },
            vAxis: { 
                gridlines: { color: '#f5f5f5' },
                minValue: 0 // Ensure the chart starts at 0
            },
            colors: ['#2b7f75'],
            chartArea: {'width': '90%', 'height': '80%'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('buy_chart'));
        chart.draw(data, options);
    }

    $(window).resize(function(){
        drawChart();
    });
</script>
@endsection
