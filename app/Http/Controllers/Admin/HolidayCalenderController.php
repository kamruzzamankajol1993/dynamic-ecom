<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use App\Exports\FlightTypeExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use App\Models\Aircraft;
use App\Models\AircraftModeltype; // Corrected model name
use App\Models\AircraftAvailabiity;
use App\Models\HolidayCalender;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // For date handling
use Illuminate\Support\Facades\DB; // For database transactions
class HolidayCalenderController extends Controller
{
     function __construct()
    {
         $this->middleware('permission:holidayCalenderView|holidayCalenderAdd|holidayCalenderUpdate|holidayCalenderDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:holidayCalenderAdd', ['only' => ['create','store']]);
         $this->middleware('permission:holidayCalenderUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:holidayCalenderDelete', ['only' => ['destroy']]);
    }

    public function data(Request $request)
    {
        $query = HolidayCalender::query();

        // Eager load the AircraftModeltype relationship
        $query->with('aircraftModelType'); // Assuming you have this relationship defined in HolidayCalender model

        // Search by holiday_date or holiday_charge
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('holiday_date', 'like', "%{$searchTerm}%")
                  ->orWhere('holiday_charge', 'like', "%{$searchTerm}%");
            })->orWhereHas('aircraftModelType', function ($q) use ($searchTerm) { // Search by aircraft model name
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        // Sorting
        // For simplicity with grouped data, we'll sort by aircraft model name first, then holiday date
        $query->join('aircraft_modeltypes', 'holiday_calenders.aircraft_model_id', '=', 'aircraft_modeltypes.id')
              ->select('holiday_calenders.*', 'aircraft_modeltypes.name as aircraft_model_name')
              ->orderBy('aircraft_model_name', 'asc')
              ->orderBy('holiday_date', 'asc');


        // Pagination
        $perPage = $request->get('perPage', 10);
        $paginated = $query->paginate($perPage);

        // Group the data by aircraft model
        $groupedData = $paginated->getCollection()->groupBy('aircraft_model_id')->map(function ($holidays, $aircraftModelId) {
            $aircraftModelName = $holidays->first()->aircraft_model_name; // Get name from first holiday in group
            return [
                'aircraft_model_id' => $aircraftModelId,
                'aircraft_model_name' => $aircraftModelName,
                'holidays' => $holidays->map(function ($holiday) {
                    return [
                        'id' => $holiday->id,
                        'holiday_date' => Carbon::parse($holiday->holiday_date)->format('d-m-Y'),
                        'holiday_charge' => $holiday->holiday_charge,
                    ];
                })->values()->all(), // Ensure holidays are a simple array
            ];
        })->values()->all(); // Ensure groupedData is a simple array

        return response()->json([
            'data' => $groupedData, // Return grouped data
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'can_edit' => Auth::user()->can('holidayCalenderUpdate'),
            'can_show' => Auth::user()->can('holidayCalenderView'),
            'can_delete' => Auth::user()->can('holidayCalenderDelete'),
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        // Assuming CommonController::addToLog exists and is configured
        // CommonController::addToLog('holidayCalender page view');

        $data = HolidayCalender::latest()->get(); // This data is for initial load if not using AJAX for the whole table


        return view('admin.holidayCalender.index',compact('data'));
    }

    public function create(): View
    {
        $ins_name = 'Your Company Name'; // Replace with actual logic to get your company name
        $aircraftModelType = AircraftModeltype::latest()->get();
        return view('admin.holidayCalender.create',compact('aircraftModelType', 'ins_name'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Base validation for fields that are always required
        $baseValidator = Validator::make($request->all(), [
            'aircraft_id' => 'required', // Just ensure it's present
            'holiday_dates' => 'required|array|min:1',
            'holiday_dates.*' => 'required|date_format:Y-m-d',
            'holiday_charges' => 'required|array|min:1',
            'holiday_charges.*' => 'required|numeric|min:0.01',
        ]);

        if ($baseValidator->fails()) {
            return redirect()->back()
                        ->withErrors($baseValidator)
                        ->withInput();
        }

        // Get common data
        $selectedAircraft = $request->input('aircraft_id');
        $holidayDates = $request->input('holiday_dates');
        $holidayCharges = $request->input('holiday_charges');

        // Logic for handling "All" or a single aircraft model
        if ($selectedAircraft === 'all') {
            // Get all aircraft model IDs
            $allAircraftIds = AircraftModeltype::pluck('id')->all();

            foreach ($allAircraftIds as $aircraftId) {
                foreach ($holidayDates as $key => $date) {
                    HolidayCalender::create([
                        'aircraft_model_id' => $aircraftId,
                        'holiday_date' => Carbon::parse($date)->toDateString(),
                        'holiday_charge' => $holidayCharges[$key],
                    ]);
                }
            }
        } else {
            // If a single aircraft is chosen, validate its existence
            $singleAircraftValidator = Validator::make($request->all(), [
                'aircraft_id' => 'exists:aircraft_modeltypes,id',
            ]);

            if ($singleAircraftValidator->fails()) {
                return redirect()->back()
                            ->withErrors($singleAircraftValidator)
                            ->withInput();
            }
            
            // Loop through and save for the single selected aircraft
            foreach ($holidayDates as $key => $date) {
                HolidayCalender::create([
                    'aircraft_model_id' => $selectedAircraft,
                    'holiday_date' => Carbon::parse($date)->toDateString(),
                    'holiday_charge' => $holidayCharges[$key],
                ]);
            }
        }

        // Redirect with a success message
        return redirect()->route('holidayCalender.index')->with('success', 'Holiday calendars added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $aircraft_model_id // Changed parameter name to match route binding
     * @return \Illuminate\Http\Response
     */
    public function edit(string $aircraft_model_id): View
    {
        // Fetch the aircraft model and its associated holidays
        $aircraftHolidayOne = AircraftModeltype::with('holidays')->findOrFail($aircraft_model_id);
        $aircraftModelType = AircraftModeltype::all(); // For the dropdown (though disabled in edit)
      

        $aircraftHoliday = DB::table('aircraft_modeltypes')
    ->select('aircraft_modeltypes.*','holiday_calenders.*','holiday_calenders.id as holidayId')
    ->join('holiday_calenders', 'holiday_calenders.aircraft_model_id', '=', 'aircraft_modeltypes.id')
    ->where('aircraft_modeltypes.id', $aircraft_model_id)
    ->get();

     $airCraftId = $aircraft_model_id;

        return view('admin.holidayCalender.edit', compact('airCraftId','aircraftHoliday', 'aircraftModelType', 'aircraftHolidayOne'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $aircraft_model_id // Changed parameter name to match route binding
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $aircraft_model_id): RedirectResponse
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'aircraft_id' => 'required|exists:aircraft_modeltypes,id', // Ensure aircraft_modeltypes is your table name
            'holiday_dates' => 'required|array',
            'holiday_dates.*' => 'required|date_format:Y-m-d',
            'holiday_charges' => 'required|array',
            'holiday_charges.*' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        // Get validated data
        $aircraftId = $request->input('aircraft_id');
        $holidayDates = $request->input('holiday_dates');
        $holidayCharges = $request->input('holiday_charges');

        DB::transaction(function () use ($aircraftId, $holidayDates, $holidayCharges) {
            // Delete all existing holidays for this aircraft model
            HolidayCalender::where('aircraft_model_id', $aircraftId)->delete();

            // Re-create new holiday entries based on the submitted data
            foreach ($holidayDates as $key => $date) {
                HolidayCalender::create([
                    'aircraft_model_id' => $aircraftId,
                    'holiday_date' => Carbon::parse($date)->toDateString(),
                    'holiday_charge' => $holidayCharges[$key],
                ]);
            }
        });

        // Redirect with a success message
        return redirect()->route('holidayCalender.index')->with('success', 'Holiday calendars updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $aircraft_model_id)
    {
        // This method would typically delete a single holiday entry.
        // If you want to delete all holidays for an aircraft model, you might adjust this.
        HolidayCalender::where('aircraft_model_id', $aircraft_model_id)->delete();
 return response()->json(['success' => true, 'message' => 'Holiday entry deleted successfully!']);    }

     public function deleteSingleHoliday(string $holiday_id) // Changed return type from RedirectResponse
    {
        $holiday = HolidayCalender::find($holiday_id);
        if ($holiday) {
            $holiday->delete();
            return response()->json(['success' => true, 'message' => 'Holiday entry deleted successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Holiday entry not found!'], 404);
        }
    }
}
