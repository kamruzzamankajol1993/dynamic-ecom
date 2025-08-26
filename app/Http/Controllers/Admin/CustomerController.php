<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use App\Models\User; 
use Mpdf\Mpdf;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Color;
use App\Models\Size;
class CustomerController extends Controller
{
    public function index()
    {
         
        return view('admin.customer.index');
    }

      public function data(Request $request)
    {
        // Eager load addresses and calculate the sum of paid orders
        $query = Customer::with('addresses')->withSum(['orders' => function ($query) {
            $query->where('payment_status', 'paid');
        }], 'total_amount');

        if ($request->filled('search')) {
            $query->where('name', 'like',$request->search . '%')
                  ->orWhere('email', 'like',$request->search . '%')
                  ->orWhere('phone', 'like',$request->search . '%');
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $customers = $query->paginate(10);

        return response()->json([
            'data' => $customers->items(),
            'total' => $customers->total(),
            'current_page' => $customers->currentPage(),
            'last_page' => $customers->lastPage(),
        ]);
    }

    public function create()
    {
        return view('admin.customer.create');
    }

public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:customers'],
            'type' => ['required', 'string', 'in:normal,silver,platinum'],
            'addresses' => ['nullable', 'array'],
        ];

        if ($request->boolean('create_login_account')) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users'];
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        } else {
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:customers'];
        }

        $request->validate($rules);

        DB::transaction(function () use ($request) {
            $userId = null;
            if ($request->boolean('create_login_account')) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'user_type' => 2,
                    'status' => 1,
                    'password' => $request->password,
                ]);
                $userId = $user->id;
            }

            $customer = Customer::create([
                'user_id' => $userId,
                'name' => $request->name,
                'email' => $request->email, // Also store email on customer table for guests
                'phone' => $request->phone,
                'type' => $request->type,
            ]);

            if ($request->has('addresses')) {
                foreach ($request->addresses as $addressData) {
                    if (!empty($addressData['address'])) {
                        $customer->addresses()->create($addressData);
                    }
                }
            }
        });

        return redirect()->route('customer.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        $customer->load('addresses');
        $user = $customer->user_id ? User::find($customer->user_id) : null;
        return view('admin.customer.edit', compact('customer', 'user'));
    }

       public function show(Customer $customer)
    {
        // Eager load all necessary relationships
        $customer->load('addresses', 'orders');
        $user = $customer->user_id ? User::find($customer->user_id) : null;

        // Calculate statistics
        $totalOrders = $customer->orders->count();
        $pendingOrders = $customer->orders->where('status', 'pending')->count();
        $totalBuyAmount = $customer->orders->where('payment_status', 'paid')->sum('total_amount');

        // Get sales data grouped by month for the last 12 months
        $salesData = $customer->orders()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('total_sales', 'month');

        // Prepare a list of the last 12 months to ensure all months are present
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->put(now()->subMonths($i)->format('Y-m'), 0);
        }

        // Merge the actual sales data with the list of all months
        $monthlyTotals = $months->merge($salesData);

        // Format the data for Google Charts
        $chartData = [['Month', 'Amount']];
        foreach ($monthlyTotals as $month => $total) {
            $chartData[] = [date('M', strtotime($month . '-01')), $total];
        }

        return view('admin.customer.show', compact(
            'customer', 
            'user', 
            'totalOrders', 
            'pendingOrders', 
            'totalBuyAmount',
            'chartData'
        ));
    }


    public function update(Request $request, Customer $customer)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:customers,phone,' . $customer->id],
            'type' => ['required', 'string', 'in:normal,silver,platinum'],
            'addresses' => ['nullable', 'array'],
        ];

        if ($customer->user_id || $request->boolean('create_login_account')) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $customer->user_id];
            $rules['password'] = ['nullable', 'confirmed', Rules\Password::defaults()];
        } else {
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:customers,email,' . $customer->id];
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $customer) {
            $userId = $customer->user_id;

            // Scenario: Create a login for an existing customer who doesn't have one
            if (!$userId && $request->boolean('create_login_account')) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'user_type' => 2,
                    'status' => 1,
                    'password' => $request->password,
                ]);
                $userId = $user->id;
            } 
            // Scenario: Update an existing login
            else if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $userData = ['name' => $request->name, 'email' => $request->email];
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
                'type' => $request->type,
            ]);

            $customer->addresses()->delete();
            if ($request->has('addresses')) {
                foreach ($request->addresses as $addressData) {
                    if (!empty($addressData['address'])) {
                        $customer->addresses()->create($addressData);
                    }
                }
            }
        });

        return redirect()->route('customer.index')->with('success', 'Customer updated successfully.');
    }
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully.']);
    }
}
