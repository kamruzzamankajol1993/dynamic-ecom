@extends('admin.master.master')
@section('title', 'Edit Main Slider')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Edit Main Slider</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('main-slider.update', $mainSlider->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $mainSlider->title) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Slider Image (Optional)</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="{{ asset('/') }}public/{{$mainSlider->image}}" alt="{{ $mainSlider->title }}" width="200">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                             <label class="form-label">Status</label>
                             <select name="status" class="form-control">
                                 <option value="1" {{ old('status', $mainSlider->status) == 1 ? 'selected' : '' }}>Active</option>
                                 <option value="0" {{ old('status', $mainSlider->status) == 0 ? 'selected' : '' }}>Inactive</option>
                             </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Slider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
