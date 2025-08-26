<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Reusable variables
    const tableBody = $('#table-body');
    const paginationContainer = $('#pagination-container');
    const paginationInfo = $('#pagination-info');
    const spinner = $('.loading-spinner');
    const searchInput = $('#search-input');
    let searchTimeout;

    // Fetch Data Function
    function fetchData(page = 1, searchQuery = '') {
        spinner.show();
        tableBody.empty();
        let url = `{{ route('expense-category.data') }}?page=${page}&search=${searchQuery}`;

        $.get(url, function(response) {
            spinner.hide();
            renderTable(response);
            renderPagination(response);
            paginationInfo.text(`Showing ${response.from || 0} to ${response.to || 0} of ${response.total} entries`);
        }).fail(function() {
            spinner.hide();
            tableBody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load data.</td></tr>');
        });
    }

    // Render Table Function
    function renderTable(response) {
        if (response.data.length === 0) {
            tableBody.html('<tr><td colspan="4" class="text-center">No categories found.</td></tr>');
            return;
        }
        const startSl = (response.current_page - 1) * response.per_page;
        response.data.forEach((item, index) => {
            const statusBadge = item.status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            const row = `
                <tr>
                    <td>${startSl + index + 1}</td>
                    <td>${item.name}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-info edit-btn" data-id="${item.id}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${item.id}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;
            tableBody.append(row);
        });
    }

    // Render Pagination Function
    function renderPagination(response) {
        paginationContainer.empty();
        if (response.last_page > 1) {
            for (let i = 1; i <= response.last_page; i++) {
                paginationContainer.append(`<li class="page-item ${i === response.current_page ? 'active' : ''}"><a class="page-link" data-page="${i}">${i}</a></li>`);
            }
        }
    }

    // Add Form Submission
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        $.post('{{ route("expense-category.store") }}', $(this).serialize(), function(res) {
            $('#addModal').modal('hide');
            $('#addForm')[0].reset();
            Swal.fire('Success!', res.success, 'success');
            fetchData();
        }).fail(err => Swal.fire('Error!', 'Something went wrong.', 'error'));
    });

    // Edit Button Click
    tableBody.on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let url = `{{ route('expense-category.edit', ':id') }}`.replace(':id', id);
        $.get(url, function(data) {
            $('#edit_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#edit_status').val(data.status ? 1 : 0);
            $('#editModal').modal('show');
        });
    });

    // Edit Form Submission
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let url = `{{ route('expense-category.update', ':id') }}`.replace(':id', id);
        $.post(url, $(this).serialize(), function(res) {
            $('#editModal').modal('hide');
            Swal.fire('Success!', res.success, 'success');
            fetchData(paginationContainer.find('.active .page-link').data('page'), searchInput.val());
        }).fail(err => Swal.fire('Error!', 'Something went wrong.', 'error'));
    });

    // Delete Button Click
    tableBody.on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let url = `{{ route('expense-category.destroy', ':id') }}`.replace(':id', id);
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, { _token: '{{ csrf_token() }}', _method: 'DELETE' }, function(res) {
                    Swal.fire('Deleted!', res.success, 'success');
                    fetchData(paginationContainer.find('.active .page-link').data('page'), searchInput.val());
                }).fail(err => Swal.fire('Error!', 'Something went wrong.', 'error'));
            }
        });
    });

    // Search and Pagination Listeners
    searchInput.on('keyup', () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => fetchData(1, searchInput.val()), 500); });
    paginationContainer.on('click', '.page-link', (e) => { e.preventDefault(); fetchData($(e.target).data('page'), searchInput.val()); });

    // Initial Fetch
    fetchData();
});
</script>
