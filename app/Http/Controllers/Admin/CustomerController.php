<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use App\Models\User; 
use Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            return view('admin.customer.index');
        } catch (Exception $e) {
            Log::error('Failed to load customer index page: ' . $e);
            return redirect()->back()->with('error', 'Could not load the page.');
        }
    }

    public function data(Request $request)
    {
        try {
            $query = Customer::with('addresses')->withSum(['orders' => function ($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount');

            if ($request->filled('search')) {
                $query->where('name', 'like', $request->search . '%')
                      ->orWhere('email', 'like', $request->search . '%')
                      ->orWhere('phone', 'like', $request->search . '%');
            }

            $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
            $customers = $query->paginate(10);

            return response()->json([
                'data' => $customers->items(),
                'total' => $customers->total(),
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch customer data: ' . $e);
            return response()->json(['error' => 'Failed to retrieve data.'], 500);
        }
    }

    public function create()
    {
        try {
            return view('admin.customer.create');
        } catch (Exception $e) {
            Log::error('Failed to load create customer page: ' . $e);
            return redirect()->back()->with('error', 'Could not load the page.');
        }
    }

      public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:customers'],
            'type' => ['required', 'string', 'in:normal,silver,platinum'],
            'addresses' => ['required', 'array', 'min:1'],
            'addresses.*.address' => ['required', 'string', 'max:255'],
            'default_address_index' => ['required', 'numeric'],
        ];

        if ($request->boolean('create_login_account')) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users'];
            // --- UPDATED VALIDATION RULE ---
            $rules['password'] = ['required', 'confirmed', Rules\Password::min(8)];
        } else {
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:customers'];
        }

        $request->validate($rules);

        try {
            DB::transaction(function () use ($request) {
                $userId = null;
                if ($request->boolean('create_login_account')) {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'user_type' => 1,
                        'status' => 1,
                        'password' => $request->password,
                    ]);
                    $userId = $user->id;
                }

                $customer = Customer::create([
                    'user_id' => $userId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'type' => $request->type,
                    'source' => 'admin', // Customers created here are from admin
                ]);

                 if ($request->boolean('create_login_account')) {
                    $user->update(['customer_id' => $customer->id]);
                }   

                if ($request->has('addresses')) {
                    $defaultIndex = $request->default_address_index;
                    foreach ($request->addresses as $index => $addressData) {
                        if (!empty($addressData['address'])) {
                            $addressData['is_default'] = ($index == $defaultIndex);
                            $customer->addresses()->create($addressData);
                        }
                    }
                }
            });

            Log::info('Customer created successfully.', ['name' => $request->name]);
            return redirect()->route('customer.index')->with('success', 'Customer created successfully.');

        } catch (Exception $e) {
            Log::error('Failed to create customer: ' . $e);
            return redirect()->back()->with('error', 'Failed to create customer. Please check logs.')->withInput();
        }
    }

    public function show(Customer $customer)
    {
        try {
            $customer->load('addresses', 'orders');
            $user = $customer->user_id ? User::find($customer->user_id) : null;
            $totalOrders = $customer->orders->count();
            $pendingOrders = $customer->orders->where('status', 'pending')->count();
            $totalBuyAmount = $customer->orders->where('payment_status', 'paid')->sum('total_amount');

            $salesData = $customer->orders()
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(total_amount) as total_sales'))
                ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('month')->orderBy('month', 'asc')->get()->pluck('total_sales', 'month');

            $months = collect();
            for ($i = 11; $i >= 0; $i--) {
                $months->put(now()->subMonths($i)->format('Y-m'), 0);
            }
            $monthlyTotals = $months->merge($salesData);

            $chartData = [['Month', 'Amount']];
            foreach ($monthlyTotals as $month => $total) {
                $chartData[] = [date('M', strtotime($month . '-01')), $total];
            }

            return view('admin.customer.show', compact('customer', 'user', 'totalOrders', 'pendingOrders', 'totalBuyAmount', 'chartData'));
        } catch (Exception $e) {
            Log::error("Failed to show customer ID {$customer->id}: " . $e);
            return redirect()->route('customer.index')->with('error', 'Could not load customer details.');
        }
    }

    public function edit(Customer $customer)
    {
        try {
            $customer->load('addresses');
            $user = $customer->user_id ? User::find($customer->user_id) : null;
            return view('admin.customer.edit', compact('customer', 'user'));
        } catch (Exception $e) {
            Log::error("Failed to load edit page for customer ID {$customer->id}: " . $e);
            return redirect()->route('customer.index')->with('error', 'Customer not found.');
        }
    }

    // --- UPDATED UPDATE FUNCTION ---
    public function update(Request $request, Customer $customer)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:customers,phone,' . $customer->id],
            'type' => ['required', 'string', 'in:normal,silver,platinum'],
            'addresses' => ['required', 'array', 'min:1'],
            'addresses.*.address' => ['required', 'string', 'max:255'],
            'default_address_index' => ['required', 'numeric'],
        ];

        if ($customer->user_id || $request->boolean('create_login_account')) {
            $userId = $customer->user_id ?? 'NULL';
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId];
            $rules['password'] = ['nullable', 'confirmed', Rules\Password::min(8)];
        } else {
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:customers,email,' . $customer->id];
        }

        $request->validate($rules);

        try {
            DB::transaction(function () use ($request, $customer) {
                $userId = $customer->user_id;

                // Case 1: Customer has no login, but we are creating one now.
                if (!$userId && $request->boolean('create_login_account')) {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'user_type' => 1,
                        'status' => 1,
                        'password' => $request->password,
                        'customer_id' => $customer->id, // <-- The key change!
                    ]);
                    $userId = $user->id;
                } 
                // Case 2: Customer already has a login, so we update it.
                else if ($userId) {
                    $user = User::find($userId);
                    if ($user) {
                        $userData = [
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                        ];
                        if ($request->filled('password')) {
                            $userData['password'] = $request->password;
                        }
                        $user->update($userData);
                    }
                }

                // Update the customer with the latest info, including the new user_id if created.
                $customer->update([
                    'user_id' => $userId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'type' => $request->type,
                ]);

                // Re-sync addresses
                $customer->addresses()->delete();
                if ($request->has('addresses')) {
                    $defaultIndex = $request->default_address_index;
                    foreach ($request->addresses as $index => $addressData) {
                        if (!empty($addressData['address'])) {
                            $addressData['is_default'] = ($index == $defaultIndex);
                            $customer->addresses()->create($addressData);
                        }
                    }
                }
            });

            Log::info('Customer updated successfully.', ['id' => $customer->id]);
            return redirect()->route('customer.index')->with('success', 'Customer updated successfully.');
        } catch (Exception $e) {
            Log::error("Failed to update customer ID {$customer->id}: " . $e);
            return redirect()->back()->with('error', 'Failed to update customer. Please check logs.')->withInput();
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            // Deleting a customer might also delete their associated user account
            if ($customer->user_id) {
                User::find($customer->user_id)->delete();
            }
            $customer->delete();
            Log::info('Customer deleted successfully.', ['id' => $customer->id]);
            return redirect()->route('customer.index')->with('success', 'Customer deleted successfully.');
        } catch (Exception $e) {
            Log::error("Failed to delete customer ID {$customer->id}: " . $e);
            return redirect()->route('customer.index')->with('error', 'Failed to delete customer.');
        }
    }
}