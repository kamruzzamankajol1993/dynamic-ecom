<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize Datepickers
    $("#add_expense_date, #edit_expense_date").datepicker({
        dateFormat: 'yy-mm-dd'
    });

    const tableBody = $('#table-body');
    const paginationContainer = $('#pagination-container');
    const paginationInfo = $('#pagination-info');
    const spinner = $('.loading-spinner');
    const searchInput = $('#search-input');
    let searchTimeout;

    function fetchData(page = 1, searchQuery = '') {
        spinner.show();
        tableBody.empty();
        let url = `{{ route('expense.data') }}?page=${page}&search=${searchQuery}`;

        $.get(url, function(response) {
            spinner.hide();
            renderTable(response);
            renderPagination(response);
            paginationInfo.text(`Showing ${response.from || 0} to ${response.to || 0} of ${response.total} entries`);
        }).fail(() => spinner.hide());
    }

    function renderTable(response) {
        if (response.data.length === 0) {
            tableBody.html('<tr><td colspan="6" class="text-center">No expenses found.</td></tr>');
            return;
        }
        const startSl = (response.current_page - 1) * response.per_page;
        response.data.forEach((item, index) => {
            const row = `
                <tr>
                    <td>${startSl + index + 1}</td>
                    <td>${item.category ? item.category.name : 'N/A'}</td>
                    <td>${new Intl.NumberFormat().format(item.amount)}</td>
                    <td>${new Date(item.expense_date).toLocaleDateString('en-GB')}</td>
                    <td>${item.description || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-info edit-btn" data-id="${item.id}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${item.id}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;
            tableBody.append(row);
        });
    }

    function renderPagination(response) {
         paginationContainer.empty();
        if (response.last_page > 1) {
            for (let i = 1; i <= response.last_page; i++) {
                paginationContainer.append(`<li class="page-item ${i === response.current_page ? 'active' : ''}"><a class="page-link" data-page="${i}">${i}</a></li>`);
            }
        }
    }

    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        $.post('{{ route("expense.store") }}', $(this).serialize(), function(res) {
            $('#addModal').modal('hide');
            $('#addForm')[0].reset();
            Swal.fire('Success!', res.success, 'success');
            fetchData();
        }).fail(() => Swal.fire('Error!', 'Something went wrong.', 'error'));
    });

    tableBody.on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let url = `{{ route('expense.edit', ':id') }}`.replace(':id', id);
        $.get(url, function(data) {
            $('#edit_id').val(data.id);
            $('#edit_expense_category_id').val(data.expense_category_id);
            $('#edit_amount').val(data.amount);
            $('#edit_expense_date').val(data.expense_date);
            $('#edit_description').val(data.description);
            $('#editModal').modal('show');
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let url = `{{ route('expense.update', ':id') }}`.replace(':id', id);
        $.post(url, $(this).serialize(), function(res) {
            $('#editModal').modal('hide');
            Swal.fire('Success!', res.success, 'success');
            fetchData(paginationContainer.find('.active .page-link').data('page'), searchInput.val());
        }).fail(() => Swal.fire('Error!', 'Something went wrong.', 'error'));
    });

    tableBody.on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let url = `{{ route('expense.destroy', ':id') }}`.replace(':id', id);
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
                }).fail(() => Swal.fire('Error!', 'Something went wrong.', 'error'));
            }
        });
    });

    searchInput.on('keyup', () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => fetchData(1, searchInput.val()), 500); });
    paginationContainer.on('click', '.page-link', (e) => { e.preventDefault(); fetchData($(e.target).data('page'), searchInput.val()); });

    fetchData();
});
</script>
