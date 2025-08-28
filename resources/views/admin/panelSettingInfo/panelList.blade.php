@extends('admin.master.master')

@section('title')

Panel Management | {{ $ins_name }}

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
                        <h2 class="mb-0">Panel Setting</h2>
                        <div class="d-flex align-items-center">
                            <form class="d-flex me-2" role="search">
                                <input class="form-control" id="searchInput" type="search" placeholder="Search Setting..." aria-label="Search">
                            </form>
                          

                              @if (Auth::user()->can('panelSettingAdd'))
                               <a href="{{ route('systemInformation.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;">
                                <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New Information
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
                                       <th class="sortable" style="width:5%">Sl</th>
                 
                   <th style="width:15%" class="sortable" data-column="branch_name">Branch Name</th>
            <th style="width:10%" class="sortable" data-column="icon">Icon</th>
            <th  style="width:10%"class="sortable" data-column="logo">Logo</th>
            <th  style="width:10%"class="sortable" data-column="black_logo">Black Logo</th>
            <th style="width:10%" class="sortable" data-column="name">Name</th>
            <th style="width:10%" class="sortable" data-column="phone">Phone</th>
            <th style="width:10%" class="sortable" data-column="email">Email</th>
            <th style="width:10%" class="sortable" data-column="address">Address</th>
                    <th class="sortable" style="width:10%">Action</th>
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
@include('admin.panelSettingInfo._partial.script')
@endsection
