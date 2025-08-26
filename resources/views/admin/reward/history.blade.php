@extends('admin.master.master')
@section('title', 'Customer Points History')

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
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Customer Points</h5>
                <div class="d-flex align-items-center">
                    <input type="text" id="search-input" class="form-control" placeholder="Search customer..." style="width: 250px;">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive position-relative" style="min-height: 400px;">
                    <div class="loading-spinner">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Current Points</th>
                                <th>Total Transactions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body">
                            {{-- Data will be loaded via AJAX --}}
                        </tbody>
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
@endsection

@section('script')
@include('admin.reward._partial._script')
@endsection
