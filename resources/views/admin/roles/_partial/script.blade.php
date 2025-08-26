<script>
   
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.roletable.data') }}",
        edit: id => `{{ route('roles.edit', ':id') }}`.replace(':id', id),
        show: id => `{{ route('roles.show', ':id') }}`.replace(':id', id),
        update: id => `{{ route('roles.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('roles.destroy', ':id') }}`.replace(':id', id),
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
            ${res.can_show ? `<a href="${routes.show(user.id)}" class="btn btn-sm btn-info btn-custom-sm"><i class="fas fa-clipboard-list"></i></a>` : ''}
                        
            ${res.can_edit ? `<a href="${routes.edit(user.id)}" class="btn btn-sm btn-primary btn-custom-sm"><i class="fa fa-edit"></i></a>` : ''}


                ${res.can_delete ? `<button class="btn btn-sm btn-danger btn-delete btn-custom-sm" data-id="${user.id}"><i class="fa fa-trash"></i></button>` : ''}

                     
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

<script>
    var exportInvoicesUrl = "{{ route('downloadRoleExcel') }}";
    var exportInvoicesUrlPdf = "{{ route('downloadRolePdf') }}";

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