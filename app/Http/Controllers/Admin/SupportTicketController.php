<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportQa;
use App\Models\SupportPageCategory;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        return view('admin.support.tickets.index');
    }

    public function data(Request $request)
    {
        $query = SupportQa::with('category:id,name');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('question', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $tickets = $query->paginate(10);

        return response()->json([
            'data' => $tickets->items(),
            'total' => $tickets->total(),
            'current_page' => $tickets->currentPage(),
            'last_page' => $tickets->lastPage(),
        ]);
    }

    public function create()
    {
        $categories = SupportPageCategory::where('status', true)->get();
        return view('admin.support.tickets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255|unique:support_tickets',
            'answer' => 'required|string',
            'category_id' => 'nullable|exists:support_page_categories,id',
            'is_faq' => 'boolean',
            'status' => 'required|boolean',
        ]);

        SupportQa::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'category_id' => $request->category_id,
            'is_faq' => $request->boolean('is_faq'),
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('support-tickets.index')->with('success', 'Support ticket created successfully.');
    }

    public function show(SupportTicket $support_ticket)
    {
        $support_ticket->load('category');
        return view('admin.support.tickets.show', ['ticket' => $support_ticket]);
    }

    public function edit(SupportTicket $support_ticket)
    {
        $categories = SupportPageCategory::where('status', true)->get();
        return view('admin.support.tickets.edit', ['ticket' => $support_ticket, 'categories' => $categories]);
    }

    public function update(Request $request, SupportTicket $support_ticket)
    {
        $request->validate([
            'question' => 'required|string|max:255|unique:support_tickets,question,' . $support_ticket->id,
            'answer' => 'required|string',
            'category_id' => 'nullable|exists:support_ticket_categories,id',
            'is_faq' => 'boolean',
            'status' => 'required|boolean',
        ]);
        
        $support_ticket->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category_id' => $request->category_id,
            'is_faq' => $request->boolean('is_faq'),
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('support-tickets.index')->with('success', 'Support ticket updated successfully.');
    }

    public function destroy(SupportTicket $support_ticket)
    {
        $support_ticket->delete();
        return response()->json(['message' => 'Support ticket deleted successfully.']);
    }
}