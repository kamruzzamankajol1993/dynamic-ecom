<script>
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.extracategory.data') }}",
        show: id => `{{ url('extracategory') }}/${id}`,
        update: id => `{{ url('extracategory') }}/${id}`,
        delete: id => `{{ url('extracategory') }}/${id}`,
        csrf: "{{ csrf_token() }}"
    };

    function fetchData() {
        $.get(routes.fetch, {
            page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection
        }, function (res) {
            let rows = '';
            res.data.forEach((item, i) => {
                const statusBadge = item.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                rows += `<tr>
                    <td>${(res.current_page - 1) * res.per_page + i + 1}</td>
                    <td>${item.name}</td>
                    <td>${statusBadge}</td>
                    <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-info btn-edit" data-id="${item.id}"><i class="fa fa-edit"></i></button>
                         <form action="${routes.delete(item.id)}" method="POST" class="d-inline">
                            <input type="hidden" name="_token" value="${routes.csrf}"><input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>
                        </form>
                    </td>
                </tr>`;
            });
            $('#tableBody').html(rows);

            let paginationHtml = '';
            if (res.last_page > 1) {
                // ... pagination logic ...
                 paginationHtml += `<li class="page-item ${res.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page - 1}">Prev</a></li>`;
                const startPage = Math.max(1, res.current_page - 2);
                const endPage = Math.min(res.last_page, res.current_page + 2);
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `<li class="page-item ${i === res.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                paginationHtml += `<li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page + 1}">Next</a></li>`;
            }
            $('#pagination').html(paginationHtml);
        });
    }

    $('#searchInput').on('keyup', function () { searchTerm = $(this).val(); currentPage = 1; fetchData(); });
    $(document).on('click', '.sortable', function () {
        let col = $(this).data('column');
        sortDirection = sortColumn === col ? (sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
        sortColumn = col;
        fetchData();
    });
    $(document).on('click', '.page-link', function (e) { e.preventDefault(); currentPage = $(this).data('page'); fetchData(); });

    $(document).on('click', '.btn-edit', function () {
        $.get(routes.show($(this).data('id')), function (item) {
            $('#editId').val(item.id);
            $('#editName').val(item.name);
            $('#editStatus').val(item.status);
            editModal.show();
        });
    });

    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editId').val();
        const btn = $(this).find('button[type="submit"]');
        let formData = new FormData(this);
        formData.append('_method', 'PUT');
        formData.append('_token', routes.csrf);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        $.ajax({
            url: routes.update(id), method: 'POST', data: formData, processData: false, contentType: false,
            success() {
                Swal.fire({ toast: true, icon: 'success', title: 'Updated successfully', showConfirmButton: false, timer: 3000 });
                editModal.hide();
                fetchData();
            },
            complete() { btn.prop('disabled', false).text('Save Changes'); }
        });
    });

    $(document).on('click', '.btn-delete', function () {
        const deleteButton = $(this);
        Swal.fire({
            title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteButton.closest('form').submit();
            }
        });
    });

    $('#editModal').on('hidden.bs.modal', () => { $('#editForm')[0].reset(); });
    fetchData();
</script>