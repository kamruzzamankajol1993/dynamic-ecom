@extends('admin.master.master')

@section('title')

Service Details | {{ $ins_name ?? 'Your App Name' }}

@endsection


@section('css')

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
    .detail-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px dashed #e9ecef; /* Dashed separator for subtle division */
    }
    .detail-item:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: 600; /* Semi-bold for labels */
        color: #495057; /* Dark grey for labels */
        width: 30%; /* Adjust label width */
        flex-shrink: 0;
    }
    .detail-value {
        color: #212529; /* Dark text for values */
        width: 70%; /* Adjust value width */
    }
    .price-display {
        font-size: 1.5rem; /* Larger price font */
        font-weight: 700; /* Extra bold price */
        color: #198754; /* Green color for price */
    }
    .status-badge {
        font-size: 0.9em;
        padding: 0.6em 1em;
        border-radius: 0.75rem; /* More rounded badges */
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .description-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin-top: 1rem;
        box-shadow: 0 0.2rem 0.5rem rgba(0,0,0,0.1);
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
    .btn-action-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 0.75rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 0.2rem 0.5rem rgba(0, 0, 0, 0.1);
    }
    .btn-action-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 0.3rem 0.6rem rgba(0, 0, 0, 0.15);
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
                <li class="breadcrumb-item"><a href="{{route('service.index')}}">Service Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Service Details</li>
            </ol>
        </nav>
        <!-- Breadcrumb End -->
    </div>


    <div class="card shadow-lg rounded-3">
        <div class="card-header">
            Service Details: {{ $service->title }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6 mb-4 mb-lg-0 text-center">
                    @if ($service->image)
                        <img src="{{ asset($service->image) }}" alt="{{ $service->title }} Image" class="img-fluid rounded-3 shadow-sm" style="max-height: 350px; width: auto; object-fit: cover;">
                    @else
                        <img src="https://placehold.co/350x350/E0E0E0/505050?text=No+Image" alt="No Image" class="img-fluid rounded-3 shadow-sm">
                    @endif
                </div>
                <div class="col-12 col-lg-6">
                    <h4 class="mb-4">Service Information</h4>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item detail-item">
                            <span class="detail-label">Title:</span>
                            <span class="detail-value">{{ $service->title }}</span>
                        </div>
                        <div class="list-group-item detail-item">
                            <span class="detail-label">Slug:</span>
                            <span class="detail-value">{{ $service->slug }}</span>
                        </div>
                        <div class="list-group-item detail-item">
                            <span class="detail-label">Price:</span>
                            <span class="detail-value price-display">৳{{ number_format($service->price, 2) }}</span>
                        </div>
                        <div class="list-group-item detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                @if($service->status == 'Active')
                                    <span class="badge bg-success status-badge">Active</span>
                                @else
                                    <span class="badge bg-danger status-badge">Inactive</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h4>Description</h4>
                <div class="description-content border p-3 rounded-3 bg-light">
                    {!! $service->des !!}
                </div>
            </div>

            <div class="mt-5 text-center">
                <a href="{{ route('service.index') }}" class="btn btn-action-primary d-inline-flex align-items-center me-2">
                    <i class="fas fa-arrow-left me-2"></i> Back to Services
                </a>
                <a href="{{ route('service.edit', $service->id) }}" class="btn btn-warning d-inline-flex align-items-center">
                    <i class="fas fa-edit me-2"></i> Edit Service
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

@endsection
