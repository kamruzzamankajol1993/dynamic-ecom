@extends('admin.master.master')
@section('title', 'Featured Section Control')

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Featured Section Control</h2>

        <div class="card">
            <div class="card-body">
                @include('flash_message')
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('featured-category.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        {{-- First Row Section --}}
                        <div class="col-md-6">
                            <h5 class="mb-3">First Row Section</h5>
                            <div class="mb-3">
                                <label for="firstRowSelect" class="form-label">Select a type for the first row.</label>
                                <select class="form-select" id="firstRowSelect" name="first_row">
                                    <option value="">-- None --</option>
                                    @foreach($options as $key => $value)
                                        <option value="{{ $key }}" 
                                            @if($key == $firstRowSetting) selected @endif>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Second Row Section --}}
                        <div class="col-md-6">
                            <h5 class="mb-3">Second Row Section</h5>
                            <div class="mb-3">
                                <label for="secondRowSelect" class="form-label">Select a type for the second row.</label>
                                <select class="form-select" id="secondRowSelect" name="second_row">
                                     <option value="">-- None --</option>
                                     @foreach($options as $key => $value)
                                        <option value="{{ $key }}"
                                            @if($key == $secondRowSetting) selected @endif>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
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
    function syncSelects(sourceSelect, targetSelect) {
        const selectedValue = $(sourceSelect).val();
        
        // First, enable all options in the target dropdown
        $(targetSelect).find('option').prop('disabled', false);

        // If a value was selected in the source dropdown, disable that option in the target
        if (selectedValue) {
            $(targetSelect).find('option[value="' + selectedValue + '"]').prop('disabled', true);
        }
    }

    // Run the sync function when the page loads to set the initial state
    syncSelects('#firstRowSelect', '#secondRowSelect');
    syncSelects('#secondRowSelect', '#firstRowSelect');

    // Add event listeners to sync whenever a selection changes
    $('#firstRowSelect').on('change', function() {
        syncSelects(this, '#secondRowSelect');
    });

    $('#secondRowSelect').on('change', function() {
        syncSelects(this, '#firstRowSelect');
    });
});
</script>
@endsection