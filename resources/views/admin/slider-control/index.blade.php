@extends('admin.master.master')
@section('title', 'Slider Control')
@section('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .product-list, .product-source { 
            min-height: 100px; 
            border: 1px dashed #ccc;
            border-radius: 0.25rem;
            padding: 10px;
        }
        .sortable-placeholder { 
            border: 2px dashed #0d6efd; 
            background-color: #f8f9fa; 
            height: 50px; 
            border-radius: 0.25rem;
        }
        #search-results li, .product-list li { 
            cursor: grab; 
            font-size: 0.9em;
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .list-group-item .btn-close {
            margin-left: 10px;
        }
        .custom-toast-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
            padding: 1rem 1.5rem;
            border-radius: 0.25rem;
            color: #fff;
            display: none;
        }
        /* Style for the item being dragged */
        .ui-sortable-helper {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            opacity: 0.9;
        }
    </style>
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Homepage Slider & Banner Control</h2>
        <form action="{{ route('slider.control.update') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Search & Source Column -->
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">1. Find Products</h5>
                            <div class="mb-3">
                                <label class="form-label">Search by Name or Code</label>
                                <input type="text" id="productSearch" class="form-control" placeholder="Start typing...">
                            </div>
                            <hr>
                            <h6 class="text-muted">Search Results (Drag items from here)</h6>
                            <ul id="search-results" class="list-group product-source flex-grow-1">
                                <li class="list-group-item text-center text-muted border-0">Search to see results</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Destination Columns -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">2. Assign to Sections</h5>
                            <p class="text-muted">Drag products from the search results into the sections below. You can also drag items between sections.</p>
                            <div class="row">
                                <!-- Main Slider -->
                                <div class="col-md-4">
                                    <div class="text-center p-2 bg-light rounded-top">
                                        <h6 class="mb-0">{{ $mainSlider->title }}</h6>
                                    </div>
                                    <ul class="list-group product-list rounded-bottom" id="main_slider_list" data-limit="2">
                                        @foreach($mainSlider->products as $product)
                                        <li class="list-group-item" data-id="{{$product->id}}"><span>{{$product->name}}</span><button type="button" class="btn-close"></button></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Top Banner -->
                                <div class="col-md-4">
                                    <div class="text-center p-2 bg-light rounded-top">
                                        <h6 class="mb-0">{{ $topBanner->title }}</h6>
                                    </div>
                                    <ul class="list-group product-list rounded-bottom" id="top_banner_list" data-limit="1">
                                        @foreach($topBanner->products as $product)
                                        <li class="list-group-item" data-id="{{$product->id}}"><span>{{$product->name}}</span><button type="button" class="btn-close"></button></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Bottom Banners -->
                                <div class="col-md-4">
                                    <div class="text-center p-2 bg-light rounded-top">
                                        <h6 class="mb-0">{{ $bottomBanners->title }}</h6>
                                    </div>
                                    <ul class="list-group product-list rounded-bottom" id="bottom_banners_list" data-limit="2">
                                        @foreach($bottomBanners->products as $product)
                                        <li class="list-group-item" data-id="{{$product->id}}"><span>{{$product->name}}</span><button type="button" class="btn-close"></button></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="form-inputs">
                {{-- Hidden inputs will be dynamically added here --}}
            </div>
            <button type="submit" class="btn btn-primary mt-4">Save Slider Settings</button>
        </form>
    </div>
</main>
<div id="custom-alert" class="custom-toast-alert"></div>
@endsection
@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
$(function() {
    // Custom alert function
    function showAlert(message, type = 'danger') {
        const alertBox = $('#custom-alert');
        alertBox.text(message).removeClass('bg-danger bg-success').addClass(`bg-${type}`);
        alertBox.fadeIn().delay(3000).fadeOut();
    }

    // Initialize sortable lists for drag and drop
    $("#search-results, .product-list").sortable({
        connectWith: ".product-list",
        placeholder: "list-group-item list-group-item-light",
        helper: 'clone',
        appendTo: 'body',
        start: function(event, ui) {
            if ($(this).attr('id') === 'search-results') {
                ui.item.addClass('is-new-item');
            }
            ui.item.css('opacity', 0.5);
        },
        receive: function(event, ui) {
            const targetList = $(this);
            const limit = parseInt(targetList.data('limit'));

            if (targetList.children().length > limit) {
                $(ui.sender).sortable('cancel');
                showAlert(`This section can only have ${limit} product(s).`, 'danger');
                return;
            }

            if (ui.item.hasClass('is-new-item')) {
                ui.item.removeClass('is-new-item');
                ui.item.find('span').after('<button type="button" class="btn-close"></button>');
            }
        },
        stop: function(event, ui) {
            ui.item.css('opacity', 1);
            updateAllHiddenInputs();
        }
    }).disableSelection();

    // --- NEW REAL-TIME SEARCH LOGIC ---
    let searchTimeout;
    $('#productSearch').on('keyup', function() {
        const searchTerm = $(this).val();
        const resultsList = $('#search-results');

        clearTimeout(searchTimeout);

        if (searchTerm.length < 1) {
            resultsList.html('<li class="list-group-item text-center text-muted border-0">Search to see results</li>');
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: "{{ route('slider.control.search') }}",
                data: { term: searchTerm },
                dataType: "json",
                success: function(data) {
                    resultsList.empty();
                    if (data.length > 0) {
                        data.forEach(item => {
                            if ($(`.product-list [data-id=${item.id}]`).length === 0) {
                                resultsList.append(`<li class="list-group-item" data-id="${item.id}"><span>${item.name}</span></li>`);
                            }
                        });
                    } else {
                        resultsList.html('<li class="list-group-item text-center text-muted border-0">No results found</li>');
                    }
                }
            });
        }, 300); // 300ms delay before sending request
    });

    // Handle removing an item from a list
    $('.product-list').on('click', '.btn-close', function() {
        $(this).closest('li').remove();
        updateAllHiddenInputs();
    });

    // This function syncs the visual lists with hidden inputs for form submission
    function updateAllHiddenInputs() {
        const formInputs = $('#form-inputs');
        formInputs.empty();
        $('.product-list').each(function() {
            const list = $(this);
            const inputName = list.attr('id').replace('_list', '_products') + '[]';
            list.children('li').each(function() {
                const productId = $(this).data('id');
                formInputs.append(`<input type="hidden" name="${inputName}" value="${productId}">`);
            });
        });
    }

    updateAllHiddenInputs();
});
</script>
@endsection
