@extends('admin.master.master')
@section('title', 'Footer Banner')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Manage Footer Banner</h2>
        </div>
        @include('flash_message')
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('footer-banner.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="image" class="form-label">Upload New Banner Image*</label>
                                <input type="file" name="image" id="image" class="form-control" required>
                                <small class="form-text text-muted">Recommended dimensions: 1200px width and 400px height.</small>
                            </div>
                            
                            @if($bannerImage)
                            <div class="mb-3">
                                <label class="form-label">Current Banner</label>
                                <div>
                                    <img src="{{ asset('public/'.$bannerImage) }}" alt="Current Footer Banner" class="img-fluid img-thumbnail" style="max-height: 100px;">
                                </div>
                            </div>
                            @endif

                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection