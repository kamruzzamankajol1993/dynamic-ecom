<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:posView')->only(['index']);
        $this->middleware('can:posAdd')->only(['create', 'store']);
        $this->middleware('can:posUpdate')->only(['edit', 'update']);
        $this->middleware('can:posDelete')->only(['destroy']);
    }

    public function index()
    {
        return view('pos.index');
    }

     public function search(Request $request)
    {
        $query = $request->get('q');
        $customers = Customer::where('name', 'LIKE', $query . '%')
            ->orWhere('phone', 'LIKE', $query . '%')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return response()->json($customer);
    }
}
