@extends('admin.master.master')

@section('title')
Unit Management
@endsection
@section('css')
<style>
     /* --- Font & Layout Adjustments --- */
    .main-content {
        font-size: 0.9rem; /* Reduced base font size */
    }
    .main-content h2 { font-size: 1.6rem; }
    .main-content h5 { font-size: 1.1rem; }

    /* Forms & Buttons */
    .form-control, .form-select, .btn {
        font-size: 0.875rem; /* Consistent font size for form elements */
    }
    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.3rem;
    }
    /* Cards */
    .card-body, .card-header, .card-footer {
        padding: 1rem;
    }

    /* Tables */
    .table {
        font-size: 0.875rem;
    }
    .table th, .table td {
        padding: 0.6rem 0.5rem; /* Reduce padding for a tighter look */
        vertical-align: middle;
    }
    .pagination {
        font-size: 0.875rem;
    }
    </style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Unit List</h2>
            <div class="d-flex align-items-center">
                <form class="d-flex me-2" role="search">
                    <input class="form-control" id="searchInput" type="search" placeholder="Search..." aria-label="Search">
                </form>
                <a type="button" data-bs-toggle="modal" data-bs-target="#addModal" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;">
                    <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New Unit
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
                                <th class="sortable" data-column="name">Unit Name</th>
                                <th class="sortable" data-column="code">Code</th>
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

@include('admin.unit._partial.addModal')
@include('admin.unit._partial.editModal')
@endsection

@section('script')
@include('admin.unit._partial.script')
@endsection
