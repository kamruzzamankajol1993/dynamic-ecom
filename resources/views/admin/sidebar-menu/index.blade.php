@extends('admin.master.master')
@section('title', 'Sidebar Menu Control')

@section('css')
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
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Sidebar Menu Management</h2>

        <form action="{{ route('sidebar-menu.control.update') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5>Sidebar Menu Items (Categories)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Drag and drop to reorder the menu items that will appear on the frontend sidebar.</p>
                    <ul id="sortable-menu" class="list-group">
                        @foreach($menuItems as $item)
                        <li class="list-group-item d-flex align-items-center gap-3">
                            <i class="fa fa-bars sortable-handle text-muted"></i>
                            <input type="hidden" name="menus[{{ $loop->index }}][id]" value="{{ $item->id }}">
                            <input type="hidden" class="menu-order" name="menus[{{ $loop->index }}][order]" value="{{ $item->order }}">
                            
                            <span class="fw-bold flex-grow-1">{{ $item->name }}</span>
                            
                            <div class="input-group input-group-sm" style="max-width: 250px;">
                                <span class="input-group-text">Route</span>
                                <input type="text" class="form-control" name="menus[{{ $loop->index }}][route]" value="{{ $item->route }}">
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="menus[{{ $loop->index }}][is_visible]" value="1" id="menu-visible-{{$item->id}}" @if($item->is_visible) checked @endif>
                                <label class="form-check-label" for="menu-visible-{{$item->id}}">Visible</label>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save Sidebar Menu</button>
            </div>
        </form>
    </div>
</main>
@endsection

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
$(document).ready(function() {
    $("#sortable-menu").sortable({
        placeholder: "sortable-placeholder",
        handle: ".sortable-handle",
        update: function(event, ui) {
            $('#sortable-menu .list-group-item').each(function(index) {
                $(this).find('.menu-order').val(index);
            });
        }
    }).disableSelection();
});
</script>
@endsection