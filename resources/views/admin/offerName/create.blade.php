@extends('admin.master.master')
@section('title', 'Create Offer Name')
@section('css')
    {{-- Add Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Create New Offer Name</h2>
        <form action="{{ route('bundle-offer.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Offer Details</h5>
                    <div class="mb-3">
                        <label class="form-label">Offer Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Offer Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                {{-- Changed input type to text and added a class for the datepicker --}}
                                <input type="text" name="startdate" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                {{-- Changed input type to text and added a class for the datepicker --}}
                                <input type="text" name="enddate" class="form-control datepicker">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Offer Image</label>
                        <input class="form-control" type="file" name="image" id="formFile">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">Save Offer</button>
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
