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
                    {{-- Changed 'customer' to 'user' --}}
                    <li class="list-group-item"><strong>Customer:</strong> {{ $review->user->name ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Date:</strong> {{ $review->created_at->format('d M, Y h:i A') }}</li>
                    <li class="list-group-item"><strong>Rating:</strong> 
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa {{ $i <= $review->rating ? 'fa-star text-warning' : 'fa-star-o text-muted' }}"></i>
                        @endfor
                    </li>
                    <li class="list-group-item"><strong>Status:</strong>
                        {{-- Changed 'published' to 'is_approved' --}}
                        @if($review->is_approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </li>
                    <li class="list-group-item">
                        {{-- Changed 'Comment' to 'Description' --}}
                        <strong>Description:</strong>
                        <p class="mt-2 text-muted">{{ $review->description ?? 'No description provided.' }}</p>
                    </li>
                    @if($review->images->isNotEmpty())
                    <li class="list-group-item">
                        <strong>Images:</strong>
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @foreach($review->images as $image)
                                {{-- Changed '$image->image' to '$image->image_path' --}}
                                <a href="{{ $ins_url .'public/'. $image->image_path }}" data-lightbox="review-images">
                                    <img src="{{ $ins_url .'public/'. $image->image_path }}" alt="Review Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
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