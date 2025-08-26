@extends('admin.master.master')
@section('title', 'Coupons')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
        font-weight: 600;
    }
    .table thead th {
        background-color: #f8f9fa;
        border-bottom-width: 1px;
    }
    .loading-spinner { 
        display: none; 
        position: absolute; 
        top: 50%; 
        left: 50%; 
        transform: translate(-50%, -50%); 
    }
    .pagination .page-item .page-link {
        cursor: pointer;
        border-radius: 0.25rem;
        margin: 0 2px;
        border: none;
        color: #6c757d;
    }
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color, #2b7f75);
        color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h5 class="mb-0">Coupon Management</h5>
                <div class="d-flex align-items-center">
                    <input type="text" id="search-input" class="form-control me-2" placeholder="Search by code..." style="width: 200px;">
                    <button type="button" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fa fa-plus me-1"></i> Add New
                    </button>
                </div>
            </div>
        <div class="card">
            
            <div class="card-body">
                <div class="table-responsive position-relative" style="min-height: 400px;">
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Usage</th>
                                <th>Expires At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="coupon-table-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="text-muted" id="pagination-info"></div>
                <nav>
                    <ul class="pagination mb-0" id="pagination-container"></ul>
                </nav>
            </div>
        </div>
    </div>
</main>

@include('admin.coupon._partial._addModal')
@include('admin.coupon._partial._editModal')
@endsection

@section('script')
@include('admin.coupon._partial._script')
@endsection
