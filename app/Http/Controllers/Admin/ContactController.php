<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use Mpdf\Mpdf;
use App\Models\Contact;
use App\Models\Blog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Log;
use Carbon\Carbon;
class ContactController extends Controller
{
    /**
     * Display the form to manage contact details.
     * It will show an edit form if details exist, otherwise a create form.
     */
    public function index()
    {
        $contact = Contact::first(); // We only need one row for all contact details
        return view('admin.contact.manage', compact('contact'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_one'   => 'required|string|max:255',
            'phone_two'   => 'nullable|string|max:255',
            'email_one'   => 'required|email|max:255',
            'email_two'   => 'nullable|email|max:255',
            'address_one' => 'required|string',
            'address_two' => 'nullable|string',
        ]);

        Contact::create($request->all());

        return redirect()->route('contact.index')
                         ->with('success', 'Contact details have been saved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'phone_one'   => 'required|string|max:255',
            'phone_two'   => 'nullable|string|max:255',
            'email_one'   => 'required|email|max:255',
            'email_two'   => 'nullable|email|max:255',
            'address_one' => 'required|string',
            'address_two' => 'nullable|string',
        ]);

        $contact->update($request->all());

        return redirect()->route('contact.index')
                         ->with('success', 'Contact details have been updated successfully.');
    }
}
