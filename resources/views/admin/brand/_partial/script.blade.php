<script>
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.brand.data') }}",
        show: id => `{{ route('brand.show', ':id') }}`.replace(':id', id),
        update: id => `{{ route('brand.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('brand.destroy', ':id') }}`.replace(':id', id),
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
            res.data.forEach((brand, i) => {
                const logoUrl = brand.logo ? `{{ asset('public') }}/${brand.logo}` : 'https://placehold.co/50x50/EFEFEF/AAAAAA&text=No+Image';
                const statusBadge = brand.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';

                rows += `<tr>
                    <td>${(res.current_page - 1) * 10 + i + 1}</td>
                    <td><img src="${logoUrl}" alt="${brand.name}" width="50" class="img-thumbnail"></td>
                    <td>${brand.name}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-info btn-edit btn-custom-sm" data-id="${brand.id}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-delete btn-custom-sm" data-id="${brand.id}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;
            });
            $('#tableBody').html(rows);

            // Pagination logic (copied from your script)
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

    // Show Edit Modal
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.get(routes.show(id), function (brand) {
            $('#editBrandId').val(brand.id);
            $('#editName').val(brand.name);
            $('#editStatus').val(brand.status);
            if (brand.logo) {
                $('#logoPreview').attr('src', `{{ asset('public') }}/${brand.logo}`).show();
            } else {
                $('#logoPreview').hide();
            }
            editModal.show();
        });
    });

    // Update Form Submission
    $('#editBrandForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editBrandId').val();
        const btn = $(this).find('button[type="submit"]');
        let formData = new FormData(this);
        formData.append('_method', 'PUT'); // Since HTML forms don't support PUT
        formData.append('_token', routes.csrf);

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: routes.update(id),
            method: 'POST', // Use POST to tunnel PUT
            data: formData,
            processData: false,
            contentType: false,
            success() {
                Swal.fire({ toast: true, icon: 'success', title: 'Brand updated successfully', showConfirmButton: false, timer: 3000 });
                editModal.hide();
                fetchData();
            },
            error(xhr) {
                // Handle validation errors
            },
            complete() {
                btn.prop('disabled', false).text('Save Changes');
            }
        });
    });

    // Delete Action
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.delete(id),
                    method: 'DELETE',
                    data: { _token: routes.csrf },
                    success: function() {
                        Swal.fire({ toast: true, icon: 'success', title: 'Brand deleted successfully', showConfirmButton: false, timer: 3000 });
                        fetchData();
                    }
                });
            }
        });
    });

    $('#editModal').on('hidden.bs.modal', function () {
        $('#editBrandForm')[0].reset();
        $('#logoPreview').hide();
    });

    fetchData(); // Initial data load
</script>
