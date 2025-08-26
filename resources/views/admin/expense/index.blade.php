@extends('admin.master.master')
@section('title', 'Expenses')

@section('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .card { border: none; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .card-header { background-color: #fff; border-bottom: 1px solid #e9ecef; padding: 1rem 1.5rem; font-weight: 600; }
    .table thead th { background-color: #f8f9fa; border-bottom-width: 1px; }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="gap-3 mb-4 d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Expense List</h5>
                <div class="d-flex align-items-center">
                    <input type="text" id="search-input" class="form-control me-2" placeholder="Search..." style="width: 200px;">
                    <button type="button" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fa fa-plus me-1"></i> Add New
                    </button>
                </div>
            </div>
        <div class="card">
            
            <div class="card-body">
                @include('admin.expense._partial._table')
            </div>
        </div>
    </div>
</main>

@include('admin.expense._partial._addModal')
@include('admin.expense._partial._editModal')
@endsection

@section('script')
@include('admin.expense._partial._script')
@endsection
