<script>
$(document).ready(function() {
    // --- Dynamic Row Logic ---
    function addEntryRow(container, entry = {}) {
        const index = $(container).children().length;
        const size = entry.size || '';
        const length = entry.length || '';
        const width = entry.width || '';
        const shoulder = entry.shoulder || '';
        const sleeve = entry.sleeve || '';

        const newRow = `
            <div class="row align-items-end entry-row mb-2">
                <div class="col-md-2"><label class="form-label form-label-sm">Size</label><input type="text" name="entries[${index}][size]" class="form-control form-control-sm" value="${size}" required></div>
                <div class="col-md-2"><label class="form-label form-label-sm">Length</label><input type="text" name="entries[${index}][length]" class="form-control form-control-sm" value="${length}"></div>
                <div class="col-md-2"><label class="form-label form-label-sm">Width</label><input type="text" name="entries[${index}][width]" class="form-control form-control-sm" value="${width}"></div>
                <div class="col-md-2"><label class="form-label form-label-sm">Shoulder</label><input type="text" name="entries[${index}][shoulder]" class="form-control form-control-sm" value="${shoulder}"></div>
                <div class="col-md-2"><label class="form-label form-label-sm">Sleeve</label><input type="text" name="entries[${index}][sleeve]" class="form-control form-control-sm" value="${sleeve}"></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-entry-btn w-100"><i class="fa fa-trash"></i></button></div>
            </div>`;
        $(container).append(newRow);
    }

    // Add row button for the "Add" modal
    $('#add-entry-btn').on('click', () => addEntryRow('#add-entry-container'));
    
    // Add row button for the "Edit" modal
    $('#edit-add-entry-btn').on('click', () => addEntryRow('#edit-entry-container'));

    // Remove row button (delegated event)
    $(document).on('click', '.remove-entry-btn', function() {
        $(this).closest('.entry-row').remove();
    });


    // --- CRUD Script ---
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.size-chart.data') }}",
        show: id => `{{ route('size-chart.show', ':id') }}`.replace(':id', id),
        update: id => `{{ route('size-chart.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('size-chart.destroy', ':id') }}`.replace(':id', id),
        csrf: "{{ csrf_token() }}"
    };

    function fetchData() {
        $.get(routes.fetch, {
            page: currentPage, search: searchTerm, sort: sortColumn, direction: sortDirection
        }, function (res) {
            let rows = '';
            res.data.forEach((item, i) => {
                const statusBadge = item.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                const entryCount = item.entries.length;
                rows += `<tr>
                    <td>${(res.current_page - 1) * 10 + i + 1}</td>
                    <td>${item.name}</td>
                    <td>${entryCount}</td>
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
        sortColumn = col; fetchData();
    });
    $(document).on('click', '.page-link', function (e) { e.preventDefault(); currentPage = $(this).data('page'); fetchData(); });

    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.get(routes.show(id), function (item) {
            $('#editId').val(item.id);
            $('#editName').val(item.name);
            $('#editStatus').val(item.status);

            const container = $('#edit-entry-container');
            container.empty(); // Clear previous entries
            if(item.entries && item.entries.length > 0) {
                item.entries.forEach(entry => addEntryRow(container, entry));
            }

            editModal.show();
        });
    });

     // MODIFIED UPDATE HANDLER
    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editId').val();
        const btn = $(this).find('button[type="submit"]');
        
        // Use serialize() and manually add the CSRF token and method
        let formData = $(this).serialize() + '&_token=' + routes.csrf + '&_method=PUT';

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        
        $.ajax({
            url: routes.update(id),
            method: 'POST', // Use POST to tunnel the PUT request
            data: formData,
            success() {
                Swal.fire({ toast: true, icon: 'success', title: 'Updated successfully', showConfirmButton: false, timer: 3000 });
                editModal.hide();
                fetchData();
            },
            error(xhr) {
                // Handle potential errors, like validation
                if (xhr.status === 422) {
                    // You can add logic here to display validation errors
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please check your input.' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!' });
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
            title: 'Are you sure?',
            text: "This will delete the chart and all its entries!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: routes.delete(id),
                    method: 'DELETE',
                    data: { _token: routes.csrf },
                    success: () => {
                        Swal.fire({ toast: true, icon: 'success', title: 'Deleted successfully', showConfirmButton: false, timer: 3000 });
                        fetchData();
                    }
                });
            }
        });
    });
    
    // Clear modal on close
    $('#addModal').on('hidden.bs.modal', function(){
        $(this).find('form')[0].reset();
        $('#add-entry-container').empty();
    });

    fetchData(); // Initial data load
});
</script>
