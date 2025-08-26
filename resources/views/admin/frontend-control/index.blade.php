@extends('admin.master.master')
@section('title', 'Frontend Control')

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
    .item-type-badge {
        font-size: 0.75em;
        padding: 0.25em 0.5em;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Frontend Control Management</h2>

        <form action="{{ route('frontend.control.update') }}" method="POST">
            @csrf
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
                </div>

                {{-- Menu Management Column --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Header Menu Management</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Drag and drop to reorder the menu items.</p>
                            <ul id="sortable-menu" class="list-group">
                                @foreach($menuItems as $item)
                                <li class="list-group-item d-flex align-items-center gap-3">
                                    <i class="fa fa-bars sortable-handle text-muted"></i>
                                    <input type="hidden" name="menus[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                    <input type="hidden" class="menu-order" name="menus[{{ $loop->index }}][order]" value="{{ $item->order }}">
                                    
                                    <div class="flex-grow-1">
                                        <span class="fw-bold">{{ $item->name }}</span><br>
                                        @if($item->type === 'category')
                                            <span class="badge bg-primary-soft text-primary item-type-badge">Category</span>
                                        @else
                                            <span class="badge bg-success-soft text-success item-type-badge">{{ $item->type }}</span>
                                        @endif
                                    </div>
                                    
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
<script>
$(document).ready(function() {
    $("#sortable-menu").sortable({
        placeholder: "sortable-placeholder",
        handle: ".sortable-handle",
        update: function(event, ui) {
            // Update the 'order' input field for each item after sorting
            $('#sortable-menu .list-group-item').each(function(index) {
                $(this).find('.menu-order').val(index);
            });
        }
    }).disableSelection();
});
</script>
@endsection
