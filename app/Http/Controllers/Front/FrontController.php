<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\ClientSay;
use App\Models\Gallery;
use App\Models\Review;
use App\Models\AboutUs;
use App\Models\Contact;
use App\Models\NewsAndMedia;
use App\Models\Blog;
use App\Models\ExtraPage;
use App\Models\Ticket;
use App\Models\GeneralTicket;
use Carbon\Carbon;
use App\Models\GeneralTicketPassenger;
use App\Models\GeneralTicketPayment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
class FrontController extends Controller
{

    // app/Http/Controllers/Front/FrontController.php

public function ajaxTicketSearch(Request $request)
{
    $ticketNumber = $request->input('ticketNumber');

    if (!$ticketNumber) {
        return response()->json(['error' => 'Ticket number is required.'], 400);
    }

    // Search for a personal/chartered ticket
    $personalTicket = Ticket::with('customer', 'flightType')
                            ->where('ticket_number', $ticketNumber)
                            ->first();

    // Search for a general ticket (offer/service)
    $generalTicket = GeneralTicket::with('customer', 'ticketable', 'payments')
                                ->where('ticket_number', $ticketNumber)
                                ->first();

    $data = [
        'personal_ticket' => null,
        'general_ticket' => null,
    ];

    if ($personalTicket) {
        $data['personal_ticket'] = [
            'ticket_number' => $personalTicket->ticket_number,
            'booking_type' => 'Personal Chartered',
            'item_booked' => $personalTicket->flightType->name ?? 'N/A',
            'departure' => $personalTicket->from_location_address ?? 'N/A',
            'landing' => 'N/A', // Not directly available on Ticket model
            'fly_date' => Carbon::parse($personalTicket->fly_date)->format('d M, Y'),
            'total_paid' => number_format($personalTicket->total_price, 2),
            'payment_method' => 'N/A', // No direct payment info in Ticket model
            'download_url' => route('customerPersonalTicketPdf', $personalTicket->id)
        ];
    }

    if ($generalTicket) {
        $itemBooked = 'N/A';
        $departure = 'N/A';
        $landing = 'N/A';
        $flyDate = 'N/A';

        if ($generalTicket->ticketable) {
            $itemBooked = $generalTicket->ticketable->title ?? $generalTicket->ticketable->mainTitle ?? 'N/A';
            if ($generalTicket->ticket_type === 'App\Models\Offer') {
                $departure = $generalTicket->ticketable->departure;
                $landing = $generalTicket->ticketable->landing;
                $flyDate = Carbon::parse($generalTicket->ticketable->fly_date)->format('d M, Y');
            }
        }

        $totalPaid = $generalTicket->payments->sum('paid_amount');
        $paymentMethods = $generalTicket->payments->pluck('payment_method')->unique()->implode(', ');

        $data['general_ticket'] = [
            'ticket_number' => $generalTicket->ticket_number,
            'booking_type' => $generalTicket->ticket_type === 'App\Models\Offer' ? 'Offer Booking' : 'Service Booking',
            'item_booked' => $itemBooked,
            'departure' => $departure,
            'landing' => $landing,
            'flight_date' => $flyDate,
            'total_paid' => number_format($totalPaid, 2),
            'payment_method' => $paymentMethods ?: 'N/A',
            'download_url' => route('customerGeneralTicketPdf', $generalTicket->id)
        ];
    }

    $data['found'] = !is_null($data['personal_ticket']) || !is_null($data['general_ticket']);

    return response()->json($data);
}
    public function index(){
        $sliders = Slider::where('status', 'Active')->latest()->get();
        $offers = Offer::where('status', 'Active')->where("fly_date",">",date('Y-m-d'))->limit(8)->get();
        $services = Service::where('status', 'Active')->latest()->limit(6)->get();

        $clientSays = ClientSay::where('status', 'Active')->latest()->limit(8)->get();

        $galleries = Gallery::where('status', 'Active')->latest()->limit(8)->get();

        $reviews = Review::where('status', 'Active')->latest()->limit(2)->get();

        /// dd($sliders); // Debugging line to check the sliders data
        return view('front.home.index', compact('sliders', 'offers', 'services', 'clientSays', 'galleries', 'reviews'));
    }

    public function offerList(){
        $offers = Offer::where('status', 'Active')->where("fly_date",">",date('Y-m-d'))->paginate(12);
        if ($offers->isEmpty()) {
            return redirect()->route('front.index')->with('error', 'No offers found.');
        }
        return view('front.offerList', compact('offers'));
    }

