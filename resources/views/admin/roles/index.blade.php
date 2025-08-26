<!-- new code strat -->

@extends('admin.master.master')

@section('title')

Role Management | {{ $ins_name }}

@endsection


@section('css')

@endsection


@section('body')

<main class="main-content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <h2 class="mb-0">Role List</h2>
                        <div class="d-flex align-items-center">
                            <form class="d-flex me-2" role="search">
                                <input class="form-control" id="searchInput" type="search" placeholder="Search roles..." aria-label="Search">
                            </form>
                            @if (Auth::user()->can('roleAdd'))
                            <a href="{{ route('roles.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;">
                                <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New Role
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
                                       <th>Sl</th>
                    <th class="sortable" data-column="name">Role Name</th>
                    <th >Action</th>
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
@include('admin.roles._partial.script')
@endsection

<!-- new code end -->

