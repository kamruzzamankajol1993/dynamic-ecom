@extends('admin.master.master')

@section('title')
Manage About Us Page | {{ $ins_name ?? 'Your App Name' }}
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
    .image-preview-container {
        margin-top: 15px;
    }
    #image-preview {
        max-width: 250px;
        border-radius: 0.5rem;
        border: 1px solid #ddd;
        padding: 5px;
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
            <h3 class="mb-0">{{ isset($aboutUs) ? 'Edit About Us Content' : 'Create About Us Content' }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($aboutUs) ? route('aboutUs.update', $aboutUs->id) : route('aboutUs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($aboutUs))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="image" class="form-label fs-5">Image</label>
                            <input type="file" class="form-control" id="image" name="image" {{ !isset($aboutUs) ? 'required' : '' }}>
                             <div class="image-preview-container">
                                <img id="image-preview" src="{{ isset($aboutUs) ? asset($aboutUs->image) : '#' }}" alt="Image Preview" style="display: {{ isset($aboutUs) ? 'block' : 'none' }};"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="mb-4">
                            <label for="youtube_video_link" class="form-label fs-5">YouTube Video Link</label>
                            <input type="url" class="form-control" id="youtube_video_link" name="youtube_video_link" placeholder="link" value="{{ old('youtube_video_link', $aboutUs->youtube_video_link ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="des" class="form-label fs-5">Description</label>
                    <textarea class="form-control summernote" id="des" name="des" rows="10">{{ old('des', $aboutUs->des ?? '') }}</textarea>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">{{ isset($aboutUs) ? 'Update Content' : 'Save Content' }}</button>
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
        // Initialize Summernote
        $('.summernote').summernote({
            placeholder: 'Enter content here...',
            tabsize: 2,
            height: 250,
        });

        // Image preview script
        $('#image').on('change', function(event) {
            const preview = $('#image-preview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                    preview.css('display', 'block');
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endsection