    public function serviceList(){
        $services = Service::where('status', 'Active')->latest()->paginate(12);
        if ($services->isEmpty()) {
            return redirect()->route('front.index')->with('error', 'No services found.');
        }
        return view('front.serviceList', compact('services'));
    }

    public function aboutUs(){
        $aboutUs = AboutUs::first();
        if (!$aboutUs) {    
            return redirect()->route('front.index')->with('error', 'About Us content not found.');
        }
        return view('front.aboutUs', compact('aboutUs'));
    }

    public function contact(){
        $contact = Contact::first();
        if (!$contact) {
            return redirect()->route('front.index')->with('error', 'Contact content not found.');
        }
        return view('front.contact', compact('contact'));
    }

    public function contactSubmit(Request $request){
        // Handle contact form submission
    }

    public function blog(){
        $blogs = Blog::where('status', 'Active')->latest()->paginate(12);
        if ($blogs->isEmpty()) {
            return redirect()->route('front.index')->with('error', 'No blog posts found.');
        }
        return view('front.blog', compact('blogs'));
    }

    public function blogDetails($slug){
        // Decode the slug to get the ID
        $id = base64_decode($slug);
        $blog = Blog::where('status', 'Active')->find($id);
        if (!$blog) {
            return redirect()->route('front.blog')->with('error', 'Blog post not found.');
        }
        // Pass the blog data to the view
        $slug = $blog->slug; // Use the slug from the blog post
        return view('front.blogDetails', compact('blog', 'slug'));
    }       
      

    public function gallery(){
        return view('front.gallery');
    }

    public function newsAndMedia(){
        $newsAndMedia = NewsAndMedia::where('status', 'Active')->latest()->get();
        if ($newsAndMedia->isEmpty()) {
            return redirect()->route('front.index')->with('error', 'No news and media content found.');
        }
        // Pass the news and media data to the view
        return view('front.newsAndMedia', compact('newsAndMedia'));
    }

    public function extraPage($slug){

        // Decode the slug to get the ID
        $id = base64_decode($slug);
        $newsAndMediaDetail = NewsAndMedia::where('status', 'Active')->find($id);
        return view('front.newsAndMediaDetail', compact('slug', 'id', 'newsAndMediaDetail'));
    }

    public function termsAndConditions(){
        $termsAndConditions = ExtraPage::first();
        if (!$termsAndConditions) {
            return redirect()->route('front.index')->with('error', 'Terms and Conditions content not found.');
        }
        return view('front.termsAndConditions', compact('termsAndConditions'));
    }

    public function privacyPolicy(){
        $privacyPolicy = ExtraPage::first();
        if (!$privacyPolicy) {
            return redirect()->route('front.index')->with('error', 'Privacy Policy content not found.');
        }
        return view('front.privacyPolicy', compact('privacyPolicy'));
    }
    public function refundPolicy(){
        $returnPolicy = ExtraPage::first();
        if (!$returnPolicy) {
            return redirect()->route('front.index')->with('error', 'Return Policy content not found.');
        }
        return view('front.returnPolicy', compact('returnPolicy'));
    }


    public function ticketSearch()
    {
      
        return view('front.ticketSearch');
    }

    public function offerListDetails($slug)
    {
        // Decode the slug to get the ID
        $id = base64_decode($slug);
        $offer = Offer::where('status', 'Active')->find($id);
        if (!$offer) {
            return redirect()->route('front.offerList')->with('error', 'Offer not found.');
        }
        // Pass the offer data to the view
        $slug = $offer->slug; // Use the slug from the offer

               
        return view('front.offerListDetails', compact('offer', 'slug'));
                
    }

