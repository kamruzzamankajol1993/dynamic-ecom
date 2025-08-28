<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketContactInfo;
use Illuminate\Http\Request;

class TicketContactInfoController extends Controller
{
    public function index()
    {
        return view('admin.support.contact_infos.index');
    }

    public function data(Request $request)
    {
        $query = TicketContactInfo::query();

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $contacts = $query->paginate(10);

        return response()->json([
            'data' => $contacts->items(),
            'total' => $contacts->total(),
            'current_page' => $contacts->currentPage(),
            'last_page' => $contacts->lastPage(),
        ]);
    }

    public function create()
    {
        return view('admin.support.contact_infos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|boolean',
        ]);

        TicketContactInfo::create($request->all());

        return redirect()->route('ticket-contact-infos.index')->with('success', 'Contact info created successfully.');
    }

    public function edit(TicketContactInfo $ticket_contact_info)
    {
        return view('admin.support.contact_infos.edit', ['contact' => $ticket_contact_info]);
    }

    public function update(Request $request, TicketContactInfo $ticket_contact_info)
    {
        $request->validate([
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|boolean',
        ]);

        $ticket_contact_info->update($request->all());

        return redirect()->route('ticket-contact-infos.index')->with('success', 'Contact info updated successfully.');
    }

    public function destroy(TicketContactInfo $ticket_contact_info)
    {
        $ticket_contact_info->delete();
        return response()->json(['message' => 'Contact info deleted successfully.']);
    }
}