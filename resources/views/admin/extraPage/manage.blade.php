@extends('admin.master.master')

@section('title')
Manage Extra Pages | {{ $ins_name ?? 'Your App Name' }}
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
        color: #343a40;
    }
    .note-editor.note-frame {
        border-radius: 0.5rem;
        border-color: #ced4da;
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
            <h3 class="mb-0">{{ isset($extraPage) ? 'Edit Extra Page Content' : 'Create Extra Page Content' }}</h3>
        </div>
        <div class="card-body">
            {{-- The form will dynamically point to store or update method --}}
            <form action="{{ isset($extraPage) ? route('extraPage.update', $extraPage->id) : route('extraPage.store') }}" method="POST">
                @csrf
                {{-- If it's an edit form, we need to spoof the PUT method --}}
                @if(isset($extraPage))
                    @method('PUT')
                @endif

                <div class="mb-4">
                    <label for="privacy_policy" class="form-label fs-5">Privacy Policy</label>
                    <textarea class="form-control summernote" id="privacy_policy" name="privacy_policy" rows="10">{{ old('privacy_policy', $extraPage->privacy_policy ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="term_condition" class="form-label fs-5">Terms & Conditions</label>
                    <textarea class="form-control summernote" id="term_condition" name="term_condition" rows="10">{{ old('term_condition', $extraPage->term_condition ?? '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="return_pollicy" class="form-label fs-5">Return Policy</label>
                    <textarea class="form-control summernote" id="return_pollicy" name="return_pollicy" rows="10">{{ old('return_pollicy', $extraPage->return_pollicy ?? '') }}</textarea>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">{{ isset($extraPage) ? 'Update Content' : 'Save Content' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
@endsection

@section('script')

<script>
    $(document).ready(function() {
        // Initialize Summernote for all textareas with the 'summernote' class
        $('.summernote').summernote({
            placeholder: 'Enter content here...',
            tabsize: 2,
            height: 250,
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
    });
</script>
@endsection
