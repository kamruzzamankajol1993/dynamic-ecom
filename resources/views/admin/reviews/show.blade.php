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
                    <li class="list-group-item"><strong>Customer:</strong> {{ $review->customer->name ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Date:</strong> {{ $review->created_at->format('d M, Y h:i A') }}</li>
                    <li class="list-group-item"><strong>Rating:</strong> 
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa {{ $i <= $review->rating ? 'fa-star text-warning' : 'fa-star-o text-muted' }}"></i>
                        @endfor
                    </li>
                    <li class="list-group-item"><strong>Status:</strong> 
                        @if($review->published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <strong>Comment:</strong>
                        <p class="mt-2 text-muted">{{ $review->comment ?? 'No comment provided.' }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>
@endsection