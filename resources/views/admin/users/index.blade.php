@extends('admin.master.master')

@section('title')

User Management | {{ $ins_name }}

@endsection


@section('css')
<style>

    .table-bordered {
    border: 1px solid #ccc;
    border-collapse: collapse;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #ccc;
    padding: 8px 12px;
    text-align: left;
}
    </style>
 <style>
        th.sortable {
            cursor: pointer;
        }
        th.sortable{
            background-color: #f8f9fa;
        }
    </style>
@endsection


@section('body')


<main class="main-content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <h2 class="mb-0">User List</h2>
                        <div class="d-flex align-items-center">
                            <form class="d-flex me-2" role="search">
                                <input class="form-control" id="searchInput" type="search" placeholder="Search users..." aria-label="Search">
                            </form>
                            @if (Auth::user()->can('userAdd'))
                            <a href="{{ route('users.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;">
                                <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New User
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            @include('flash_message')
                            <div class="table-responsive">
                                <table class="table table-hover  table-bordered">
                                    <thead>
                                        <tr>
                                     <th class="sortable" style="width:3%">SL</th>
    <th style="width:10%" class="sortable" data-column="branch_id">Branch <span class="sort-icon"></span></th>
    <th style="width:5%" class="sortable">Image</th>
    <th  style="width:10%"class="sortable" data-column="name">Name <span class="sort-icon"></span></th>
    <th style="width:10%" class="sortable" data-column="designation_id">Designation <span class="sort-icon"></span></th>
    <th style="width:10%" class="sortable" data-column="phone">Phone <span class="sort-icon"></span></th>
    <th style="width:10%" class="sortable" data-column="email">Email <span class="sort-icon"></span></th>
    <th style="width:10%" class="sortable" data-column="address">Address <span class="sort-icon"></span></th>
    <th style="width:10%" class="sortable">Roles</th>
    <th style="width:10%" class="sortable" data-column="status">Status <span class="sort-icon"></span></th>
    <th style="width:5%" class="sortable" data-column="status">Password <span class="sort-icon"></span></th>
    <th style="width:7%" class="sortable">Actions</th>
                                        </tr>
                                    </thead>

                                     <tbody id="tableBody"></tbody>

                                </table>
                            </div>
                        </div>
                         <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                <div class="text-muted"></div>
                                <nav>
       <ul class="pagination justify-content-center" id="pagination"></ul>
    </nav>
                            </div>
                    </div>
                </div>
</main>

@endsection

@section('script')
@include('admin.users._partial.script')
@endsection

