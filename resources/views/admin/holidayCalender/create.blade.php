@extends('admin.master.master')

@section('title')

Holiday Calender Management | {{ $ins_name }}

@endsection


@section('css')
    <!-- You might already have some CSS for form elements -->
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection


@section('body')

<div class="dashboard-body">

    <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
        <!-- Breadcrumb Start -->
        <div class="breadcrumb mb-24">
            <ul class="flex-align gap-4">
                <li><a href="{{route('home')}}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                <li><span class="text-main-600 fw-normal text-15">Holiday Calender Management</span></li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb End -->
    <div class="card overflow-hidden">
        <div class="card-header">
            Add New Holiday Calender
        </div>
        <div class="card-body">
            @include('flash_message')

            <form id="holidayForm" action="{{ route('holidayCalender.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="aircraft_id" class="form-label">Aircraft Model</label>
                    <select class="form-select" required id="aircraft_id" name="aircraft_id">
                        <option value="">-- Select Aircraft Model--</option>
                        <option value="all">All</option>
                        @foreach($aircraftModelType as $aircraft)
                            <option value="{{ $aircraft->id }}">{{ $aircraft->name }}</option>
                        @endforeach
                    </select>
                    @error('aircraft_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div id="holiday-entries-container">
                    <!-- Initial Holiday Entry -->
                    <div class="holiday-entry border border-gray-300 p-4 mb-4 rounded-lg">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="holiday_date_0" class="form-label">Holiday Date</label>
                                <input type="text" required class="form-control holiday-datepicker" id="holiday_date_0" name="holiday_dates[]" placeholder="Holiday Date">
                                @error('holiday_dates.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="holiday_charge_0" class="form-label">Holiday Charge</label>
                                <input type="number" required step="0.01" class="form-control" id="holiday_charge_0" name="holiday_charges[]" placeholder="Holiday Charge">
                                @error('holiday_charges.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-more-holiday" class="btn btn-secondary mb-4">Add More Holiday</button>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Save Holiday Calender</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection


@section('script')
    
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            // Initialize Flatpickr on the initial input
            flatpickr(".holiday-datepicker", {
                dateFormat: "Y-m-d", // Flatpickr format: YYYY-MM-DD
            });

            let holidayEntryCount = 1;

            $('#add-more-holiday').on('click', function() {
                const container = $('#holiday-entries-container');
                const newEntry = `
                    <div class="holiday-entry border border-gray-300 p-4 mb-4 rounded-lg">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="holiday_date_${holidayEntryCount}" class="form-label">Holiday Date</label>
                                <input type="text" required class="form-control holiday-datepicker" id="holiday_date_${holidayEntryCount}" name="holiday_dates[]" placeholder="Holiday Date">
                            </div>
                            <div class="col-md-6">
                                <label for="holiday_charge_${holidayEntryCount}" class="form-label">Holiday Charge</label>
                                <input type="number" required step="0.01" class="form-control" id="holiday_charge_${holidayEntryCount}" name="holiday_charges[]" placeholder="Holiday Charge">
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <button type="button" class="btn btn-danger btn-sm remove-holiday-entry">Remove</button>
                        </div>
                    </div>
                `;
                container.append(newEntry);

                // Initialize Flatpickr on the newly added input
                flatpickr(`#holiday_date_${holidayEntryCount}`, {
                    dateFormat: "Y-m-d",
                });

                holidayEntryCount++;
            });

            // Remove button functionality (event delegation)
            $(document).on('click', '.remove-holiday-entry', function() {
                $(this).closest('.holiday-entry').remove();
            });
        });
    </script>
@endsection
