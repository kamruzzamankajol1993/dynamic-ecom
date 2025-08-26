@extends('admin.master.master')

@section('title')
Edit ClientSay | {{ $ins_name ?? 'Your App Name' }}
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
    .btn-update-clientsay {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-update-clientsay:hover {
        background-color: #0056b3;
        border-color: #0056b3;
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
                <li class="breadcrumb-item"><a href="{{route('clientSay.index')}}">ClientSay Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit ClientSay</li>
            </ol>
        </nav>
        <!-- Breadcrumb End -->

        <a href="{{ route('clientSay.index') }}" class="btn btn-back-to-list d-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Back to ClientSay List
        </a>
    </div>

    <div class="card shadow-lg rounded-3">
        <div class="card-header">
            Edit ClientSay
        </div>
        <div class="card-body">
            <form action="{{ route('clientSay.update', $clientsay->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Use PUT method for update requests --}}

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $clientsay->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="des" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('des') is-invalid @enderror" id="des" name="des" rows="4" required>{{ old('des', $clientsay->des) }}</textarea>
                    @error('des')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="youtube_video_link" class="form-label fw-bold">YouTube Video Link</label>
                    <input type="url" class="form-control @error('youtube_video_link') is-invalid @enderror" id="youtube_video_link" name="youtube_video_link" value="{{ old('youtube_video_link', $clientsay->youtube_video_link) }}">
                    <div class="form-text">Provide a valid YouTube video URL.</div>
                    @error('youtube_video_link')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="Active" {{ (old('status', $clientsay->status) == 'Active') ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ (old('status', $clientsay->status) == 'Inactive') ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-update-clientsay d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-sync-alt me-2"></i> Update ClientSay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')

@endsection
