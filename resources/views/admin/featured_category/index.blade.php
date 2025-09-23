@extends('admin.master.master')
@section('title', 'Featured Section Control')

@section('body')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">Featured Section Control</h2>

        <div class="card">
            <div class="card-body">
                @include('flash_message')
                <form action="{{ route('featured-category.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        {{-- First Row Section --}}
                        <div class="col-md-6">
                            <h5 class="mb-3 border-bottom pb-2">First Row Section</h5>
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
                            
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="first_row_status" value="1" id="first_row_status" {{ $firstRowStatus ? 'checked' : '' }}>
                                    <label class="form-check-label" for="first_row_status">Show this row on homepage</label>
                                </div>
                            </div>
                        </div>

                        {{-- Second Row Section --}}
                        <div class="col-md-6">
                            <h5 class="mb-3 border-bottom pb-2">Second Row Section</h5>
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

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="second_row_status" value="1" id="second_row_status" {{ $secondRowStatus ? 'checked' : '' }}>
                                    <label class="form-check-label" for="second_row_status">Show this row on homepage</label>
                                </div>
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
        
        $(targetSelect).find('option').prop('disabled', false);

        if (selectedValue) {
            $(targetSelect).find('option[value="' + selectedValue + '"]').prop('disabled', true);
        }
    }
    
    const row1Select = $('#firstRowSelect');
    const row2Select = $('#secondRowSelect');

    syncSelects(row1Select, row2Select);
    syncSelects(row2Select, row1Select);

    row1Select.on('change', function() {
        syncSelects(this, row2Select);
    });

    row2Select.on('change', function() {
        syncSelects(this, row1Select);
    });
});
</script>
@endsection