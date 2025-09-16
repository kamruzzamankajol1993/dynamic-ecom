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
                                <label for="row1_image" class="form-label">Custom Image (410x530)</label>
                                <input type="file" name="row1_image" id="row1_image" class="form-control">
                            </div>

                            @if(optional($row1)->image)
                            <div class="mb-3">
                                <label class="form-label">Current Image:</label>
                                <div>
                                    <img src="{{ $row1->image }}?v={{ time() }}" alt="Row 1 Image" height="150">
                                   
                                </div>
                            </div>
                            @endif
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
                                <label for="row2_image" class="form-label">Custom Image (410x530)</label>
                                <input type="file" name="row2_image" id="row2_image" class="form-control">
                            </div>

                            @if(optional($row2)->image)
                            <div class="mb-3">
                                <label class="form-label">Current Image:</label>
                                <div>
                                    <img src="{{ $row2->image }}?v={{ time() }}" alt="Row 2 Image" height="150">
                                   
                                </div>
                            </div>
                            @endif
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

    /**
     * This function synchronizes two dropdowns.
     * It disables the option in the target dropdown that is selected in the source dropdown.
     */
    function syncDropdowns(source, target) {
        const selectedValue = source.val();
        
        // First, enable all options in the target dropdown
        target.find('option').prop('disabled', false);

        // If the source dropdown has a selection (and it's not the "-- None --" option),
        // disable that corresponding option in the target dropdown.
        if (selectedValue) {
            target.find('option[value="' + selectedValue + '"]').prop('disabled', true);
        }
    }

    // Add event listeners that trigger the sync function when a selection changes
    row1Select.on('change', function() {
        syncDropdowns(row1Select, row2Select);
    });

    row2Select.on('change', function() {
        syncDropdowns(row2Select, row1Select);
    });

    // Run the sync functions once on page load to set the initial state correctly
    syncDropdowns(row1Select, row2Select);
    syncDropdowns(row2Select, row1Select);
});
</script>
@endsection