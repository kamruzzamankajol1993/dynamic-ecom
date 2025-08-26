@extends('admin.master.master')
@section('title', 'Income Report')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .summary-card { text-align: center; padding: 1.5rem; border-radius: 0.5rem; color: #fff; }
    .summary-card h5 { font-size: 1rem; margin-bottom: 0.5rem; }
    .summary-card h2 { font-size: 2rem; font-weight: 700; margin: 0; }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Income Report</h5>
                <div class="d-flex align-items-center flex-wrap">
                    <select id="month-filter" class="form-select form-select-sm me-2 mb-2 mb-md-0" style="width: 150px;"></select>
                    <select id="year-filter" class="form-select form-select-sm me-2 mb-2 mb-md-0" style="width: 120px;"></select>
                    <input type="text" id="date-range-picker" class="form-control form-control-sm" style="width: 240px;">
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row g-4 mb-4" id="summary-cards">
                    {{-- Cards will be loaded via AJAX --}}
                </div>
                <!-- Data Table -->
                @include('admin.report._partial._income_table')
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
@include('admin.report._partial._income_script')
@endsection
