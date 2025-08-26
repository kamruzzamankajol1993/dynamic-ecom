@extends('admin.master.master')

@section('title')
Edit Social Link | {{ $ins_name ?? 'Your App Name' }}
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
</style>
@endsection

@section('body')

<main class="main-content">
    <div class="container-fluid">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <!-- Breadcrumb Start -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
               
            </ol>
        </nav>
        <!-- Breadcrumb End -->

        <a href="{{ route('socialLink.index') }}" class="btn btn-back-to-list d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to Social Link List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg rounded-3">
        <div class="card-header">
            Edit Social Link
        </div>
        <div class="card-body">
            <form action="{{ route('socialLink.update', $socialLink->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="title" class="form-label">Social Media Platform</label>
                            <select class="form-select" id="title" name="title" required>
                                <option disabled value="">Choose...</option>
                                @foreach ($socialMediaNames as $platform)
                                    <option value="{{ $platform }}" {{ $socialLink->title == $platform ? 'selected' : '' }}>{{ $platform }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="link" class="form-label">Profile Link</label>
                            <input type="url" class="form-control" id="link" name="link" value="{{ $socialLink->link }}" required>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Update Link</button>
                    <a href="{{ route('socialLink.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
@endsection

@section('script')
{{-- Add any specific scripts for this page if needed --}}
@endsection
