@extends('admin.master.master')

@section('title')
Create Blog Post | {{ $ins_name ?? 'Your App Name' }}
@endsection

@section('css')

<style>
    .note-editor.note-frame {
        border-radius: 0.5rem;
    }
    /* Style for the image preview */
    .image-preview-container {
        margin-top: 15px;
    }
    #image-preview {
        max-width: 200px;
        max-height: 200px;
        border-radius: 0.5rem;
        border: 1px solid #ddd;
        padding: 5px;
        display: none; /* Hidden by default */
    }
</style>

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
    .btn-create-blog {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-create-blog:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-1px);
    }
    .table thead th {
        background-color: #f2f2f2;
        font-weight: 600;
        color: #343a40;
    }
    .table tbody tr:hover {
        background-color: #f5f5f5;
    }
    .action-buttons .btn {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        margin-right: 0.25rem;
    }
    .status-badge {
        font-size: 0.8em;
        padding: 0.4em 0.8em;
        border-radius: 0.5rem;
    }
    /* SweetAlert2 Custom Styling */
    .swal2-popup {
        border-radius: 0.75rem !important;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    .swal2-title {
        color: #343a40 !important;
        font-weight: 700 !important;
    }
    .swal2-html-container {
        color: #6c757d !important;
    }
    .swal2-confirm.swal2-styled {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
    .swal2-cancel.swal2-styled {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
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
</style>
@endsection

@section('body')
<div class="dashboard-body container py-4">
     <div class="mb-4 d-flex justify-content-between align-items-center">
        <!-- Breadcrumb Start -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('blog.index')}}">Blog Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New</li>
            </ol>
        </nav>
        <!-- Breadcrumb End -->

        <a href="{{ route('blog.index') }}" class="btn btn-back-to-list d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to Blog List
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow-sm rounded-3">
        <div class="card-header text-center">
            <h3 class="mb-0">Create New Blog Post</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label for="des" class="form-label">Description</label>
                    <textarea class="form-control" id="summernote" name="des" rows="10">{{ old('des') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Feature Image</label>
                    <input class="form-control" type="file" id="image" name="image" required>
                    <!-- Image Preview Container -->
                    <div class="image-preview-container">
                         <img id="image-preview" src="#" alt="Image Preview"/>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Create Post</button>
                    <a href="{{ route('blog.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')

<script>
    $(document).ready(function() {
        // Initialize Summernote
        $('#summernote').summernote({
            placeholder: 'Write your blog description here...',
            tabsize: 2,
            height: 300
        });

        // Image preview script
        $('#image').on('change', function(event) {
            const preview = $('#image-preview');
            const file = event.target.files[0];
            
            if (file) {
                // Create a new FileReader instance
                const reader = new FileReader();

                // Set the image source once the file is loaded
                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                    preview.css('display', 'block'); // Show the preview
                }

                // Read the file as a Data URL
                reader.readAsDataURL(file);
            } else {
                // Hide the preview if no file is selected
                preview.attr('src', '#');
                preview.css('display', 'none');
            }
        });
    });
</script>
@endsection
