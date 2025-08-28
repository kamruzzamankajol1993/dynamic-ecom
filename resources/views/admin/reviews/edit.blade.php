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
                <form action="{{ route('review.update', $review->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <strong>Product:</strong> {{ $review->product->name ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Customer:</strong> {{ $review->customer->name ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Rating:</strong> 
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa {{ $i <= $review->rating ? 'fa-star text-warning' : 'fa-star-o text-muted' }}"></i>
                        @endfor
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea name="comment" id="comment" class="form-control" rows="4">{{ old('comment', $review->comment) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="published" class="form-select" required>
                            <option value="1" @selected(old('published', $review->published) == 1)>Published</option>
                            <option value="0" @selected(old('published', $review->published) == 0)>Pending</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection