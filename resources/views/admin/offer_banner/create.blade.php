@extends('admin.master.master')
@section('title', 'Create Offer Banner')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2>Create New Offer Banner</h2>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('offer-banner.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Banner Type</label>
                            <select name="banner_type" class="form-control" required>
                                <option value="">Select Banner Type</option>
                                @foreach($availableTypes as $type)
                                    <option value="{{ $type }}" {{ old('banner_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Banner Image</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
