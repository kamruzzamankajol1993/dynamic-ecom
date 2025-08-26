<script>
$(document).ready(function() {
    // --- Custom Searchable Select jQuery Plugin ---
    function createSearchableSelect(originalSelect) {
        const $originalSelect = $(originalSelect);
        if ($originalSelect.next('.custom-select-container').length) {
            return; // Already initialized
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

        // Event Handlers
        $display.on('click', function(e) {
            e.stopPropagation();
            $('.custom-select-options').not($optionsContainer).hide();
            $optionsContainer.toggle();
        });

        $searchInput.on('click', function(e) {
            e.stopPropagation(); // Prevents the dropdown from closing when clicking the search input
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

    // --- Dependent Dropdown Logic ---
    function handleCategoryChange(categoryId, subcategorySelectId) {
        const $subcategorySelect = $(subcategorySelectId);
        if (!categoryId) {
            $subcategorySelect.html('<option value="">Select Category First</option>');
            return;
        }

        const route = `{{ url('get-subcategories') }}/${categoryId}`;
        $.get(route, function(data) {
            let options = '<option value="">Select Subcategory</option>';
            data.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            $subcategorySelect.html(options);
        });
    }

    // Listeners for dependent dropdowns in Add and Edit modals
    $('#addCategoryId').on('change', function() {
        handleCategoryChange($(this).val(), '#addSubcategoryId');
    });
    $('#editCategoryId').on('change', function() {
        handleCategoryChange($(this).val(), '#editSubcategoryId');
    });


    // --- CRUD Script ---
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    var currentPage = 1, searchTerm = '', sortColumn = 'id', sortDirection = 'desc';

    var routes = {
        fetch: "{{ route('ajax.sub-subcategory.data') }}",
        show: id => `{{ route('sub-subcategory.show', ':id') }}`.replace(':id', id),
        update: id => `{{ route('sub-subcategory.update', ':id') }}`.replace(':id', id),
        delete: id => `{{ route('sub-subcategory.destroy', ':id') }}`.replace(':id', id),
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
                    <td>${item.subcategory.name}</td>
                    <td>${item.subcategory.category.name}</td>
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

            const $editCategorySelect = $('#editCategoryId');
            const $editSubcategorySelect = $('#editSubcategoryId');

            $editCategorySelect.val(item.category_id);
            $editCategorySelect.next('.custom-select-container').remove();
            createSearchableSelect($editCategorySelect);
            const categoryText = $editCategorySelect.find('option:selected').text();
            $editCategorySelect.next('.custom-select-container').find('.custom-select-display').text(categoryText);

            const subcategoriesRoute = `{{ url('get-subcategories') }}/${item.category_id}`;
            $.get(subcategoriesRoute, function(data) {
                let options = '<option value="">Select Subcategory</option>';
                data.forEach(function(sub) {
                    options += `<option value="${sub.id}" ${sub.id == item.subcategory_id ? 'selected' : ''}>${sub.name}</option>`;
                });
                $editSubcategorySelect.html(options);
            });
            
            editModal.show();
        });
    });

    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editId').val();
        const btn = $(this).find('button[type="submit"]');
        const data = {
            name: $('#editName').val(),
            subcategory_id: $('#editSubcategoryId').val(),
            status: $('#editStatus').val(),
            _token: routes.csrf
        };
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        $.ajax({
            url: routes.update(id), method: 'PUT', data: data,
            success() {
                Swal.fire({ toast: true, icon: 'success', title: 'Updated successfully', showConfirmButton: false, timer: 3000 });
                editModal.hide(); fetchData();
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

    fetchData(); // Initial data load
});
</script>