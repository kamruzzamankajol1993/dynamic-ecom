@extends('admin.master.master')
@section('title', 'Coupon Details')

@section('css')
<style>
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    .details-list {
        list-style: none;
        padding-left: 0;
    }
    .details-list li {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f1f1;
    }
    .details-list li:last-child {
        border-bottom: none;
    }
    .details-list .label {
        color: #6c757d;
        font-weight: 500;
    }
    .details-list .value {
        font-weight: 600;
    }
    .badge-list .badge {
        margin-right: 5px;
        margin-bottom: 5px;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Coupon Details</h2>
            <a href="{{ route('coupon.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Coupon: <span class="text-primary">{{ $coupon->code }}</span></h5>
                    </div>
                    <div class="card-body">
                        <ul class="details-list">
                            <li>
                                <span class="label">Discount Type</span>
                                <span class="value">{{ ucfirst($coupon->type) }}</span>
                            </li>
                            <li>
                                <span class="label">Value</span>
                                <span class="value">{{ $coupon->type == 'percent' ? $coupon->value . '%' : number_format($coupon->value, 2) . ' Taka' }}</span>
                            </li>
                            <li>
                                <span class="label">Minimum Purchase</span>
                                <span class="value">{{ $coupon->min_amount ? number_format($coupon->min_amount, 2) . ' Taka' : 'No minimum' }}</span>
                            </li>
                            <li>
                                <span class="label">Usage</span>
                                <span class="value">{{ $coupon->times_used }} / {{ $coupon->usage_limit ?? 'Unlimited' }} times</span>
                            </li>
                            <li>
                                <span class="label">Expires At</span>
                                <span class="value">{{ $coupon->expires_at ? $coupon->expires_at->format('d F, Y') : 'Never expires' }}</span>
                            </li>
                             <li>
                                <span class="label">User Type</span>
                                <span class="value">{{ ucfirst($coupon->user_type) }} Users</span>
                            </li>
                            <li>
                                <span class="label">Status</span>
                                <span class="value">
                                    @if($coupon->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Restrictions</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Applicable Products</h6>
                        <div class="badge-list">
                            @forelse($products as $productName)
                                <span class="badge bg-light text-dark">{{ $productName }}</span>
                            @empty
                                <p class="text-muted">This coupon applies to all products.</p>
                            @endforelse
                        </div>
                        <hr>
                        <h6 class="mb-3">Applicable Categories</h6>
                         <div class="badge-list">
                            @forelse($categories as $categoryName)
                                <span class="badge bg-light text-dark">{{ $categoryName }}</span>
                            @empty
                                <p class="text-muted">This coupon applies to all categories.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
