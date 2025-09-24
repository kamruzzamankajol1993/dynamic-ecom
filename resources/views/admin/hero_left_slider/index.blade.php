@extends('admin.master.master')
@section('title', 'Hero Left Sliders')
@section('body')
<main class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="mb-0">Hero Left Sliders</h2>
            <a href="{{ route('hero-left-slider.create') }}" class="btn btn-dark"><i class="fas fa-plus"></i> Add New Slider</a>
        </div>
        @include('flash_message')
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Target Slug</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sliders as $slider)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><img src="{{ asset('public/'.$slider->image) }}" alt="Slider Image" height="50"></td>
                                <td>{{ $slider->title }}</td>
                                <td>{{ $slider->subtitle }}</td>
                                <td>{{ optional($slider->linkable)->slug ?? 'N/A' }}</td>
                                <td>
                                    @if($slider->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('hero-left-slider.edit', $slider->id) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> </a>
                                   <form action="{{ route('hero-left-slider.destroy', $slider->id) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm delete-btn">
        <i class="fas fa-trash-alt"></i>
    </button>
</form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Listen for click events on elements with the class 'delete-btn'
    $('.delete-btn').on('click', function(event) {
        // Prevent the form from submitting immediately
        event.preventDefault();

        // Find the closest form associated with the clicked button
        const form = $(this).closest('form');

        // Show the SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            // If the user clicks the "confirm" button
            if (result.isConfirmed) {
                // Submit the form
                form.submit();
            }
        });
    });
});
</script>
@endsection