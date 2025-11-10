@extends('admin.master.master')
@section('title', 'Edit Offer Name')
@section('css')
    {{-- Add Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Edit Offer Name</h2>
        <form action="{{ route('bundle-offer.update', $bundleOffer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Offer Details</h5>
                    <div class="mb-3">
                        <label class="form-label">Offer Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $bundleOffer->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Offer Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $bundleOffer->title }}" required>
                    </div>
                     <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                {{-- Changed input type to text and added datepicker class --}}
                                <input type="text" name="startdate" class="form-control datepicker" value="{{ $bundleOffer->startdate ? $bundleOffer->startdate->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                {{-- Changed input type to text and added datepicker class --}}
                                <input type="text" name="enddate" class="form-control datepicker" value="{{ $bundleOffer->enddate ? $bundleOffer->enddate->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Offer Image</label>
                        <input class="form-control" accept="image/webp" type="file" name="image" id="formFile">
                        @if($bundleOffer->image)
                            <img src="{{ asset('public/'.$bundleOffer->image) }}" alt="Offer Image" class="img-thumbnail mt-2" width="150">
                        @endif<br>
                         <small class="text-danger">image width: 660px and height: 350px , image type webp</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="status" @if($bundleOffer->status) checked @endif>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                    {{-- START: NEW FIELD --}}
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_free_delivery" value="1" id="is_free_delivery" @if($bundleOffer->is_free_delivery) checked @endif>
                        <label class="form-check-label" for="is_free_delivery">Free Delivery</label>
                    </div>
                    {{-- END: NEW FIELD --}}
                    <button type="submit" class="btn btn-primary mt-4">Update Offer</button>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection
@section('script')
    {{-- Add Flatpickr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize the datepicker
        flatpickr(".datepicker", {
            enableTime: false, // Set to false to only select the date
            dateFormat: "Y-m-d",
        });
    </script>
@endsection
