@extends('admin.master.master')
@section('title', 'Offer Banner List')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Offer Banner List</h2>
            <a href="{{ route('offer-banner.create') }}" class="btn text-white" style="background-color: var(--primary-color); white-space: nowrap;"><i data-feather="plus" class="me-1" style="width:18px; height:18px;"></i> Add Offer Banner</a>
        </div>
        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Banner Type</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
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
<script>
$(document).ready(function() {
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.offer-banner.data') }}",
        destroy: id => `{{ url('offer-banner') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };

    const loaderRow = `<tr class="loader-row"><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`;

    function fetchData() {
        $('#tableBody').html(loaderRow);
        $.get(routes.fetch, { page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection }, function (res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = '<tr><td colspan="5" class="text-center">No offer banners found.</td></tr>';
            } else {
                res.data.forEach((item, i) => {
                    const statusBadge = item.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    const editUrl = `{{ url('offer-banner') }}/${item.id}/edit`;
                    const imageUrl = `{{ asset('') }}${item.image}`;

                    rows += `<tr>
                        <td>${(res.current_page - 1) * 10 + i + 1}</td>
                        <td>${item.banner_type}</td>
                        <td><img src="${imageUrl}" alt="${item.banner_type}" width="100"></td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="${editUrl}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>`;
                });
            }
            $('#tableBody').html(rows);
            // Pagination logic can be added here
        });
    }
    
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.destroy(id), method: 'DELETE', data: { _token: routes.csrf },
                    success: function() {
                        Swal.fire('Deleted!', 'The offer banner has been removed.', 'success');
                        fetchData();
                    }
                });
            }
        });
    });

    fetchData();
});
</script>
@endsection
