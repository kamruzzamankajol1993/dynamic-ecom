@extends('admin.master.master')
@section('title', 'Expense Categories')

@section('css')
{{-- Add any specific CSS if needed --}}
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
         <div class="gap-3 mb-4 d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Expense Categories</h5>
                <div class="d-flex align-items-center">
                    <input type="text" id="search-input" class="form-control me-2" placeholder="Search..." style="width: 200px;">
                    <button type="button" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fa fa-plus me-1"></i> Add New
                    </button>
                </div>
            </div>
        <div class="card">
           
            <div class="card-body">
                @include('admin.expense_category._partial._table')
            </div>
        </div>
    </div>
</main>

@include('admin.expense_category._partial._addModal')
@include('admin.expense_category._partial._editModal')
@endsection

@section('script')
@include('admin.expense_category._partial._script')
@endsection
