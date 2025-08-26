@extends('admin.master.master')
@section('title', 'Profit & Loss Report')

@section('css')
<style>
    .card { border: none; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .card-header { background-color: #fff; border-bottom: 1px solid #e9ecef; padding: 1rem 1.5rem; font-weight: 600; }
    .table thead th { background-color: #f8f9fa; border-bottom-width: 1px; white-space: nowrap; }
    .table td { white-space: nowrap; }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Profit & Loss Report</h5>
                <div class="d-flex align-items-center">
                    <select id="year-filter" class="form-select form-select-sm" style="width: 120px;"></select>
                </div>
            </div>
            <div class="card-body">
                @include('admin.report._partial._profit_loss_table')
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
@include('admin.report._partial._profit_loss_script')
@endsection
