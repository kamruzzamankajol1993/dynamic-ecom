@extends('admin.master.master')

@section('title')
Add New News & Media | {{ $ins_name ?? 'Your App Name' }}
@endsection

@section('css')

<style>
   
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem 2.5rem;
        font-size: 1.35rem;
        font-weight: 700;
        color: #343a40;
        text-align: center;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: var(--bs-breadcrumb-divider, "/");
        color: #6c757d;
        font-weight: normal;
    }
    .breadcrumb {
        background-color: #e9ecef;
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    .breadcrumb-item a {
        color: #007bff;
        text-decoration: none;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    .btn-back-to-list {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-back-to-list:hover {
        background-color: #5a6268;
        border-color: #5a6268;
        transform: translateY(-1px);
    }
    .btn-save-news {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-save-news:hover {
        background-color: #218838;
        border-color: #218838;
        transform: translateY(-1px);
    }
    .image-preview {
        max-width: 200px;
        height: auto;
        border-radius: 5px;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        margin-top: 10px;
        display: none; /* Hidden by default */
    }
</style>
@endsection

@section('body')

<div class="dashboard-body container py-4">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <!-- Breadcrumb Start -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('newsAndMedia.index')}}">News & Media Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New</li>
            </ol>
        </nav>
        <!-- Breadcrumb End -->

        <a href="{{ route('newsAndMedia.index') }}" class="btn btn-back-to-list d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to List
        </a>
    </div>

    <div class="card shadow-lg rounded-3">
        <div class="card-header">
            Add New News & Media Entry
        </div>
        <div class="card-body">
            <form action="{{ route('newsAndMedia.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="des" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('des') is-invalid @enderror" id="des" name="des" rows="6" required>{{ old('des') }}</textarea>
                    @error('des')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Image <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <img id="imagePreview" src="#" alt="Image Preview" class="image-preview">
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-save-news d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-plus-circle me-2"></i> Add Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Summernote
        $('#des').summernote({
            placeholder: 'Enter news or media description...',
            tabsize: 2,
            height: 200, // Set height of the editor
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // Image Preview Logic
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block'; // Show the image preview
                };

                reader.readAsDataURL(event.target.files[0]);
            } else {
                imagePreview.src = '#';
                imagePreview.style.display = 'none'; // Hide if no file is selected
            }
        });
    });
</script>
@endsection
