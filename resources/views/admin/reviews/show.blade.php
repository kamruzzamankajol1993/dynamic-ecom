@extends('admin.master.master')
@section('title', 'Review Details')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Review Details</h2>
            <a href="{{ route('review.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Product:</strong> {{ $review->product->name ?? 'N/A' }}</li>
                    
                    <li class="list-group-item"><strong>Customer:</strong> {{ $review->user->name ?? 'N/A' }}</li>
                    
                    <li class="list-group-item"><strong>Date:</strong> {{ $review->created_at->format('d M, Y h:i A') }}</li>
                    
                    <li class="list-group-item"><strong>Rating:</strong> 
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa {{ $i <= $review->rating ? 'fa-star text-warning' : 'fa-star-o text-muted' }}"></i>
                        @endfor
                    </li>
                    
                    {{-- Status Badge --}}
                    <li class="list-group-item"><strong>Status:</strong>
                        @if($review->is_approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </li>

                    {{-- NEW: Source Badge --}}
                    <li class="list-group-item"><strong>Source:</strong>
                        @if($review->is_upload_from_admin)
                            <span class="badge bg-info text-white">Admin Upload</span>
                        @else
                            <span class="badge bg-secondary">Customer</span>
                        @endif
                    </li>

                    <li class="list-group-item">
                        <strong>Description:</strong>
                        <p class="mt-2 text-muted">{{ $review->description ?? 'No description provided.' }}</p>
                    </li>
                    
                    {{-- Images Section --}}
                    @if($review->images->isNotEmpty())
                    <li class="list-group-item">
                        <strong>Images:</strong>
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @foreach($review->images as $image)
                                @php
                                    // Check if the image specifically was uploaded by admin to use asset()
                                    // Otherwise use the custom path logic
                                    $imageUrl = ($image->is_upload_from_admin == 1) 
                                        ? asset('public/'.$image->image_path) 
                                        : $ins_url . 'public/' . $image->image_path;
                                @endphp
                                
                                <a href="{{ $imageUrl }}" data-lightbox="review-images">
                                    <img src="{{ $imageUrl }}" alt="Review Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</main>
@endsection