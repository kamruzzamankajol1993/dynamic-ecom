<script>
  
  
    var routes = {
        fetch: "{{ route('ajax.systemInformationtable.data') }}",
        edit: id => `{{ route('systemInformation.edit', ':id') }}`.replace(':id', id),
        update: id => `{{ route('systemInformation.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('systemInformation.destroy', ':id') }}`.replace(':id', id),
        csrf: "{{ csrf_token() }}"
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
    }, function (res) {
        let rows = '';
        res.data.forEach((item, index) => {
            rows += `
                <tr>
                    <td>${(res.current_page - 1) * 10 + index + 1}</td>
                    <td>${item.branch_name ?? ''}</td>
                    <td><img src="{{ asset('/') }}${item.icon}" style="height:40px;"></td>
                    <td><img src="{{ asset('/') }}${item.logo}" style="height:60px;"></td>
                    <td><img src="{{ asset('/') }}${item.black_logo}" style="height:60px;"></td>
                    <td>${item.ins_name}</td>
                    <td>${item.phone}</td>
                    <td>${item.email}</td>
                    <td>${item.address}</td>
                   
                    <td>
                        ${res.can_edit ? `<a href="${routes.edit(item.id)}" class="btn btn-sm btn-primary btn-custom-sm"><i class="fa fa-edit"></i></a>` : ''}


                ${res.can_delete ? `<button class="btn btn-sm btn-danger btn-delete btn-custom-sm" data-id="${item.id}"><i class="fa fa-trash"></i></button>` : ''}
                    </td>
                </tr>`;
        });
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
    var exportInvoicesUrl = "{{ route('downloadSystemInformationExcel') }}";
    var exportInvoicesUrlPdf = "{{ route('downloadSystemInformationPdf') }}";

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