@extends('admin.master.master')

@section('title')

Permission Management | {{ $ins_name }}

@endsection


@section('css')

@endsection


@section('body')


<main class="main-content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <h2 class="mb-0">Permission List</h2>
                        <div class="d-flex align-items-center">
                            <form class="d-flex me-2" role="search">
                                <input class="form-control" id="searchInput" type="search" placeholder="Search permissions..." aria-label="Search">
                            </form>
                            @if (Auth::user()->can('permissionAdd'))
                            <a data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;">
                                <i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add New Permission
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
                                           <th scope="col" >Sl</th>
                                                <th scope="col">Group Name</th>
                                                <th scope="col">Permission Name</th>
                                                <th scope="col" >Actions</th>
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


@include('admin.permission._partial.addModal')
@endsection


@section('script')
@include('admin.permission._partial.script')
<script type="text/javascript">
    var i = 0;
    $("#dynamic-ar").click(function () {
        ++i;
        $("#dynamicAddRemove").append('<tr><td><input type="text" name="name[]" id="name'+i+'" placeholder="Permission Name" class="form-control" /></td><td><button type="button" class="btn btn-danger btn-sm remove-input-field"><i class="fa fa-trash"></i></button></td></tr>'
            );
    });
    $(document).on('click', '.remove-input-field', function () {
        $(this).parents('tr').remove();
    });
</script>



@endsection