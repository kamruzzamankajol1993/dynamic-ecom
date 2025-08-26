@extends('admin.master.master')

@section('title')
Social Link Management | {{ $ins_name ?? 'Your App Name' }}
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
    .btn-create-news {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 0.4rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-create-news:hover {
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

<main class="main-content">
    <div class="container-fluid">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <!-- Breadcrumb Start -->
        <nav aria-label="breadcrumb">
           
        </nav>
        <!-- Breadcrumb End -->

        <a href="{{ route('socialLink.create') }}" class="btn btn-create-news d-inline-flex align-items-center">
            <i class="fas fa-plus-circle me-2"></i> Add New Social Link
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
            Social Link List
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Platform</th>
                            <th scope="col">Link</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($socialLinks as $key => $link)
                        <tr>
                            <th scope="row">{{ $socialLinks->firstItem() + $key }}</th>
                            <td>{{ $link->title }}</td>
                            <td><a href="{{ $link->link }}" target="_blank">{{ $link->link }}</a></td>
                            <td class="action-buttons">
                                <a href="{{ route('socialLink.edit', $link->id) }}" class="btn btn-sm btn-success"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('socialLink.destroy', $link->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No social links found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
         <div class="card-footer">
            {{ $socialLinks->links() }}
        </div>
    </div>
</main>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // SweetAlert for Delete Confirmation
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const currentForm = this;

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
                        currentForm.submit(); // Submit the form if confirmed
                    }
                });
            });
        });
    });
</script>
@endsection
