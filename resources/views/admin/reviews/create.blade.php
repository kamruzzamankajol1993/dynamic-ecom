@extends('admin.master.master')
@section('title', 'Add New Review')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* --- Select2 Bootstrap 5 Compatibility Fixes --- */
    .select2-container .select2-selection--single {
        height: 38px !important; /* Match Bootstrap input height */
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 12px;
        color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 5px;
    }
    .select2-container--default .select2-selection--single:focus {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* --- Star Rating CSS --- */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 5px;
    }
    .star-rating input { display: none; }
    .star-rating label {
        cursor: pointer;
        font-size: 25px;
        color: #ddd;
        transition: color 0.2s;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffc107;
    }

    /* --- Image Preview CSS --- */
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }
    .preview-item {
        width: 100px;
        height: 100px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        position: relative;
    }
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .preview-item .remove-btn {
        position: absolute;
        top: 0;
        right: 0;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        cursor: pointer;
        width: 20px;
        height: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 14px;
        border-bottom-left-radius: 5px;
    }
    .preview-item .remove-btn:hover {
        background: red;
    }
</style>
@endsection

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Add New Review</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('review.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        {{-- Product Select2 --}}
                        <div class="col-md-6 mb-3">
                            <label for="product_id" class="form-label">Select Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                <option value="">-- Choose Product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- User Select2 --}}
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Select Customer <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- Choose User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-3">
                        <label class="form-label">Rating <span class="text-danger">*</span></label>
                        <div class="star-rating">
                            <input type="radio" name="rating" id="star5" value="5" @checked(old('rating') == 5)><label for="star5" class="fa fa-star"></label>
                            <input type="radio" name="rating" id="star4" value="4" @checked(old('rating') == 4)><label for="star4" class="fa fa-star"></label>
                            <input type="radio" name="rating" id="star3" value="3" @checked(old('rating') == 3)><label for="star3" class="fa fa-star"></label>
                            <input type="radio" name="rating" id="star2" value="2" @checked(old('rating') == 2)><label for="star2" class="fa fa-star"></label>
                            <input type="radio" name="rating" id="star1" value="1" @checked(old('rating') == 1)><label for="star1" class="fa fa-star"></label>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Review Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="is_approved" class="form-select" required>
                            <option value="1" @selected(old('is_approved') == 1)>Approved</option>
                            <option value="0" @selected(old('is_approved') == 0)>Pending</option>
                        </select>
                    </div>

                    {{-- Image Upload --}}
                    <div class="mb-3">
                        <label for="images" class="form-label">Review Images</label>
                        <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                        <div class="form-text">Supported formats: jpg, png, webp, gif.</div>
                        
                        <div id="image-preview" class="image-preview-container"></div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Review</button>
                        <a href="{{ route('review.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        
        // --- 1. Initialize Select2 ---
        $('#product_id').select2({
            placeholder: "-- Choose Product --",
            allowClear: true,
            width: '100%'
        });
        
        $('#user_id').select2({
            placeholder: "-- Choose User --",
            allowClear: true,
            width: '100%'
        });


        // --- 2. Image Preview Logic ---
        const dt = new DataTransfer();

        $('#images').on('change', function(e) {
            for (let i = 0; i < this.files.length; i++) {
                dt.items.add(this.files[i]);
            }
            this.files = dt.files;
            renderPreviews();
        });

        function renderPreviews() {
            const previewContainer = $('#image-preview');
            previewContainer.empty();

            $.each(dt.files, function(index, file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const html = `
                        <div class="preview-item">
                            <img src="${e.target.result}" alt="Preview">
                            <span class="remove-btn" data-index="${index}">&times;</span>
                        </div>
                    `;
                    previewContainer.append(html);
                };
                reader.readAsDataURL(file);
            });
        }

        $(document).on('click', '.remove-btn', function() {
            const indexToRemove = $(this).data('index');
            const newDt = new DataTransfer();
            
            $.each(dt.files, function(i, file) {
                if (i !== indexToRemove) {
                    newDt.items.add(file);
                }
            });

            dt.items.clear();
            for (let i = 0; i < newDt.files.length; i++) {
                dt.items.add(newDt.files[i]);
            }

            $('#images')[0].files = dt.files;
            renderPreviews();
        });
    });
</script>
@endsection