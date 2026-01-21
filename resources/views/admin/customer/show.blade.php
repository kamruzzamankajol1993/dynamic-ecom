@extends('admin.master.master')
@section('title', 'Customer Details')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Customer: {{ $customer->name }}</h2>
            <a href="{{ route('customer.index') }}" class="btn btn-secondary">Back to List</a>
        </div>

        {{-- Top Summary Cards (Updated to fit 4 cards) --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
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
            <div class="col-md-3">
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
            <div class="col-md-3">
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
            {{-- NEW CARD: Reward Points --}}
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="card-icon bg-info text-white me-3">
                            <i data-feather="star"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Current Points</h6>
                            <h4 class="mb-0">{{ $currentPoints }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Left Column --}}
            <div class="col-md-7">
                {{-- Chart Section --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Last 12 Month Buy Chart</h5>
                    </div>
                    <div class="card-body">
                        <div id="buy_chart" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>

                {{-- Order List --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Orders</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
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
                                        <td colspan="4" class="text-center text-muted">No orders found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- NEW SECTION: Point History --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Reward Point History</h5>
                        <span class="badge bg-primary">Total: {{ $currentPoints }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th class="text-end">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->rewardPointLogs->sortByDesc('created_at') as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <small>{{ $log->meta ?? 'N/A' }}</small>
                                            @if($log->order_id)
                                                <br><a href="#" class="text-decoration-none small">Order #{{ optional($log->order)->invoice_no ?? $log->order_id }}</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->type == 'earned')
                                                <span class="badge bg-success">Earned</span>
                                            @elseif($log->type == 'redeemed')
                                                <span class="badge bg-warning text-dark">Redeemed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($log->type) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold {{ $log->type == 'earned' ? 'text-success' : 'text-danger' }}">
                                            {{ $log->type == 'earned' ? '+' : '-' }}{{ $log->points }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No point history found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column: Customer Info --}}
            <div class="col-md-5">
                <div class="card sticky-top" style="top: 20px; z-index: 1;">
                    <div class="card-body">
                        <h5 class="card-title mb-3 border-bottom pb-2">Customer Information</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Name:</span>
                                <span class="fw-bold">{{ $customer->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Type:</span>
                                {{-- Highlighted Customer Type --}}
                                <div>
                                    @if($customer->type == 'platinum')
                                        <span class="badge bg-primary"><i data-feather="award" style="width:12px;"></i> Platinum</span>
                                    @elseif($customer->type == 'silver')
                                        <span class="badge bg-secondary">Silver</span>
                                    @else
                                        <span class="badge bg-light text-dark border">Normal</span>
                                    @endif
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Discount:</span>
                                <span>{{ $customer->discount_in_percent ?? 0 }}%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Current Points:</span>
                                <span class="fw-bold text-warning">{{ $currentPoints }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Email:</span>
                                <span>{{ $user->email ?? $customer->email ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Phone:</span>
                                <span>{{ $customer->phone }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Status:</span>
                                @if($customer->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Member Since:</span>
                                <span>{{ $customer->created_at->format('d M, Y') }}</span>
                            </li>
                        </ul>
                        
                        <h6 class="card-title mt-4 mb-3 border-bottom pb-2">Addresses</h6>
                        @forelse($customer->addresses as $address)
                            <div class="mb-2 p-2 bg-light rounded border {{ $address->is_default ? 'border-primary' : '' }}">
                                <p class="mb-1 small">{{ $address->address }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ $address->address_type }}</small>
                                    @if($address->is_default)
                                        <span class="badge bg-primary" style="font-size: 0.6rem;">Default</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">No address found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
{{-- ফাইলের একদম নিচে এই অংশটি যোগ করুন --}}

@section('script')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        // কন্ট্রোলার থেকে আসা ডাটা এখানে লোড হচ্ছে
        var data = google.visualization.arrayToDataTable(@json($chartData));

        var options = {
            legend: { position: 'none' },
            hAxis: { 
                textStyle: { fontSize: 11 },
                slantedText: true, 
                slantedTextAngle: 45 
            },
            vAxis: { 
                gridlines: { color: '#f5f5f5' },
                minValue: 0,
                format: 'short' // নাম্বারের ফরম্যাট ঠিক রাখবে
            },
            colors: ['#2b7f75'], // চার্টের রং
            chartArea: {'width': '85%', 'height': '70%'},
            animation: {
                startup: true,
                duration: 1000,
                easing: 'out',
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('buy_chart'));
        chart.draw(data, options);
    }

    // উইন্ডো রিসাইজ করলে চার্ট রেসপন্সিভ থাকার জন্য
    window.addEventListener('resize', drawChart);
</script>
@endsection