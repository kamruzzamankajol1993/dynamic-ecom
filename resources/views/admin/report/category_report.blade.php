@extends('admin.master.master')
@section('title', 'Category Wise Sales Report')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .card { border: none; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .card-header { background-color: #fff; border-bottom: 1px solid #e9ecef; padding: 1rem 1.5rem; font-weight: 600; }
    .table thead th { background-color: #f8f9fa; border-bottom-width: 1px; }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Category Wise Sales Report</h5>
                <div class="d-flex align-items-center flex-wrap">
                    <div class="btn-group btn-group-sm me-2 mb-2 mb-md-0" role="group">
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="weekly">This Week</button>
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="monthly">This Month</button>
                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="yearly">This Year</button>
                    </div>
                    <input type="text" id="date-range-picker" class="form-control form-control-sm me-2 mb-2 mb-md-0" style="width: 240px;">
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Search category..." style="width: 200px;">
                </div>
            </div>
            <div class="card-body">
                @include('admin.report._partial._category_table')
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
@include('admin.report._partial._category_script')
@endsection
