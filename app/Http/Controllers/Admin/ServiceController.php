<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketExport;
use Mpdf\Mpdf;
use App\Models\User;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Service;
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
class ServiceController extends Controller
{
     function __construct()
    {
         $this->middleware('permission:serviceView|serviceAdd|serviceUpdate|serviceDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:serviceAdd', ['only' => ['create','store']]);
         $this->middleware('permission:serviceUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:serviceDelete', ['only' => ['destroy']]);
    }

     public function index(Request $request)
    {
        // Check if the request is an AJAX request for data
        if ($request->ajax()) {
           
            $query = Service::query();

            // Apply search filter
            if ($request->has('search') && $request->input('search') != '') {
                $searchTerm = $request->input('search');
                $query->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('slug', 'like', '%' . $searchTerm . '%')
                      ->orWhere('des', 'like', '%' . $searchTerm . '%');
            }

            // Apply sorting (if implemented in frontend and passed as params)
            // Example: ?sort_by=name&sort_order=asc
            if ($request->has('sort_by') && $request->has('sort_order')) {
                $sortBy = $request->input('sort_by');
                $sortOrder = $request->input('sort_order');
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('id', 'desc'); // Default sort
            }


            // Apply pagination
            $perPage = $request->input('per_page', 10); // Default to 10 rows per page
            $customers = $query->paginate($perPage);

            // Return paginated data as JSON
            return response()->json([
                'data' => $customers->items(), // Actual customer data for current page
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
            ]);
        }

        // If it's not an AJAX request, return the Blade view for the initial page load.
        return view('admin.service.index');
    }

     public function create()
    {
        return view('admin.service.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255', // Changed from mainTitle
            'slug' => 'nullable|string|max:255|unique:services,slug', // Changed table name
            'des' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // Max size handled by Intervention
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $input = $request->except('_token');

        // Handle image upload and compression with Intervention Image
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '.' . $imageFile->getClientOriginalExtension();
            $destinationPath = public_path('uploads/services'); // Changed destination folder

            // Create directory if it doesn't exist
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $img = Image::read($imageFile->getRealPath());

           ;

            // Compress image to fit 500KB (approx)
            $maxFileSize = 500 * 1024; // 500 KB in bytes
            $quality = 90;
            $imgFormat = $imageFile->getClientOriginalExtension();

            do {
                if ($imgFormat === 'png') {
                    $img->save($destinationPath . '/' . $imageName, $quality);
                } else {
                    $img->save($destinationPath . '/' . $imageName, $quality);
                }
                clearstatcache();
                $currentSize = File::size($destinationPath . '/' . $imageName);
                $quality -= 5;
            } while ($currentSize > $maxFileSize && $quality >= 10);

            if ($currentSize > $maxFileSize) {
                \Log::warning("Service Image '{$imageName}' could not be compressed to under 500KB. Current size: " . round($currentSize / 1024, 2) . "KB");
            }

            $input['image'] = 'public/uploads/services/' . $imageName; // Save path in database
        }

        // Generate slug if not provided
        if (empty($input['slug'])) {
            $input['slug'] = Str::slug($input['title']); // Changed from mainTitle
        }

        Service::create($input); // Changed from Offer

        return redirect()->route('service.index')->with('success', 'Service created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service) // Changed from Offer $offer
    {
        return view('admin.service.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service) // Changed from Offer $offer
    {
        return view('admin.service.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service) // Changed from Offer $offer
    {
        $request->validate([
            'title' => 'required|string|max:255', // Changed from mainTitle
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id, // Changed table and ID
            'des' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // Max size handled by Intervention
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $input = $request->except('_token', '_method');

        // Handle image update and compression with Intervention Image
        if ($request->hasFile('image')) {
            // Delete old image if it exists in the public/uploads folder
            if ($service->image && File::exists(public_path($service->image))) { // Changed from offer
                File::delete(public_path($service->image)); // Changed from offer
            }

            $imageFile = $request->file('image');
            $imageName = time() . '.' . $imageFile->getClientOriginalExtension();
            $destinationPath = public_path('uploads/services'); // Changed destination folder

            // Create directory if it doesn't exist
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $img = Image::read($imageFile->getRealPath());

           
            // Compress image to fit 500KB (approx)
            $maxFileSize = 500 * 1024; // 500 KB in bytes
            $quality = 90;
            $imgFormat = $imageFile->getClientOriginalExtension();

            do {
                if ($imgFormat === 'png') {
                    $img->save($destinationPath . '/' . $imageName, $quality);
                } else {
                    $img->save($destinationPath . '/' . $imageName, $quality);
                }
                clearstatcache();
                $currentSize = File::size($destinationPath . '/' . $imageName);
                $quality -= 5;
            } while ($currentSize > $maxFileSize && $quality >= 10);

            if ($currentSize > $maxFileSize) {
                 \Log::warning("Service Image '{$imageName}' could not be compressed to under 500KB during update. Current size: " . round($currentSize / 1024, 2) . "KB");
            }

            $input['image'] = 'public/uploads/services/' . $imageName; // Save new path in database
        } else {
            // If no new image, retain the old one
            $input['image'] = $service->image; // Changed from offer
        }

        // Generate slug if not provided, or update if title changes and slug is empty
        if (empty($input['slug'])) {
            $input['slug'] = Str::slug($input['title']); // Changed from mainTitle
        }

        $service->update($input); // Changed from Offer

        return redirect()->route('service.index')->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service) // Changed from Offer $offer
    {
        // Delete associated image from public/uploads folder if it exists
        if ($service->image && File::exists(public_path($service->image))) { // Changed from offer
            File::delete(public_path($service->image)); // Changed from offer
        }

        $service->delete(); // Changed from Offer

        return redirect()->route('service.index')->with('success', 'Service deleted successfully!');
    }
}
