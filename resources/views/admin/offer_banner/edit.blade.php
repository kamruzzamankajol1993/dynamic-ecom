@extends('admin.master.master')
@section('title', 'Edit Offer Banner')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Edit Offer Banner: {{ $offerBanner->banner_type }}</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('offer-banner.update', $offerBanner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Banner Type</label>
                            <input type="text" class="form-control" value="{{ $offerBanner->banner_type }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Banner Image (Optional)</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                         <div class="col-md-6 mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="{{ asset($offerBanner->image) }}" alt="{{ $offerBanner->banner_type }}" width="150">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                             <label class="form-label">Status</label>
                             <select name="status" class="form-control">
                                 <option value="1" {{ old('status', $offerBanner->status) == 1 ? 'selected' : '' }}>Active</option>
                                 <option value="0" {{ old('status', $offerBanner->status) == 0 ? 'selected' : '' }}>Inactive</option>
                             </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
