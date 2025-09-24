@extends('admin.master.master')
@section('title', 'Frontend Control')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .sortable-placeholder {
        border: 2px dashed #ccc;
        background-color: #f8f9fa;
        height: 50px;
        margin-bottom: 0.5rem;
    }
    .sortable-handle {
        cursor: move;
        font-size: 1.2rem;
    }
    .item-type-badge {
        font-size: 0.75em;
        padding: 0.25em 0.5em;
    }
    /* --- NEW: Style for the scrollable menu container --- */
    .menu-list-container {
        max-height: 60vh; /* Set a maximum height relative to the viewport */
        overflow-y: auto; /* Add a vertical scrollbar only when needed */
        padding-right: 10px; /* Space for the scrollbar */
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Frontend Control Management</h2>
        @include('flash_message')

        <form action="{{ route('frontend.control.update') }}" method="POST">
            @csrf
            {{-- --- MODIFIED: Restored the side-by-side layout --- --}}
            <div class="row">
                {{-- Header Settings Column --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Header Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="headerColor" class="form-label">Header Background Color</label>
                                <input type="color" class="form-control form-control-color" id="headerColor" name="header_color" value="{{ old('header_color', $headerColor) }}">
                            </div>
                            <div class="mb-3">
                                <label for="menuLimit" class="form-label">Menu Limit</label>
                                <input type="number" class="form-control" id="menuLimit" name="menu_limit" value="{{ old('menu_limit', $menuLimit) }}" min="1">
                            </div>
                        </div>
                    </div>
                     <div class="card">
                        <div class="card-header">
                            <h5>Support Section</h5>
                        </div>
                        <div class="card-body">
                            {{-- Replaced dynamic rows with two simple inputs --}}
                            <div class="mb-3">
                                <label for="supportTitle" class="form-label">Support Title</label>
                                <input type="text" id="supportTitle" name="support_title" class="form-control" placeholder="e.g., For Order" value="{{ old('support_title', $support->title ?? '') }}">
                            </div>
                            <div class="mb-3">
                                <label for="supportPhone" class="form-label">Support Phone Number</label>
                                <input type="text" id="supportPhone" name="support_phone" class="form-control" placeholder="e.g., +8801..." value="{{ old('support_phone', $support->phone ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Menu Management Column --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Header Menu Management</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Drag and drop to reorder the menu items.</p>

                            {{-- --- NEW: Scrollable Container for the list --- --}}
                            <div class="menu-list-container">
                                <ul id="sortable-menu" class="list-group">
                                    @foreach($menuItems as $item)
                                    <li class="list-group-item d-flex align-items-center gap-3">
                                        <i class="fa fa-bars sortable-handle text-muted"></i>
                                        <input type="hidden" name="menus[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                        <input type="hidden" class="menu-order" name="menus[{{ $loop->index }}][order]" value="{{ $item->order }}">
                                        
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control form-control-sm mb-1" name="menus[{{ $loop->index }}][name]" value="{{ $item->name }}">
                                            @if($item->type === 'category')
                                                <span class="badge bg-primary-soft text-primary item-type-badge">Category</span>
                                            @elseif($item->type === 'extracategory')
                                                <span class="badge bg-warning-soft text-warning item-type-badge">Extra Category</span>
                                            @else
                                                <span class="badge bg-success-soft text-success item-type-badge">{{ $item->type }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="input-group input-group-sm" style="max-width: 200px;">
                                            <span class="input-group-text">Route</span>
                                            <input type="text" class="form-control" name="menus[{{ $loop->index }}][route]" value="{{ $item->route }}">
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="menus[{{ $loop->index }}][is_visible]" value="1" id="menu-visible-{{$item->id}}" @if($item->is_visible) checked @endif>
                                            <label class="form-check-label" for="menu-visible-{{$item->id}}">Visible</label>
                                        </div>

                                        {{-- <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                data-action="{{ route('frontend.control.menu.destroy', $item->id) }}" 
                                                title="Delete Menu Item">
                                            <i class="fa fa-trash"></i>
                                        </button> --}}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</main>
@endsection

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // The JavaScript for sorting and deleting is unchanged.
    $("#sortable-menu").sortable({
        placeholder: "sortable-placeholder",
        handle: ".sortable-handle",
        update: function(event, ui) {
            $('#sortable-menu .list-group-item').each(function(index) {
                $(this).find('.menu-order').val(index);
            });
        }
    }).disableSelection();

    $(document).on('click', '.btn-delete', function() {
        const deleteButton = $(this);
        const itemName = deleteButton.closest('li').find('input[name$="[name]"]').val();

        Swal.fire({
            title: `Delete "${itemName}"?`,
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const actionUrl = deleteButton.data('action');
                $('<form>', {
                    "method": "POST",
                    "action": actionUrl
                })
                .append($('<input>', {"name": "_token", "value": "{{ csrf_token() }}", "type": "hidden"}))
                .append($('<input>', {"name": "_method", "value": "DELETE", "type": "hidden"}))
                .appendTo('body')
                .submit();
            }
        });
    });
});
</script>
@endsection