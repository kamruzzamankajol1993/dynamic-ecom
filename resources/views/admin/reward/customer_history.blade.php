@extends('admin.master.master')
@section('title', 'Reward Log for ' . $customer->name)

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">Reward Log: {{ $customer->name }}</h2>
                <p class="text-muted mb-0">Current Balance: <strong>{{ $customer->reward_points }} Points</strong></p>
            </div>
            <a href="{{ route('reward.history') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i> Back to History
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Points</th>
                                <th>Related Order</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d M, Y h:i A') }}</td>
                                <td>
                                    @if($log->type == 'earned')
                                        <span class="badge bg-success">Earned</span>
                                    @elseif($log->type == 'redeemed')
                                        <span class="badge bg-danger">Redeemed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($log->type) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->type == 'redeemed')
                                        <span class="text-danger">-{{ $log->points }}</span>
                                    @else
                                        <span class="text-success">+{{ $log->points }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->order)
                                        <a href="{{ route('order.show', $log->order_id) }}">#{{ $log->order->invoice_no }}</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $log->meta }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No transactions found for this customer.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
