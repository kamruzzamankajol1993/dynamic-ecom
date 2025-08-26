@extends('admin.master.master')

@section('title')

Edit Banner | {{ $ins_name ?? 'Your App Name' }}

@endsection


@section('css')
{{-- Bootstrap CSS --}}

<style>
   
    .card {
        border: none; /* No default border */
        border-radius: 0.75rem; /* Softly rounded corners */
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08); /* A medium-strength shadow */
        overflow: hidden;
    }
    .card-header {
        background-color: #ffffff; /* White header background */
        border-bottom: 1px solid #e9ecef; /* Light border */
        padding: 1.5rem 2.5rem;
        font-size: 1.35rem;
        font-weight: 700;
        color: #343a40;
        text-align: center;
    }
    .card-body {
        padding: 2.5rem;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .form-control, .form-select {
        border-radius: 0.4rem; /* Slightly less rounded inputs */
        padding: 0.65rem 1rem; /* Slightly smaller padding */
        border: 1px solid #ced4da;
    }
    .form-control:focus, .form-select:focus {
        border-color: #a2cffc; /* Lighter blue focus */
        box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25); /* Lighter shadow */
    }
    .invalid-feedback {
        font-size: 0.875rem;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: var(--bs-breadcrumb-divider, "/"); /* Revert to default slash divider */
        color: #6c757d;
        font-weight: normal;
    }
    .breadcrumb {
        background-color: #e9ecef; /* Light grey background for breadcrumb */
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    .breadcrumb-item a {
        color: #007bff; /* Standard Bootstrap blue for links */
        text-decoration: none;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d; /* Muted color for active breadcrumb */
    }

    .btn-action-primary {
        background-color: #28a745; /* Green for primary action */
        border-color: #28a745;
        padding: 0.75rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 0.5rem; /* Less rounded button */
        transition: all 0.2s ease-in-out;
        box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.1);
    }
    .btn-action-primary:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-1px); /* Subtle lift */
        box-shadow: 0 0.3rem 0.6rem rgba(0, 0, 0, 0.15);
    }
    .btn-outline-secondary {
        border-radius: 0.5rem;
        padding: 0.75rem 2rem;
        font-weight: 600;
    }
    .btn-outline-secondary:hover {
        transform: translateY(-1px);
    }

    /* Section Headers */
    .section-header {
        border-bottom: 2px solid #007bff; /* Blue underline */
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        color: #007bff;
        font-weight: 700;
        font-size: 1.2rem;
    }

    /* Image Preview */
    .image-preview-container {
        margin-top: 1rem;
        text-align: center;
        border: 1px solid #e9ecef;
        padding: 0.5rem;
        border-radius: 0.5rem;
        background-color: #fff;
    }
    .image-preview-container img {
        max-width: 150px; /* Slightly smaller preview */
        max-height: 150px;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        object-fit: cover;
        box-shadow: 0 0.1rem 0.3rem rgba(0,0,0,0.05);
    }
    .image-error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endsection


@section('body')

<div class="dashboard-body container py-4">

    <div class="mb-4">
        <!-- Breadcrumb Start -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('banner.index')}}">Banner Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Banner</li>
            </ol>
        </nav>
        <!-- Breadcrumb End -->
    </div>


    <div class="card shadow-lg rounded-3">
        {{-- Card Header --}}
        <div class="card-header">
            Edit Banner
            <p class="text-muted mb-0 mt-2">Update the Banner image and status.</p>
        </div>

        <div class="card-body">
            <form action="{{ route('banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data" id="sliderForm">
                @csrf
                @method('PUT')

                <div class="row g-4 justify-content-center">
                    {{-- Image & Status Section --}}
                    <div class="col-12 col-md-8 col-lg-6">
                        <h5 class="section-header text-center">Banner Image & Status</h5>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="image" class="form-label">Banner Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                <small id="image_size_error" class="image-error d-block"></small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="image-preview-container">
                                    <img id="imagePreview" src="{{ $banner->image ? asset($banner->image) : 'https://placehold.co/150x150/E0E0E0/505050?text=No+Image' }}" alt="Image Preview">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="Active" {{ old('status', $banner->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status', $banner->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <button type="submit" class="btn btn-action-primary me-3 d-inline-flex align-items-center">
                        <i class="fas fa-save me-2"></i> Update Banner
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')


<script>
    $(function() {
        // Image Preview and Size Validation
        $('#image').on('change', function() {
            const file = this.files[0];
            const maxFileSize = 500 * 1024; // 500 KB in bytes
            const imagePreview = $('#imagePreview');
            const imageError = $('#image_size_error');

            imageError.text(''); // Clear previous errors

            if (file) {
                if (file.size > maxFileSize) {
                    imageError.text('Image size exceeds 500KB. Please choose a smaller image.').addClass('d-block');
                    imagePreview.attr('src', 'https://placehold.co/150x150/FF0000/FFFFFF?text=Too+Large'); // Red placeholder
                    $(this).val(''); // Clear the input
                } else {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                // If no file is selected, revert to current image or default placeholder
                const currentImageUrl = imagePreview.data('current-src') || 'https://placehold.co/150x150/E0E0E0/505050?text=No+Image';
                imagePreview.attr('src', currentImageUrl);
            }
        });

        // Store current image src for edit view
        $('#imagePreview').data('current-src', $('#imagePreview').attr('src'));
    });
</script>
@endsection
