@extends('admin.master.master')

@section('title')

Holiday Calender Management | {{ $ins_name }}

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
    /* Styles for grouped table layout */
    .aircraft-model-row {
        background-color: #e9ecef; /* Light gray background for model name row */
        font-weight: bold;
    }
    .aircraft-model-row td {
        padding: 12px 8px;
    }
    .holiday-detail-row td {
        padding-left: 30px; /* Indent holiday details */
    }
    /* Styles for action buttons within the table */
    .btn-custom-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        line-height: 1.5;
        border-radius: 0.2rem;
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

<div class="dashboard-body">

    <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
        
<div class="breadcrumb mb-24">
<ul class="flex-align gap-4">
<li><a href="{{route('home')}}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
<li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
<li><span class="text-main-600 fw-normal text-15">Holiday Calender Management</span></li>
</ul>
</div>
        
<div class="flex-align gap-8 flex-wrap">
            <div class="flex-align text-gray-500 text-13">

              
                @if (Auth::user()->can('holidayCalenderAdd'))
                            <a href="{{ route('holidayCalender.create') }}" type="button"  class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i> Add New Data
                            </a>
                            @endif
         
            </div>
        </div>
        
</div>
   

    <div class="card overflow-hidden">
        <div class="card-body">
            @include('flash_message')
             <div class="d-flex justify-content-between mb-3">
        <h4></h4>
        <input type="text" id="searchInput" class="form-control w-25" placeholder="Search...">
    </div>


             <div class="table-responsive mt-5">
        <table class="display responsive nowrap w-100 table-bordered "  style="width: 100% !important">
            <thead class="table-light">
                <tr>
                  
                    <th class="sortable" style="width:10%">SL</th>
    <th style="width:30%" class="sortable" data-column="aircraft_model_name">Aircraft Model <span class="sort-icon"></span></th>
    <th style="width:20%" class="sortable">Holiday Date</th>
    <th  style="width:20%"class="sortable" data-column="holiday_charge">Holiday Charge <span class="sort-icon"></span></th>
    
    <th style="width:20%" class="sortable">Actions</th>

    
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  
  
    var routes = {
        fetch: "{{ route('holidayCalender.data') }}",
        edit: id => `{{ route('holidayCalender.edit', ':id') }}`.replace(':id', id),
        deleteAircraftModelHolidays: id => `{{ route('holidayCalender.destroy', ':id') }}`.replace(':id', id), // Route for deleting all holidays for an aircraft model
        deleteSingle: id => `{{ route('holidayCalender.deleteSingle', ':id') }}`.replace(':id', id), // Route for single holiday deletion
        csrf: "{{ csrf_token() }}"
    };

    var currentPage = 1;
    var searchTerm = '';
    var sortColumn = 'aircraft_model_name';
    var sortDirection = 'asc';

    function fetchData() {
    $.get(routes.fetch, {
        page: currentPage,
        search: searchTerm,
        sort: sortColumn,
        direction: sortDirection,
        perPage: 10
    }, function (res) {
        let rows = '';
        let sl = (res.current_page - 1) * res.per_page + 1;

        if (res.data.length === 0) {
            rows += `<tr><td colspan="5" class="text-center">No holiday calendars found.</td></tr>`;
        } else {
            res.data.forEach((aircraftModel, index) => {
                // Aircraft Model Row
                rows += `
                    <tr class="aircraft-model-row">
                        <td>${sl++}</td>
                        <td>
                            ${aircraftModel.aircraft_model_name ?? ''}
                            ${res.can_delete ? `
                                <button class="btn btn-sm btn-danger btn-delete-aircraft-model btn-custom-sm ms-2" data-id="${aircraftModel.aircraft_model_id}" title="Delete all holidays for this model">
                                    <i class="fa fa-trash-alt"></i> All
                                </button>
                            ` : ''}
                        </td>
                        <td colspan="3"></td>
                    </tr>`;

                // Holidays for this aircraft model
                if (aircraftModel.holidays && aircraftModel.holidays.length > 0) {
                    aircraftModel.holidays.forEach((holiday, holidayIndex) => {
                        rows += `
                            <tr class="holiday-detail-row">
                                <td></td>
                                <td></td>
                                <td>${holiday.holiday_date}</td>
                                <td>${holiday.holiday_charge ?? ''}</td>
                                <td>
                                    ${res.can_edit ? `<a href="${routes.edit(aircraftModel.aircraft_model_id)}" class="btn btn-sm btn-primary btn-custom-sm"><i class="fa fa-edit"></i></a>` : ''}
                                    ${res.can_delete ? `<button class="btn btn-sm btn-danger btn-delete-single btn-custom-sm" data-id="${holiday.id}" title="Delete this specific holiday"><i class="fa fa-trash"></i></button>` : ''}
                                </td>
                            </tr>`;
                    });
                } else {
                    rows += `
                        <tr class="holiday-detail-row">
                            <td></td>
                            <td></td>
                            <td colspan="3" class="text-center text-muted">No holidays defined for this model.</td>
                        </tr>`;
                }
            });
        }
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
   
    // Event listener for SINGLE holiday delete button
    $(document).on('click', '.btn-delete-single', function () {
        const id = $(this).data('id'); // This ID is the individual holiday's ID
        Swal.fire({
            title: 'Delete this holiday entry?',
            text: 'This will delete only this specific holiday date and charge. You will not be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            preConfirm: () => {
                return $.ajax({
                    url: routes.deleteSingle(id), // Call the new single delete route
                    method: 'DELETE',
                    data: { _token: routes.csrf }
                });
            }
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({ toast: true, icon: 'success', title: 'Holiday entry deleted', showConfirmButton: false, timer: 3000 });
                fetchData(); // Re-fetch data after successful deletion
            }
        });
    });

    // NEW: Event listener for BULK holiday delete button (deletes all holidays for an aircraft model)
    $(document).on('click', '.btn-delete-aircraft-model', function () {
        const id = $(this).data('id'); // This ID is the aircraft_model_id
        Swal.fire({
            title: 'Delete ALL holidays for this aircraft model?',
            text: 'This will delete all associated holiday dates and charges for this aircraft model. You will not be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete all!',
            preConfirm: () => {
                return $.ajax({
                    url: routes.deleteAircraftModelHolidays(id), // Call the bulk delete route
                    method: 'DELETE',
                    data: { _token: routes.csrf }
                });
            }
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({ toast: true, icon: 'success', title: 'All holidays for aircraft model deleted', showConfirmButton: false, timer: 3000 });
                //fetchData(); // Re-fetch data after successful deletion

                location.reload();
            }
        });
    });


    fetchData();
</script>
@endsection