    public function serviceListDetails($slug)
    {
        // Decode the slug to get the ID
        $id = base64_decode($slug);
        $service = Service::where('status', 'Active')->find($id);
        if (!$service) {
            return redirect()->route('front.serviceList')->with('error', 'Service not found.');
        }
        // Pass the service data to the view
        $slug = $service->slug; // Use the slug from the service
       
        return view('front.serviceListDetails', compact('service', 'slug'));
   
}


public function storeBooking(Request $request)
{
    // Validation logic remains the same...
    $request->validate([ /* ... */ ]);

    DB::beginTransaction();
    try {
        // Logic for identifying item type and creating the ticket record remains the same...
        $item = null;
        $itemType = null;
        if ($request->has('offer_id')) {
            $item = Offer::findOrFail($request->offer_id);
            $itemType = 'offer';
            // ... (seat checking logic)
        } elseif ($request->has('service_id')) {
            $item = Service::findOrFail($request->service_id);
            $itemType = 'service';
        } else {
            return back()->with('error', 'Invalid booking request.');
        }

        $ticket = GeneralTicket::create([
            'customer_id' => Auth::user()->customer_id,
            'ticket_type' => $itemType,
            'offer_or_service_id' => $item->id,
            'ticket_number' => 'TKT-' . strtoupper(bin2hex(random_bytes(3))),
            'total_seat' => $request->numSeats,
            'sub_total_price' => $itemType === 'offer' ? ($item->price * $request->numSeats) : $item->price,
            'total_price' => $request->grand_total,
            'payment_status' => 'Pending',
            'mfc_name' => $request->paymentType === 'Mobile Banking' ? $request->mfc_name : null,
        ]);

        foreach ($request->passengers as $passengerData) {
            $ticket->passengers()->create($passengerData);
        }

        // Updated Payment Handling Logic
        if ($request->paymentType === 'Online Payment') {
            $tran_id = $ticket->id . '_' . Str::upper(Str::random(6));
            $ticket->transaction_id = $tran_id;
            $ticket->save();

            $url = 'https://sandbox.aamarpay.com/request.php';
            $fields = [
                'store_id' => 'aamarpaytest',
                'signature_key' => 'dbb74894e82415a2f7ff0ec3a97e4183',
                'cus_name'  => $request->passengers[0]['name'],
                'cus_email' => Auth::user()->email,
                'cus_phone' => $request->passengers[0]['phone'],
                'amount'    => $request->grand_total,
                'currency'  => 'BDT',
                'tran_id'   => $tran_id,
                'desc'      => $item->title ?? $item->mainTitle,
                'success_url' => route('payment.success'),
                'fail_url' => route('payment.fail'),
                'cancel_url' => route('payment.cancel'),
                'opt_a' => $ticket->id,
            ];

            $response = Http::asForm()->post($url, $fields);

            if ($response->successful()) {
                // The body is a JSON-encoded string, e.g., "\/paynow.php?track=..."
                $responseBody = $response->body();

                // Decode the JSON string into a normal path string
                $paymentPath = json_decode($responseBody);

                // Check if we received a valid, non-empty string path
                if (is_string($paymentPath) && !empty($paymentPath)) {
                    
                    // Construct the full, absolute URL for redirection
                    $fullPaymentUrl = 'https://sandbox.aamarpay.com' . $paymentPath;

                    DB::commit();
                    return redirect()->away($fullPaymentUrl);
                }
            }
            
            // If the process fails at any point, rollback and show a generic error
            DB::rollBack();
            return redirect()->back()->with('error', 'Could not process the payment gateway request. Please try again.');

        } else {
            // Logic for Cash, Card, Bank, Mobile Banking remains the same
            $ticket->payments()->create([
                'paid_amount' => $request->grand_total,
                'payment_method' => $request->paymentType,
                'payment_date' => now(),
                'transaction_id' => $request->transaction_id,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'branch_name' => $request->branch_name,
                'notes' => $request->paymentType === 'Mobile Banking' ? $request->mfc_name : null,
            ]);
            $ticket->payment_status = 'Paid';
            $ticket->save();
        }

        DB::commit();
        return redirect()->route('booking.success')->with('success', 'Your booking has been confirmed successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Booking Exception: ' . $e->getMessage());
        return back()->with('error', 'An error occurred during booking: ' . $e->getMessage());
    }
}
public function paymentSuccess(Request $request)
    {
        DB::beginTransaction();
        try {
            $ticket = GeneralTicket::find($request->opt_a);
            if ($ticket && $ticket->payment_status === 'Pending') {
                $ticket->payment_status = 'Paid';
                $ticket->save();

                $ticket->payments()->create([
                    'paid_amount' => $request->amount,
                    'payment_method' => 'Online Payment',
                    'payment_date' => now(),
                    'transaction_id' => $request->mer_txnid,
                    'notes' => 'Paid via aamarpay. Card Type: ' . $request->card_type,
                ]);
                DB::commit();
                return redirect()->route('booking.success')->with('success', 'Payment successful and booking confirmed!');
            }
            DB::rollBack();
            return redirect()->route('front.index')->with('error', 'Invalid or already processed payment.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('front.index')->with('error', 'A critical error occurred after payment.');
        }
    }

    public function paymentFail(Request $request)
    {
        $ticket = GeneralTicket::find($request->opt_a);
        if($ticket){
            $ticket->payment_status = 'Failed';
            $ticket->save();
        }
        return redirect()->route('front.index')->with('error', 'Payment failed. Please try again.');
    }

    public function paymentCancel(Request $request)
    {
        return redirect()->route('front.index')->with('info', 'Payment was cancelled.');
    }

    public function bookingSuccess()
    {
        return view('front.booking_success');
    }
}
