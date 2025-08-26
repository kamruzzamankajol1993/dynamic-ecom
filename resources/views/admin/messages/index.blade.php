@extends('admin.master.master')

@section('title')
Message Inbox | {{ $ins_name ?? 'Your App Name' }}
@endsection

@section('css')
<style>
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
    }
    .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem 2.5rem;
        font-size: 1.35rem;
        font-weight: 700;
    }
    .table thead th {
        background-color: #f8f9fa;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .action-buttons .btn {
        margin-right: 5px;
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded-3">
        <div class="card-header text-center">
            <h3 class="mb-0">Received Messages</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Received At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $key => $message)
                        <tr>
                            <td>{{ $messages->firstItem() + $key }}</td>
                            <td>{{ $message->name }}</td>
                            <td>{{ $message->email }}</td>
                            <td>{{ $message->phone }}</td>
                            <td>{{ $message->created_at->format('d M, Y h:i A') }}</td>
                            <td class="action-buttons">
                                <button type="button" class="btn btn-sm btn-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#messageModal"
                                        data-bs-name="{{ $message->name }}"
                                        data-bs-message="{{ $message->msg }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <form action="{{ route('message.destroy', $message->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No messages found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $messages->links() }}
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Message from...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="modal-body-content" style="white-space: pre-wrap;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
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
                event.preventDefault();
                const currentForm = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        currentForm.submit();
                    }
                });
            });
        });

        // Bootstrap Modal for Viewing Message
        const messageModal = document.getElementById('messageModal');
        if (messageModal) {
            messageModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const messageContent = button.getAttribute('data-bs-message');
                const senderName = button.getAttribute('data-bs-name');
                
                const modalTitle = messageModal.querySelector('.modal-title');
                const modalBody = messageModal.querySelector('.modal-body-content');

                modalTitle.textContent = 'Message from ' + senderName;
                modalBody.textContent = messageContent;
            });
        }
    });
</script>
@endsection
