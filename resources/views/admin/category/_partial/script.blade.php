<script>
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.category.data') }}",
        show: id => `{{ route('category.show', ':id') }}`.replace(':id', id),
        update: id => `{{ route('category.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('category.destroy', ':id') }}`.replace(':id', id),
        csrf: "{{ csrf_token() }}"
    };

    function fetchData() {
        $.get(routes.fetch, {
            page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection
        }, function (res) {
            let rows = '';
            res.data.forEach((item, i) => {
                const imageUrl = item.image ? `{{ asset('public') }}/${item.image}` : 'https://placehold.co/50x50/EFEFEF/AAAAAA&text=No+Image';
                const statusBadge = item.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';

                rows += `<tr>
                    <td>${(res.current_page - 1) * 10 + i + 1}</td>
                    <td><img src="${imageUrl}" alt="${item.name}" width="50" class="img-thumbnail"></td>
                    <td>${item.name}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-info btn-edit" data-id="${item.id}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;
            });
            $('#tableBody').html(rows);

            let paginationHtml = '';
            if (res.last_page > 1) {
                paginationHtml += `<li class="page-item ${res.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="1">First</a></li>`;
                paginationHtml += `<li class="page-item ${res.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page - 1}">Prev</a></li>`;
                const startPage = Math.max(1, res.current_page - 2);
                const endPage = Math.min(res.last_page, res.current_page + 2);
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `<li class="page-item ${i === res.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                paginationHtml += `<li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page + 1}">Next</a></li>`;
                paginationHtml += `<li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.last_page}">Last</a></li>`;
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
        const id = $(this).data('id');
        $.get(routes.show(id), function (item) {
            $('#editId').val(item.id);
            $('#editName').val(item.name);
            $('#editStatus').val(item.status);
            if (item.image) {
                $('#imagePreview').attr('src', `{{ asset('public') }}/${item.image}`).show();
            } else {
                $('#imagePreview').hide();
            }
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
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.delete(id), method: 'DELETE', data: { _token: routes.csrf },
                    success: () => {
                        Swal.fire({ toast: true, icon: 'success', title: 'Deleted successfully', showConfirmButton: false, timer: 3000 });
                        fetchData();
                    }
                });
            }
        });
    });

    $('#editModal').on('hidden.bs.modal', () => { $('#editForm')[0].reset(); $('#imagePreview').hide(); });
    fetchData();
</script>
