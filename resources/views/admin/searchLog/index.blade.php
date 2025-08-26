@extends('admin.master.master')

@section('title')

Search Log Management | {{ $ins_name }}

@endsection


@section('css')

@endsection


@section('body')
<div class="dashboard-body">

    <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
        <!-- Breadcrumb Start -->
<div class="breadcrumb mb-24">
<ul class="flex-align gap-4">
<li><a href="{{route('home')}}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
<li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
<li><span class="text-main-600 fw-normal text-15">Search Log Management</span></li>
</ul>
</div>
<!-- Breadcrumb End -->



        <!-- Breadcrumb Right Start -->
        <div class="flex-align gap-8 flex-wrap">
            
            <div class="flex-align text-gray-500 text-13 border border-gray-100 rounded-4 ps-20 focus-border-main-600 bg-white">
                
            </div>

            <div class="flex-align text-gray-500 text-13">
          
            </div>
        </div>
        <!-- Breadcrumb Right End -->

    </div>
   

    <div class="card overflow-hidden">
        <div class="card-body">
            @include('flash_message')
                <div class="d-flex justify-content-between mb-3">
        <h4></h4>
        <input type="text" id="searchInput" class="form-control w-25" placeholder="Search...">
    </div>


             <div class="table-responsive mt-5">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Sl</th>
                    <th class="sortable" data-column="name">Search Query</th>
                    <th class="sortable" data-column="is_active">User Name</th>
                    <th >Action</th>
                </tr>
            </thead>
           <tbody id="tableBody"></tbody>
        </table>
    </div>

   <nav>
       <ul class="pagination justify-content-center" id="pagination"></ul>
    </nav>
        </div>
        
    </div>

    
</div>

@endsection


@section('script')
<script>
  
  
    var routes = {
        fetch: "{{ route('ajax.searchLogtable.data') }}",
        edit: id => `{{ route('searchLog.edit', ':id') }}`.replace(':id', id),
        show: id => `{{ route('searchLog.show', ':id') }}`.replace(':id', id),
        update: id => `{{ route('searchLog.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('searchLog.destroy', ':id') }}`.replace(':id', id),
        csrf: "{{ csrf_token() }}"
    };

    var currentPage = 1;
    var searchTerm = '';
    var sortColumn = 'id';
    var sortDirection = 'desc';

    function fetchData() {
    $.get(routes.fetch, {
        page: currentPage,
        search: searchTerm,
        sort: sortColumn,
        direction: sortDirection,
        perPage: 10
    }, function (res) {
        let rows = '';
        res.data.forEach((item, index) => {
                const sl = (res.current_page - 1) * 10 + index + 1;
                rows += `
                    <tr>
                        <td>${sl}</td>
                        <td>${item.query}</td>
                        <td>${item.userName ?? 'N/A'}</td>
                        <td>
                          
                            ${res.can_delete ? `<button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}"><i class="fa fa-trash"></i></button>` : ''}
                        </td>
                    </tr>`;


               
            });
            $('#tableBody').html(rows);

            // Pagination
            var paginationHtml = '';
            if (res.last_page > 1) {
                paginationHtml += `
                    <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="1">First</a>
                    </li>
                    <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${res.current_page - 1}">Prev</a>
                    </li>`;

                const start = Math.max(1, res.current_page - 2);
                const end = Math.min(res.last_page, res.current_page + 2);

                for (var i = start; i <= end; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === res.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
                }

                paginationHtml += `
                    <li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${res.current_page + 1}">Next</a>
                    </li>
                    <li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${res.last_page}">Last</a>
                    </li>`;
            }
            $('#pagination').html(paginationHtml);
        });
    }

    $(document).on('keyup', '#searchInput', function () {
        searchTerm = $(this).val();
        currentPage = 1;
        fetchData();
    });

    $(document).on('click', '.sortable', function () {
        const col = $(this).data('column');
        sortDirection = (sortColumn === col && sortDirection === 'asc') ? 'desc' : 'asc';
        sortColumn = col;
        fetchData();
    });

    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && !$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
            currentPage = page;
            fetchData();
        }
    });
   

    $(document).on('click', '.btn-delete', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: 'Delete this user?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        preConfirm: () => {
            return $.ajax({
                url: routes.delete(id),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' }
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({ toast: true, icon: 'success', title: 'User deleted', showConfirmButton: false, timer: 3000 });

            // Re-fetch and adjust page if needed
            $.get(routes.fetch, {
                page: currentPage,
                search: searchTerm,
                sort: sortColumn,
                direction: sortDirection
            }, function (res) {
                if (res.data.length === 0 && currentPage > 1) {
                    currentPage--;
                }
                fetchData();
            });
        }
    });
});

 
    fetchData();
</script>


@endsection