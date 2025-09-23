@extends('admin.master.master')
@section('title', 'Homepage Sections Control')

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Homepage Sections Control</h2>

        <div class="card">
            <div class="card-body">
                @include('flash_message')
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <p class="mb-0">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('homepage-section.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        {{-- Row 1 Settings --}}
                        <div class="col-md-6">
                            <h5 class="mb-3 border-bottom pb-2">Row 1 Settings</h5>
                            <div class="mb-3">
                                <label for="row1_category_id" class="form-label">Category</label>
                                <select name="row1_category_id" id="row1_category_id" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}" {{ optional($row1)->category_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="row1_title" class="form-label">Title</label>
                                <input type="text" name="row1_title" id="row1_title" class="form-control" value="{{ optional($row1)->title }}">
                            </div>

                            <div class="mb-3">
                                <label for="row1_image" class="form-label">Custom Image (410px)x(530px)</label>
                                <input type="file" accept="image/webp" name="row1_image" id="row1_image" class="form-control">
                            </div>

                            @if(optional($row1)->image)
                            <div class="mb-3">
                                <label class="form-label">Current Image:</label>
                                <div>
                                    <img src="{{ asset($row1->image) }}?v={{ time() }}" alt="Row 1 Image" height="150">
                                </div>
                            </div>
                            @endif
                            
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="row1_status" value="1" id="row1_status" {{ (optional($row1)->status ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="row1_status">Show this row on homepage</label>
                                </div>
                            </div>
                        </div>

                        {{-- Row 2 Settings --}}
                        <div class="col-md-6">
                            <h5 class="mb-3 border-bottom pb-2">Row 2 Settings</h5>
                            <div class="mb-3">
                                <label for="row2_category_id" class="form-label">Category</label>
                                <select name="row2_category_id" id="row2_category_id" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}" {{ optional($row2)->category_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="row2_title" class="form-label">Title</label>
                                <input type="text" name="row2_title" id="row2_title" class="form-control" value="{{ optional($row2)->title }}">
                            </div>

                            <div class="mb-3">
                                <label for="row2_image" class="form-label">Custom Image (410px)x(530px)</label>
                                <input type="file" accept="image/webp" name="row2_image" id="row2_image" class="form-control">
                            </div>

                            @if(optional($row2)->image)
                            <div class="mb-3">
                                <label class="form-label">Current Image:</label>
                                <div>
                                    <img src="{{ asset($row2->image) }}?v={{ time() }}" alt="Row 2 Image" height="150">
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="row2_status" value="1" id="row2_status" {{ (optional($row2)->status ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="row2_status">Show this row on homepage</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="save-button" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
<script>
$(document).ready(function() {
    const row1Select = $('#row1_category_id');
    const row2Select = $('#row2_category_id');

    function syncDropdowns(source, target) {
        const selectedValue = source.val();
        target.find('option').prop('disabled', false);
        if (selectedValue) {
            target.find('option[value="' + selectedValue + '"]').prop('disabled', true);
        }
    }

    row1Select.on('change', function() {
        syncDropdowns(row1Select, row2Select);
    });

    row2Select.on('change', function() {
        syncDropdowns(row2Select, row1Select);
    });

    syncDropdowns(row1Select, row2Select);
    syncDropdowns(row2Select, row1Select);
});
</script>
@endsection