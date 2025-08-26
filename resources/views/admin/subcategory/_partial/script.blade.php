
<script>
$(document).ready(function() {
    // --- Custom Searchable Select jQuery Plugin ---
    function createSearchableSelect(originalSelect) {
        const $originalSelect = $(originalSelect);
        if ($originalSelect.next('.custom-select-container').length) {
            return;
        }
        $originalSelect.hide();

        const $container = $('<div class="custom-select-container" />');
        const $display = $('<div class="custom-select-display" />').text($originalSelect.find('option:selected').text());
        const $optionsContainer = $('<div class="custom-select-options" />');
        const $searchInput = $('<input type="text" class="custom-select-search-input" placeholder="Search...">');

        $optionsContainer.append($searchInput);

        $originalSelect.find('option').each(function() {
            const $option = $(this);
            const $customOption = $('<div class="custom-select-option" />')
                .data('value', $option.val())
                .text($option.text());

            if ($option.val() === '') {
                $customOption.addClass('is-hidden');
            }

            $optionsContainer.append($customOption);
        });

        $originalSelect.after($container.append($display).append($optionsContainer));

        // --- Event Handlers ---
        $display.on('click', function(e) {
            e.stopPropagation();
            $('.custom-select-options').not($optionsContainer).hide();
            $optionsContainer.toggle();
        });

        // *** THE FIX IS HERE ***
        // Stop click on search input from closing the dropdown
        $searchInput.on('click', function(e) {
            e.stopPropagation();
        });

        $searchInput.on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $optionsContainer.find('.custom-select-option').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggleClass('is-hidden', text.indexOf(searchTerm) === -1);
            });
        });

        $optionsContainer.on('click', '.custom-select-option', function() {
            const value = $(this).data('value');
            const text = $(this).text();

            $originalSelect.val(value).trigger('change');
            $display.text(text);
            $optionsContainer.hide();
        });
    }

    // Initialize all searchable selects on page load
    $('.searchable-select').each(function() {
        createSearchableSelect(this);
    });

    // Global click listener to close dropdowns
    $(document).on('click', function() {
        $('.custom-select-options').hide();
    });


    // --- Your Existing CRUD Script ---
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.subcategory.data') }}",
        show: id => `{{ route('subcategory.show', ':id') }}`.replace(':id', id),
        update: id => `{{ route('subcategory.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('subcategory.destroy', ':id') }}`.replace(':id', id),
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
                    <td>${(res.current_page - 1) * 10 + i + 1}</td>
                    <td>${item.name}</td>
                    <td>${item.category.name}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-info btn-edit" data-id="${item.id}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;
            });
            $('#tableBody').html(rows);

            // Pagination logic
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

    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        currentPage = $(this).data('page');
        fetchData();
    });

    // MODIFIED EDIT BUTTON CLICK HANDLER
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.get(routes.show(id), function (item) {
            $('#editId').val(item.id);
            $('#editName').val(item.name);
            $('#editStatus').val(item.status);

            // --- Logic for custom select in Edit Modal ---
            const $editSelect = $('#editCategoryId');
            
            // 1. Set the value of the original hidden select
            $editSelect.val(item.category_id);

            // 2. Remove the old custom dropdown if it exists to avoid duplicates
            $editSelect.next('.custom-select-container').remove();

            // 3. Re-initialize the plugin on the select
            createSearchableSelect($editSelect);
            
            // 4. Update the display text of the new custom dropdown to match the selected value
            const selectedText = $editSelect.find('option:selected').text();
            $editSelect.next('.custom-select-container').find('.custom-select-display').text(selectedText);
            
            editModal.show();
        });
    });

    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editId').val();
        const btn = $(this).find('button[type="submit"]');
        const data = {
            name: $('#editName').val(),
            category_id: $('#editCategoryId').val(),
            status: $('#editStatus').val(),
            _token: routes.csrf
        };
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        $.ajax({
            url: routes.update(id),
            method: 'PUT',
            data: data,
            success() {
                Swal.fire({ toast: true, icon: 'success', title: 'Updated successfully', showConfirmButton: false, timer: 3000 });
                editModal.hide();
                fetchData();
            },
            error(xhr) {
                // You can add validation error handling here if needed
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

    // Initial data load
    fetchData();
});
</script>