@extends('admin.master.master')

@section('title')
Review Management | {{ $ins_name ?? 'Your App Name' }}
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
    .btn-create-Review {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-create-Review:hover {
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
</style>
@endsection

@section('body')

<div class="dashboard-body container py-4">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <!-- Breadcrumb Start -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Review Management</li>
            </ol>
        </nav>
        <!-- Breadcrumb End -->

        <a href="{{ route('review.create') }}" class="btn btn-create-Review d-inline-flex align-items-center">
            <i class="fas fa-plus-circle me-2"></i> Add New Review
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg rounded-3">
        <div class="card-header">
            Review List
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>YouTube Link</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr>
                                <td>{{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</td>
                                <td>{{ Str::limit($review->title, 50) }}</td>
                                <td>{!! Str::limit($review->des, 80) !!}</td>
                                <td>
                                    @if($review->youtube_video_link)
                                        <a href="{{ $review->youtube_video_link }}" target="_blank" class="text-decoration-none">
                                            <i class="fab fa-youtube text-danger me-1"></i> Watch Video
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($review->status == 'Active')
                                        <span class="badge bg-success status-badge">Active</span>
                                    @else
                                        <span class="badge bg-danger status-badge">Inactive</span>
                                    @endif
                                </td>
                                <td class="action-buttons">
                                   
                                    <a href="{{ route('review.edit', $review->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('review.destroy', $review->id) }}" method="POST" class="d-inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No client sayings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $reviews->links('pagination::bootstrap-5') }} {{-- Using Bootstrap 5 pagination style --}}
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        popup: 'rounded-3 shadow-lg',
                        title: 'text-dark',
                        htmlContainer: 'text-secondary',
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary',
                    },
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit the form if confirmed
                    }
                });
            });
        });
    });
</script>
@endsection
