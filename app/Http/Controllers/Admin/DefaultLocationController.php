<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use App\Exports\PermissionExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use App\Models\DefaultLocation;
class DefaultLocationController extends Controller
{
 function __construct()
    {
         $this->middleware('permission:defaultLocationView|defaultLocationAdd|defaultLocationUpdate|defaultLocationDelete', ['only' => ['index','store','destroy','update']]);
         $this->middleware('permission:defaultLocationAdd', ['only' => ['create','store']]);
         $this->middleware('permission:defaultLocationUpdate', ['only' => ['edit','update']]);
         $this->middleware('permission:defaultLocationDelete', ['only' => ['destroy']]);
    }

     public function index()
    {
        // Attempt to find the first DefaultLocation record.
        // If it exists, it will be passed to the view for editing.
        // If not, the view will show the creation form.
        $location = DefaultLocation::first();
        
        return view('admin.default-location.index', compact('location'));
    }

    /**
     * Store a newly created resource in storage or update an existing one.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data.
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // Use updateOrCreate to either create a new location if none exists,
        // or update the first one found.
        // We use a known ID (e.g., 1) or just the first record to ensure we only ever have one.
        DefaultLocation::updateOrCreate(
            ['id' => optional(DefaultLocation::first())->id], // Find existing by its ID, or null for new
            [
                'name' => $request->name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        // Redirect back to the form with a success message.
        return redirect()->route('defaultLocation.index')
                         ->with('success', 'Default location saved successfully.');
    }
}
