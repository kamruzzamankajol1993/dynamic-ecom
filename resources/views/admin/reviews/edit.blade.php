@extends('admin.master.master')
@section('title', 'Edit Review')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Edit Review</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                
                <form action="{{ route('review.update', $review->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <strong>Product:</strong> {{ $review->product->name ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        {{-- Changed 'customer' to 'user' --}}
                        <strong>Customer:</strong> {{ $review->user->name ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Rating:</strong> 
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa {{ $i <= $review->rating ? 'fa-star text-warning' : 'fa-star-o text-muted' }}"></i>
                        @endfor
                    </div>
                    <div class="mb-3">
                        {{-- Changed 'comment' to 'description' --}}
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $review->description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        {{-- Changed 'published' to 'is_approved' --}}
                        <select name="is_approved" class="form-select" required>
                            <option value="1" @selected(old('is_approved', $review->is_approved) == 1)>Approved</option>
                            <option value="0" @selected(old('is_approved', $review->is_approved) == 0)>Pending</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="images" class="form-label">Add New Images</label>
                        <input type="file" name="images[]" id="images" class="form-control" multiple>
                        <div class="form-text">You can upload multiple images (jpg, png, webp, gif). Max 2MB each.</div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Review</button>
                    </div>
                </form>

                @if($review->images->isNotEmpty())
                <hr>
                <div class="mt-4">
                    <h4>Review Images</h4>
                    <div class="d-flex flex-wrap gap-3" id="review-images-container">
                        @foreach($review->images as $image)

                         @php
                                    // Check if the image specifically was uploaded by admin to use asset()
                                    // Otherwise use the custom path logic
                                    $imageUrl = ($image->is_upload_from_admin == 1) 
                                        ? asset('public/'.$image->image_path) 
                                        : $ins_url . 'public/' . $image->image_path;
                                @endphp
                                
                            <div class="position-relative" id="review-image-{{ $image->id }}">
                                {{-- Changed '$image->image' to '$image->image_path' --}}
                                <img src="{{ $imageUrl }}" alt="Review Image" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                                <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-image-btn"
                                        data-id="{{ $image->id }}"
                                        title="Delete Image">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('.delete-image-btn').on('click', function(e) {
        e.preventDefault();
        const imageId = $(this).data('id');
        
        let urlTemplate = "{{ route('review.image.destroy', ['image' => ':id']) }}";
        const url = urlTemplate.replace(':id', imageId);

        const csrfToken = '{{ csrf_token() }}';

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this image?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`#review-image-${imageId}`).remove();
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection