@extends('admin.master.master')
@section('title', 'Sales Report')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .summary-card {
        text-align: center;
        padding: 1.5rem;
        border-radius: 0.5rem;
        color: #fff;
    }
    .summary-card h5 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .summary-card h2 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Sales Report</h5>
                <div class="d-flex align-items-center flex-wrap">
                    <div class="btn-group btn-group-sm me-2 mb-2 mb-md-0" role="group">
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="weekly">This Week</button>
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="monthly">This Month</button>
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="yearly">This Year</button>
                    </div>
                    <input type="text" id="date-range-picker" class="form-control form-control-sm" style="width: 240px;">
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row g-4 mb-4" id="summary-cards">
                    {{-- Cards will be loaded via AJAX --}}
                </div>

                <!-- Chart -->
                <div class="mb-4">
                    <div id="sales_chart" style="width: 100%; height: 350px;"></div>
                </div>

                <!-- Data Table -->
                @include('admin.report._partial._sales_table')
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
@include('admin.report._partial._sales_script')
@endsection
