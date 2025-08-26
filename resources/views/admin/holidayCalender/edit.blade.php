@extends('admin.master.master')

@section('title')

Edit Holiday Calender | {{ $ins_name }}

@endsection


@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .holiday-table th, .holiday-table td {
            padding: 8px;
            border: 1px solid #dee2e6; /* Bootstrap table border color */
        }
        .holiday-table thead th {
            background-color: #f8f9fa; /* Light grey background for header */
        }
    </style>
@endsection


@section('body')

<div class="dashboard-body">

    <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
        <div class="breadcrumb mb-24">
            <ul class="flex-align gap-4">
                <li><a href="{{route('home')}}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
                <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
                <li><span class="text-main-600 fw-normal text-15">Edit Holiday Calender</span></li>
            </ul>
        </div>
    </div>
    <div class="card overflow-hidden">
        <div class="card-header">
            Edit Holiday Calender
        </div>
        <div class="card-body">
            @include('flash_message')

            <form id="holidayEditForm" action="{{ route('holidayCalender.update',$airCraftId) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Use PUT method for update --}}

                <div class="mb-3">
                    <label for="aircraft_model_name" class="form-label">Aircraft Model</label>
                    {{-- Display aircraft model name once --}}
                    <input type="text" class="form-control" id="aircraft_model_name" value="{{ $aircraftHolidayOne->name }}" disabled>
                    {{-- Hidden input to send aircraft_id --}}
                    <input type="hidden" name="aircraft_id" value="{{ $airCraftId }}">
                </div>

                <div class="mb-4">
                    <label class="form-label d-block mb-3">Holiday Dates and Charges</label>
                    <div class="table-responsive">
                        <table class="table table-bordered holiday-table w-100">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Holiday Date</th>
                                    <th style="width: 40%;">Holiday Charge (৳)</th>
                                    <th style="width: 10%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="holiday-entries-container">
                                @forelse($aircraftHoliday as $key => $holiday)
                                    <tr class="holiday-entry">
                                        <td>
                                            <input type="text" required class="form-control holiday-datepicker" id="holiday_date_{{ $key }}" name="holiday_dates[]" placeholder="Holiday Date" value="{{ $holiday->holiday_date }}">
                                            @error('holiday_dates.' . $key)
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" required step="0.01" class="form-control" id="holiday_charge_{{ $key }}" name="holiday_charges[]" placeholder="Holiday Charge" value="{{ $holiday->holiday_charge }}">
                                            @error('holiday_charges.' . $key)
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-holiday-entry">Remove</button>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Optionally, add a placeholder row if no holidays exist --}}
                                    <tr class="holiday-entry">
                                        <td>
                                            <input type="text" required class="form-control holiday-datepicker" name="holiday_dates[]" placeholder="Holiday Date">
                                        </td>
                                        <td>
                                            <input type="number" required step="0.01" class="form-control" name="holiday_charges[]" placeholder="Holiday Charge">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-holiday-entry">Remove</button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="button" id="add-more-holiday" class="btn btn-secondary mb-4">Add More Holiday</button>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Update Holiday Calender</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection


@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            // Function to initialize Flatpickr on new inputs
            function initializeFlatpickr(selector) {
                flatpickr(selector, {
                    dateFormat: "Y-m-d",
                });
            }

            // Initialize Flatpickr on all existing datepickers
            initializeFlatpickr(".holiday-datepicker");

            let holidayEntryCount = {{ count($aircraftHoliday) > 0 ? count($aircraftHoliday) : 0 }};

            $('#add-more-holiday').on('click', function() {
                const container = $('#holiday-entries-container');
                const newEntry = `
                    <tr class="holiday-entry">
                        <td>
                            <input type="text" required class="form-control holiday-datepicker" id="holiday_date_${holidayEntryCount}" name="holiday_dates[]" placeholder="Holiday Date">
                        </td>
                        <td>
                            <input type="number" required step="0.01" class="form-control" id="holiday_charge_${holidayEntryCount}" name="holiday_charges[]" placeholder="Holiday Charge">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-holiday-entry">Remove</button>
                        </td>
                    </tr>
                `;
                container.append(newEntry);

                // Initialize Flatpickr on the newly added input
                initializeFlatpickr(`#holiday_date_${holidayEntryCount}`);

                holidayEntryCount++;
            });

            // Remove button functionality (event delegation)
            $(document).on('click', '.remove-holiday-entry', function() {
                // If there's only one row left, prevent removal or make it a "clear" action
                if ($('#holiday-entries-container .holiday-entry').length > 1) {
                    $(this).closest('.holiday-entry').remove();
                } else {
                    // Optionally, clear the fields if only one row remains instead of removing it
                    const remainingRow = $(this).closest('.holiday-entry');
                    remainingRow.find('.holiday-datepicker').val('');
                    remainingRow.find('input[name="holiday_charges[]"]').val('');
                    alert('You must have at least one holiday entry. Fields cleared.');
                }
            });
        });
    </script>
@endsection
