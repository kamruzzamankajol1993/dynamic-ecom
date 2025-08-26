@extends('admin.master.master')

@section('title')

Coupon Management | {{ $ins_name }}

@endsection


@section('css')
{{-- No custom CSS needed as we are using Bootstrap 5 classes only --}}
@endsection


@section('body')

<div class="dashboard-body"> {{-- Retaining the dashboard-body class from your provided structure --}}

    <div class="breadcrumb-with-buttons mb-4 d-flex justify-content-between flex-wrap gap-2">
        <!-- Breadcrumb Start -->
        <div class="breadcrumb mb-0">
            <ul class="d-flex align-items-center gap-2 ps-0 mb-0 list-unstyled">
                <li><a href="{{route('home')}}" class="text-secondary fw-normal text-decoration-none">Home</a></li>
                <li> <span class="text-muted fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                <li><span class="text-primary fw-normal">Coupon Management</span></li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb End -->
    <div class="card overflow-hidden shadow-lg rounded-3">
        <div class="card-header bg-primary text-white  rounded-top">
            <h4 class="mb-0 fs-5 fw-semibold">Add New Coupon</h4>
        </div>
        <div class="card-body ">
             <form action="{{ route('coupon.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Coupon Code</label>
                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="discount_type" class="form-label">Discount Type</label>
                        <select class="form-select" id="discount_type" name="discount_type" required>
                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="discount_value" class="form-label">Discount Value</label>
                        <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" value="{{ old('discount_value') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="expires_at" class="form-label">Expires At (Optional)</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="usage_limit" class="form-label">Total Usage Limit (Optional)</label>
                        <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" placeholder="e.g., 100">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="usage_limit_per_user" class="form-label">Usage Limit Per User (Optional)</label>
                        <input type="number" class="form-control" id="usage_limit_per_user" name="usage_limit_per_user" value="{{ old('usage_limit_per_user') }}" placeholder="e.g., 2">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Coupon</button>
                <a href="{{ route('coupon.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
            <!---addd form here end-->

        </div>
    </div>
</div>


@endsection


@section('script')

@endsection
