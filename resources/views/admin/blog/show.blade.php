@extends('admin.master.master')

@section('title')
{{ $blog->title }} | {{ $ins_name ?? 'Your App Name' }}
@endsection

@section('css')
<style>
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
    }
    .card-header.blog-title {
        background-color: #ffffff;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem 2.5rem;
        font-size: 1.75rem;
        font-weight: 700;
        color: #343a40;
    }
    .blog-image {
        width: 100%;
        max-height: 450px;
        object-fit: cover;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }
    .blog-content {
        padding: 2rem;
        line-height: 1.8;
        font-size: 1.1rem;
        color: #333;
    }
    .blog-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
    }
    .meta-info {
        padding: 0 2rem 1.5rem;
        color: #6c757d;
        font-style: italic;
    }
</style>
<style>
  
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
                <li class="breadcrumb-item active" aria-current="page">Viw Blog</li>
            </ol>
        </nav>
        <!-- Breadcrumb End -->

        <a href="{{ route('blog.index') }}" class="btn btn-back-to-list d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to Blog List
        </a>
    </div>

    <div class="card shadow-sm rounded-3 overflow-hidden">
        <img src="{{ asset($blog->image) }}" class="card-img-top blog-image" alt="{{ $blog->title }}">
        <div class="card-header blog-title text-center">
            {{ $blog->title }}
        </div>
        <div class="card-body blog-content">
            {!! $blog->des !!}
        </div>
        <div class="card-footer bg-white meta-info d-flex justify-content-between">
           <span><strong>Status:</strong> <span class="badge {{ $blog->status == 'active' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($blog->status) }}</span></span>
           <span><strong>Published on:</strong> {{ $blog->created_at->format('F d, Y') }}</span>
        </div>
    </div>
</div>
@endsection

@section('script')
{{-- Add any specific scripts for this page if needed --}}
@endsection
