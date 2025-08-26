<script>
    var modalOne = new bootstrap.Modal(document.getElementById('editUserModal'));
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.branchtable.data') }}",
        show: id => `{{ route('branch.show', ':id') }}`.replace(':id', id),
        update: id => `{{ route('branch.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('branch.destroy', ':id') }}`.replace(':id', id),
        csrf: "{{ csrf_token() }}"
    };

    function fetchData() {
        $.get(routes.fetch, {
            page: currentPage,
            search: searchTerm,
            sort: sortColumn,
            direction: sortDirection
        }, function (res) {
            let rows = '';
            res.data.forEach((user, i) => {
                rows += `<tr>
                    <td>${(res.current_page - 1) * 10 + i + 1}</td>
                    <td>${user.name}</td>
                    <td>
                        <button class="btn btn-sm btn-info btn-edit btn-custom-sm" data-id="${user.id}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-delete btn-custom-sm" data-id="${user.id}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;
            });
            $('#tableBody').html(rows);

            let paginationHtml = '';

if (res.last_page > 1) {
    paginationHtml += `
        <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="1">First</a>
        </li>
        <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${res.current_page - 1}">Prev</a>
        </li>`;

    // Show max 5 pages around current
    const startPage = Math.max(1, res.current_page - 2);
    const endPage = Math.min(res.last_page, res.current_page + 2);

    for (let i = startPage; i <= endPage; i++) {
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

    $('#searchInput').on('keyup', function () {
        searchTerm = $(this).val();
        currentPage = 1;
        fetchData();
    });

    $(document).on('click', '.sortable', function () {
        let col = $(this).data('column');
        sortDirection = sortColumn === col ? (sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
        sortColumn = col;
        fetchData();
    });

    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        currentPage = parseInt($(this).data('page'));
        fetchData();
    });

    //show method

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.get(routes.show(id), function (user) {
            $('#editUserId').val(user.id);
            $('#editName').val(user.name);
            modalOne.show();
        });
    });

    $('#editUserForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editUserId').val();
        const btn = $(this).find('button[type="submit"]');
        const data = {
            name: $('#editName').val(),
            _token: routes.csrf
        };
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        $.ajax({
            url: routes.update(id),
            method: 'PUT',
            data: data,
            success() {
                Swal.fire({ toast: true, icon: 'success', title: 'Branch updated', showConfirmButton: false, timer: 3000 });
                modalOne.hide();
                fetchData();
            },
            error(xhr) {
                $('#editUserForm .is-invalid').removeClass('is-invalid');
                $('#editUserForm .invalid-feedback').remove();
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        const input = $(`#editUserForm [name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    }
                }
            },
            complete() {
                btn.prop('disabled', false).text('Save Changes');
            }
        });
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

    $('#editUserModal').on('hidden.bs.modalOne', function () {
        $('#editUserForm')[0].reset();
        $('#editUserForm .is-invalid').removeClass('is-invalid');
        $('#editUserForm .invalid-feedback').remove();
    });

    fetchData();
</script>

<script>
    var exportInvoicesUrl = "{{ route('downloadBranchExcel') }}";
    var exportInvoicesUrlPdf = "{{ route('downloadBranchPdf') }}";

    document.getElementById('invoiceFilter').addEventListener('change', function() {
    var selected = this.value;
    if (!selected) return;


    if( selected == 'excel'){

    var url = `${exportInvoicesUrl}`;
    }else{
 var url = `${exportInvoicesUrlPdf}`;
    }

    window.location.href = url;
});
</script>