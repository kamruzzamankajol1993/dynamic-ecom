<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use Mpdf\Mpdf;
use App\Models\ClientSay;
use App\Models\Review;
use App\Models\ExtraPage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class ExtraPageController extends Controller
{
    public function index()
    {
        // We will work with the first record, as there's only one row of settings.
        $extraPage = ExtraPage::first();
        return view('admin.extraPage.manage', compact('extraPage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'privacy_policy' => 'nullable|string',
            'term_condition' => 'nullable|string',
            'return_pollicy' => 'nullable|string',
        ]);

        ExtraPage::create($request->all());

        return redirect()->route('extraPage.index')
                         ->with('success', 'Page content created successfully.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {

        $extraPage = ExtraPage::find($id);
        $request->validate([
            'privacy_policy' => 'nullable|string',
            'term_condition' => 'nullable|string',
            'return_pollicy' => 'nullable|string',
        ]);

        $extraPage->update($request->all());

        return redirect()->route('extraPage.index')
                         ->with('success', 'Page content updated successfully.');
    }
}
