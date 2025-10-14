<script>
    // Global AJAX setup for CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });

    var routes = {
        fetch: "{{ route('ajax.systemInformationtable.data') }}",
        edit: id => `{{ route('systemInformation.edit', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('systemInformation.destroy', ':id') }}`.replace(':id', id)
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
        }, function(res) {
            let rows = '';
            if (res.data.length === 0) {
                rows = `<tr><td colspan="8" class="text-center">No records found.</td></tr>`;
            } else {
                res.data.forEach((item, index) => {
                    const serialNumber = (res.current_page - 1) * 10 + index + 1;
                    const phone = item.phone + (item.phone_one ? `<br><small>${item.phone_one}</small>` : '');
                    const email = item.email + (item.email_one ? `<br><small>${item.email_one}</small>` : '');

                    rows += `
                        <tr>
                            <td>${serialNumber}</td>
                            <td>${item.branch_name ?? 'N/A'}</td>
                            <td><img src="{{ asset('/') }}${item.icon}" style="height:40px; width:40px; object-fit:cover;"></td>
                            <td><img src="{{ asset('/') }}${item.logo}" style="height:40px;"></td>
                            <td>${item.ins_name}</td>
                            <td>${phone}</td>
                            <td>${email}</td>
                            <td>
                                ${res.can_edit ? `<a href="${routes.edit(item.id)}" class="btn btn-sm btn-primary btn-custom-sm" title="Edit"><i class="fas fa-edit"></i></a>` : ''}
                                ${res.can_delete ? `<button class="btn btn-sm btn-danger btn-delete btn-custom-sm" data-id="${item.id}" title="Delete"><i class="fas fa-trash-alt"></i></button>` : ''}
                            </td>
                        </tr>`;
                });
            }
            $('#tableBody').html(rows);

            // Pagination
            var paginationHtml = '';
            if (res.last_page > 1) {
                paginationHtml += `<li class="page-item ${res.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page - 1}">Prev</a></li>`;
                
                const start = Math.max(1, res.current_page - 2);
                const end = Math.min(res.last_page, res.current_page + 2);

                for (let i = start; i <= end; i++) {
                    paginationHtml += `<li class="page-item ${i === res.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }

                paginationHtml += `<li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${res.current_page + 1}">Next</a></li>`;
            }
            $('#pagination').html(paginationHtml);
            $('#pagination-info').text(`Showing ${res.data.length > 0 ? ((res.current_page - 1) * 10 + 1) : 0} to ${(res.current_page - 1) * 10 + res.data.length} of ${res.total} entries`);
        });
    }

    $(document).on('keyup', '#searchInput', function() {
        searchTerm = $(this).val();
        currentPage = 1;
        fetchData();
    });

    $(document).on('click', '.sortable', function() {
        const col = $(this).data('column');
        sortDirection = (sortColumn === col && sortDirection === 'asc') ? 'desc' : 'asc';
        sortColumn = col;
        fetchData();
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && !$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
            currentPage = page;
            fetchData();
        }
    });

    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.delete(id),
                    method: 'DELETE',
                    success: function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Record deleted successfully',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        
                        // Re-fetch and adjust page if the last item on a page was deleted
                        $.get(routes.fetch, { page: currentPage, search: searchTerm }, function (res) {
                            if (res.data.length === 0 && currentPage > 1) {
                                currentPage--;
                            }
                            fetchData();
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Could not delete the record.', 'error');
                    }
                });
            }
        });
    });

    fetchData();

    // Export functionality
    document.getElementById('invoiceFilter').addEventListener('change', function() {
        var selected = this.value;
        if (!selected) return;
        
        var url = selected === 'excel' ? "{{ route('downloadSystemInformationExcel') }}" : "{{ route('downloadSystemInformationPdf') }}";
        
        window.location.href = url;
        // Reset dropdown after selection
        this.value = '';
    });
</script>