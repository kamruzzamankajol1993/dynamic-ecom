<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\SearchHistory; // Import your model
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketExport;
use App\Models\DefaultLocation;
use Mpdf\Mpdf;
use App\Models\User;
use App\Models\Customer;
use App\Models\AircraftModeltype;
use App\Models\AircraftAvailabiity;
use App\Models\FlightType;
use App\Models\Ticket;
use App\Models\OtherPassenger;
use App\Models\DistanceSegment;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
use App\Models\GeneralTicket; // Assuming you have a GeneralTicket model
class CustomerPersonalController extends Controller
{
    public function customerPersonalTicket ()
    {

        if (auth()->check()) {

            $user_id = Auth::user()->customer_id;
            $getAllCustomerInfo = Customer::where('id', $user_id)->orderBy('name','asc')->get();
            $getFlightType = FlightType::latest()->get();
            $notAvailableTodayIds = AircraftAvailabiity::where('date', Carbon::today()->toDateString())
                                               ->where('is_available', 0)
                                               ->pluck('id')
                                               ->all();

                                               
            $airCraftModeltype = AircraftAvailabiity::whereNotIn('id', $notAvailableTodayIds)
            ->groupBy('aircraft_id')->select('aircraft_id')->get();
            $defaultLocation = DefaultLocation::first();

            //dd(1);
            return view('front.customer.personal_ticket', compact('user_id','getAllCustomerInfo', 'getFlightType', 'airCraftModeltype', 'defaultLocation'));
        } else {
            return redirect()->route('front.loginRegister')->with('error', 'You must be logged in to view this page.');
        }

        
    }
 protected function generateUniqueTicketNumber()
    {
        do {
            // Generate a 6-character hexadecimal string
            $ticketNumber = bin2hex(random_bytes(3)); // 3 bytes = 6 hex chars
        } while (Ticket::where('ticket_number', $ticketNumber)->exists());

        return strtoupper($ticketNumber); // Return in uppercase
    }
    public function store(Request $request)
    {
        // 1. Generate a unique 6-digit hexadecimal ticket number
        $ticketNumber = $this->generateUniqueTicketNumber();

        // 2. Validate the incoming request data
        // Adjust validation rules as per your application's requirements
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'helicopter_model_id' => 'required|exists:aircraft_modeltypes,id',
            'flight_type_id' => 'required|exists:flight_types,id',
            'from_location_address' => 'required|string|max:255',
            'total_waiting_hour' => 'required|numeric|min:0',
            'fly_date' => 'required|date_format:Y-m-d',
            'capacity' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
            'distance_nm' => 'required|numeric|min:0',
            'fixed_price' => 'required|numeric|min:0',
            'per_nautical_mile_price' => 'required|numeric|min:0',
            'extra_per_nautical_mile_price' => 'required|numeric|min:0',
            'waiting_price_per_hour' => 'required|numeric|min:0',
            'landing_price' => 'required|numeric|min:0',
            'extra_price' => 'required|numeric|min:0',
            'flight_type_extra_price' => 'required|numeric|min:0',
            'flight_type_security_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'name.*' => 'nullable|string|max:255',
            'phone.*' => 'nullable|string|max:20',
            'nid.*' => 'nullable|string|max:50',
            'waypoint.*' => 'nullable|string|max:255',
        ]);


     
               $mainStatus = 0; // Assuming 1 means active for user dashboard
        

        // Use a database transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // 3. Create the main Ticket record
            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber, // Add the generated unique ticket number
                'customer_id' => $request->customer_id,
                'helicopter_model_id' => $request->helicopter_model_id,
                'flight_type_id' => $request->flight_type_id,
                'from_location_address' => $request->from_location_address,
                'total_waiting_hour' => $request->total_waiting_hour,
                'fly_date' => $request->fly_date,
                'capacity' => $request->capacity,
                'holiday_charge' => $request->holiday_charge,
                'distance_km' => $request->distance_km,
                'distance_nm' => $request->distance_nm,
                'fixed_price' => $request->fixed_price,
                'per_nautical_mile_price' => $request->per_nautical_mile_price,
                'extra_per_nautical_mile_price' => $request->extra_per_nautical_mile_price,
                'waiting_price_per_hour' => $request->waiting_price_per_hour,
                'landing_price' => $request->landing_price,
                'extra_price' => $request->extra_price,
                'flight_type_extra_price' => $request->flight_type_extra_price,
                'flight_type_security_price' => $request->flight_type_security_price,
                'total_price' => $request->total_price,
                'discount_amount' => $request->discount_amount,
                'grand_total_price' => $request->grand_total_price,
                'grand_total_price_usd' => $request->grand_total_price_usd,
                'status' => $mainStatus, // Add main_status based on request
            ]);

           

            $names = $request->input('name', []);
            $phones = $request->input('phone', []);
            $nids = $request->input('nid', []);

             foreach ($names as $index => $passengerName) {
                $passengerPhone = $phones[$index] ?? null; // Get phone at the same index, default to null
                $passengerNid = $nids[$index] ?? null;     // Get NID at the same index, default to null

            
                if (!empty($passengerName) || !empty($passengerPhone)) {
                    $ticket->otherPassengers()->create([
                        'name' => $passengerName,
                        'phone' => $passengerPhone,
                        'nid' => $passengerNid,
                    ]);
                }
            }
            
            // 5. Save Distance Segments
            // Note: Adhering strictly to your provided DistanceSegment $fillable: ['ticket_id', 'from_location', 'waypoint']
            
            $all_addresses = [$request->from_location_address];
            $waypointsAddresses = $request->input('waypoint', []); // Corrected input name from 'waypoints_address' to 'waypoint'
            foreach ($waypointsAddresses as $address) {
                $all_addresses[] = $address;
            }

            for ($i = 0; $i < count($all_addresses) - 1; $i++) {
                $ticket->distanceSegments()->create([
                    'from_location' => $all_addresses[$i],     // Corresponds to start_location_address
                    'waypoint' => $all_addresses[$i + 1],      // Corresponds to end_location_address
                    // 'segment_order', 'start_lat', 'start_lng', 'end_lat', 'end_lng', 'segment_distance_km', 'segment_distance_nm'
                    // are NOT included here as they are not in your DistanceSegment model's $fillable array.
                    // If you need them, please update your DistanceSegment model and migration.
                ]);
            }

            DB::commit();


           

                return redirect()->route('front.userDashboard')->with('success','Created successfully!');

           
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating ticket: ' . $e->getMessage());
            return response()->route('error_500');
        }
    }

    public function customerPersonalTicketPdf($id)
    {
         $ticket = Ticket::with([
            'customer',
            'helicopterModel',
            'flightType',
            'otherPassengers',
            'distanceSegments'
        ])->find($id);

        if (!$ticket) {
            abort(404, 'Ticket not found for printing.');
        }

        return view('admin.ticket.print_ticket_pdf', compact('ticket'));
    }

     public function c($id)
    {
        $generalTicket = GeneralTicket::with(['customer', 'passengers', 'payments', 'ticketable'])->findOrFail($id);
        $item = $generalTicket->ticketable;
        $payments = $generalTicket->payments;


        

        try {


            


      $data = view('admin.generalTicket.ticket_pdf', compact('generalTicket', 'item', 'payments'))->render();

      $file_Name_Custome = 'ticketList';
      $pdfFilePath =$file_Name_Custome.'.pdf';


       $mpdf = new Mpdf([ 'default_font_size' => 14,'default_font' => 'nikosh']);
       $mpdf->WriteHTML($data);
       $mpdf->Output($pdfFilePath, "I");
       die();



        } catch (\Mpdf\MpdfException $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
