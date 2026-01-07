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
use Illuminate\Support\Facades\Http;
use Exception;

class CustomerController extends Controller
{

    public function bulkUpdateType(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'type' => 'required|in:normal,silver,platinum',
    ]);

    try {
        $discount = 0;
        if($request->type == 'silver') $discount = 5;
        if($request->type == 'platinum') $discount = 10;

        // Update Type and Discount for all selected IDs
        Customer::whereIn('id', $request->ids)->update([
            'type' => $request->type,
            'discount_in_percent' => $discount
        ]);

        return response()->json(['success' => true, 'message' => 'Selected customers updated successfully.']);
    } catch (Exception $e) {
        Log::error('Bulk update failed: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to update.'], 500);
    }
}
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
        // 1. Capture the year from the request
        $year = $request->year;

        // 2. Start Query with Address and Orders Sum
        // We pass $year into the closure with 'use ($year)'
        $query = Customer::with('addresses')->withSum(['orders' => function ($query) use ($year) {
            $query->where('payment_status', 'paid');
            
            // If a specific year is selected, filter the sum calculation by that year
            if (!empty($year)) {
                $query->whereYear('created_at', $year);
            }
        }], 'total_amount');

        // 3. Search Logic
        // Wrapped in a closure to ensure it works correctly with other filters
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm . '%')
                  ->orWhere('email', 'like', $searchTerm . '%')
                  ->orWhere('phone', 'like', $searchTerm . '%')
                  ->orWhere('secondary_phone', 'like', $searchTerm . '%');
            });
        }

        // 4. Type Filter Logic
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 5. Sorting and Pagination
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

        // --- ADD THIS LINE ---
        // Set time limit to 0 (unlimited) for this script only
        // set_time_limit(0); 

        // $apiUrl = 'https://adminpanel.spotlightattires.com/api/clients';
        // $importedCount = 0;
        // $skippedCount = 0; // Counts records skipped (no address OR already exist)
        // $failedCount = 0;  // Counts records that failed (e.g., no phone)

        // try {
        //     // 1. Fetch data from the API
        //     $response = Http::get($apiUrl);

        //     if ($response->failed()) {
        //         Log::error('Failed to fetch from API: ' . $response->body());
        //         return redirect()->back()->with('error', 'Could not connect to the client API.');
        //     }

        //     $clients = $response->json()['data'] ?? [];

        //     if (empty($clients)) {
        //         return redirect()->route('customer.index')->with('success', 'No clients found to import.');
        //     }

        //     // 2. Process each client within a database transaction
        //     DB::transaction(function () use ($clients, &$importedCount, &$skippedCount, &$failedCount) {
                
        //         foreach ($clients as $client) {
        //             try {
                        
        //                 // --- NEW CHECK ---
        //                 // 1. Check for valid address first
        //                 $address = $client['address'] ?? null;
                        
        //                 // Skip if address is empty, null, or the string 'no address'
        //                 if (empty($address) || strtolower(trim($address)) === 'no address') {
        //                     $skippedCount++;
        //                     continue;
        //                 }
                        
        //                 // 2. Check for valid phone
        //                 $phone = $client['phone'] ?? null;
        //                 if (empty($phone)) {
        //                     $failedCount++; // Fail if no phone number
        //                     continue;
        //                 }
                        
        //                 // 3. Check if Customer already exists by phone
        //                 $existingCustomer = Customer::where('phone', $phone)->first();
        //                 if ($existingCustomer) {
        //                     $skippedCount++; // Skip if customer already exists
        //                     continue;
        //                 }
                        
        //                 // 4. Create the new Customer (since they passed all checks)
        //                 $customer = Customer::create([
        //                     'phone' => $phone,
        //                     'name' => $client['name'],
        //                     'email' => $client['email'] ?? null,
        //                     'source' => 'api_import',
        //                     'type' => 'normal',
        //                     'user_id' => null,
        //                 ]);

        //                 // 5. Create the Address
        //                 // We already know the address is valid from Check #1
        //                 $customer->addresses()->create([
        //                     'address' => $address, // Use the checked $address variable
        //                     'address_type' => 'home',
        //                     'is_default' => true,
        //                 ]);
                        
        //                 $importedCount++;

        //             } catch (Exception $e) {
        //                 // Log error for this specific client and continue
        //                 Log::error("Failed to import client with phone {$client['phone']}: " . $e->getMessage());
        //                 $failedCount++;
        //             }
        //         }
        //     });

        //     Log::info("Customer import complete. Imported: $importedCount, Skipped: $skippedCount, Failed: $failedCount");
            
        //     $message = "Import complete. $importedCount new customers imported (with addresses).";
        //     if ($skippedCount > 0) {
        //         $message .= " $skippedCount customers were skipped (either no address or already exist).";
        //     }
        //     if ($failedCount > 0) {
        //         $message .= " $failedCount records failed to import (e.g., no phone number).";
        //     }

        //     return redirect()->route('customer.index')->with('success', $message);

        // } catch (Exception $e) {
        //     // Catch errors from the API call or DB transaction
        //     Log::error('Failed to import customers: ' . $e->getMessage());
        //     return redirect()->back()->with('error', 'Failed to import customers. Please check logs.');
        // }

        ///
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
            'secondary_phone' => ['nullable', 'string', 'digits:11', 'unique:customers', 'unique:users'],
            'type' => ['required', 'string', 'in:normal,silver,platinum'],
            'discount_in_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'addresses' => ['required', 'array', 'min:1'],
            'addresses.*.address' => ['required', 'string', 'max:255'],
            'default_address_index' => ['required', 'numeric'],
        ];

        if ($request->boolean('create_login_account')) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users'];
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
                        'secondary_phone' => $request->secondary_phone,
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
                    'secondary_phone' => $request->secondary_phone,
                    'type' => $request->type,
                    'discount_in_percent' => $request->discount_in_percent ?? 0,
                    'source' => 'admin',
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

    public function update(Request $request, Customer $customer)
    {
        $userId = $customer->user_id ?? 'NULL';
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:customers,phone,' . $customer->id],
            'secondary_phone' => ['nullable', 'string', 'digits:11', 'unique:customers,secondary_phone,' . $customer->id, 'unique:users,secondary_phone,' . $userId],
            'type' => ['required', 'string', 'in:normal,silver,platinum'],
            'addresses' => ['required', 'array', 'min:1'],
            'addresses.*.address' => ['required', 'string', 'max:255'],
            'default_address_index' => ['required', 'numeric'],
            'discount_in_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];

        if ($customer->user_id || $request->boolean('create_login_account')) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId];
            $rules['password'] = ['nullable', 'confirmed', Rules\Password::min(8)];
        } else {
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:customers,email,' . $customer->id];
        }

        $request->validate($rules);

        try {
            DB::transaction(function () use ($request, $customer) {
                $userId = $customer->user_id;

                if (!$userId && $request->boolean('create_login_account')) {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'secondary_phone' => $request->secondary_phone,
                        'user_type' => 1,
                        'status' => 1,
                        'password' => $request->password,
                        'customer_id' => $customer->id,
                    ]);
                    $userId = $user->id;
                } 
                else if ($userId) {
                    $user = User::find($userId);
                    if ($user) {
                        $userData = [
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                            'secondary_phone' => $request->secondary_phone,
                        ];
                        if ($request->filled('password')) {
                            $userData['password'] = $request->password;
                        }
                        $user->update($userData);
                    }
                }

                $customer->update([
                    'user_id' => $userId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'secondary_phone' => $request->secondary_phone,
                    'type' => $request->type,
                    'discount_in_percent' => $request->discount_in_percent ?? 0,
                ]);

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