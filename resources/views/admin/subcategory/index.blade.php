@extends('admin.master.master')

@section('title')
Subcategory Management
@endsection

@section('css')
<style>
    .custom-select-container { position: relative; }
    .custom-select-display {
        display: block; width: 100%; padding: 0.375rem 0.75rem; font-size: 1rem;
        font-weight: 400; line-height: 1.5; color: #212529; background-color: #fff;
        border: 1px solid #ced4da; border-radius: 0.25rem; cursor: pointer;
        position: relative;
    }
    .custom-select-display::after {
        content: ''; position: absolute; top: 50%; right: 15px;
        width: 0; height: 0; border-left: 5px solid transparent;
        border-right: 5px solid transparent; border-top: 5px solid #333;
        transform: translateY(-50%);
    }
    .custom-select-options {
        display: none; position: absolute; top: 100%; left: 0; right: 0;
        background: #fff; border: 1px solid #ced4da; border-top: 0;
        z-index: 1051; max-height: 200px; overflow-y: auto;
    }
    .custom-select-search-input { width: 100%; padding: 8px; border: none; border-bottom: 1px solid #ddd; }
    .custom-select-option { padding: 10px; cursor: pointer; }
    .custom-select-option:hover { background-color: #f0f0f0; }
    .custom-select-option.is-hidden { display: none; }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Subcategory List</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search..." aria-label="Search">
                </form>
                <a type="button" data-bs-toggle="modal" data-bs-target="#addModal" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;">
                    <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th class="sortable" data-column="name">Subcategory Name</th>
                                <th>Category</th>
                                <th class="sortable" data-column="status">Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div></div>
                <nav>
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</main>

@include('admin.subcategory._partial.addModal')
@include('admin.subcategory._partial.editModal')
@endsection

@section('script')
@include('admin.subcategory._partial.script')
@endsection
