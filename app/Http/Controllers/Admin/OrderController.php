<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Size;
use App\Models\Color;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use Mpdf\Mpdf;
use Carbon\Carbon;
class OrderController extends Controller
{

    public function searchCustomers(Request $request)
{
    $term = $request->get('term');
    $customers = Customer::where('name', 'LIKE', $term . '%')
                       ->orWhere('phone', 'LIKE', $term . '%')
                       ->limit(10)
                       ->get();
    return response()->json($customers);
}
    public function index()
    {
        // Get counts for each status tab
        $statusCounts = Order::select('status', DB::raw('count(*) as total'))
                             ->groupBy('status')
                             ->pluck('total', 'status');
        
        // Calculate the 'all' count
        $statusCounts['all'] = $statusCounts->sum();

        return view('admin.order.index', compact('statusCounts'));
    }

    public function data(Request $request)
    {
        $query = Order::with('customer');

        // Filter by status tab
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Handle specific filters
        if ($request->filled('order_id')) {
            $query->where('invoice_no', 'like', '%' . $request->order_id . '%');
        }

        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('phone', 'like', '%' . $request->customer_name . '%');
            });
        }
        
        // New: Filter by Product Name or Code
        if ($request->filled('product_info')) {
            $query->whereHas('orderDetails.product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->product_info . '%')
                  ->orWhere('product_code', 'like', '%' . $request->product_info . '%');
            });
        }


        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->latest()->paginate(10);

        return response()->json([
            'data' => $orders->items(),
            'total' => $orders->total(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ]);
    }

     public function create()
    {
        // Generate a unique invoice number
        $newInvoiceId = 'INV-' . strtoupper(uniqid());
        
        // Fetch customers for the dropdown
        $customers = Customer::where('status', 1)->get(['id', 'name', 'phone']);

        return view('admin.order.create', compact('newInvoiceId', 'customers'));
    }

     // AJAX method to get customer details
    public function getCustomerDetails($id)
    {
        $customer = Customer::with('addresses')->findOrFail($id);
        return response()->json([
            'main_address' => $customer->address,
            'addresses' => $customer->addresses,
        ]);
    }

     // AJAX method for product search
    // AJAX method for product search
    public function searchProducts(Request $request)
{
    $term = $request->get('term');
    
    $products = Product::where('name', 'LIKE', $term . '%')
        ->orWhere('product_code', 'LIKE', $term . '%')
        ->limit(10)
        ->get();

    // We need to format the results for the jQuery UI Autocomplete plugin.
    // The frontend expects objects with 'label' and 'value' keys.
    // We also include the 'id' so we can use it when a product is selected.
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id, // We'll need this to fetch details later
            'label' => $product->name . ' (' . $product->product_code . ')', // Text to display in the list
            'value' => $product->name, // Text to place in the input field on select
        ];
    });

    return response()->json($formattedProducts);
}

    public function getProductDetails($id)
    {
        $product = Product::with('variants.color')->findOrFail($id);
        
        $variantsData = $product->variants->map(function ($variant) {
            $sizes = collect($variant->sizes)->map(function ($sizeInfo) {
                $sizeModel = Size::find($sizeInfo['size_id']);
                return [
                    'id' => $sizeInfo['size_id'],
                    'name' => $sizeModel ? $sizeModel->name : 'N/A',
                    'additional_price' => $sizeInfo['additional_price'] ?? 0, 
                ];
            });

            return [
                'variant_id' => $variant->id,
                'color_id' => $variant->color->id,
                'color_name' => $variant->color->name,
                'sizes' => $sizes,
            ];
        });

        return response()->json([
            'base_price' => $product->discount_price ?? $product->base_price,
            'variants' => $variantsData,
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        'invoice_no' => 'required|string|unique:orders,invoice_no',
        'order_date' => 'required|date_format:d-m-Y', // Validate the date field
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($request) {
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'invoice_no' => $request->invoice_no,
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'shipping_cost' => $request->shipping_cost,
            'total_amount' => $request->total_amount,
            'total_pay' => $request->total_pay,
            'cod' => $request->cod,
            'due' => $request->total_amount - $request->total_pay,
            'shipping_address' => $request->shipping_address,
            'payment_term' => $request->payment_term,
            'order_from' => $request->order_from,
            'notes' => $request->notes,
            'status' => 'pending',
            // Save the order_date, converting it for the database
            'order_date' => Carbon::createFromFormat('d-m-Y', $request->order_date)->format('Y-m-d'),
        ]);

        foreach ($request->items as $item) {
            $amount = $item['quantity'] * $item['unit_price'];
            $after_discount = $amount - ($item['discount'] ?? 0);

            $order->orderDetails()->create([
                'product_id' => $item['product_id'],
                'product_variant_id' => null, // Set to null as requested
                'size' => $item['size'],
                'color' => $item['color'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $amount,
                'discount' => $item['discount'] ?? 0,
                'after_discount_price' => $after_discount,
            ]);
        }
    });

    return redirect()->route('order.index')->with('success', 'Order created successfully.');
}

     public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|string']);
        $order->update(['status' => $request->status]);

        
        return response()->json(['message' => 'Order status updated successfully.']);
    }

    /**
     * Fetch details for the order detail modal.
     */
    public function getDetails($id)
    {
        $order = Order::with('customer', 'orderDetails.product')->findOrFail($id);
        return response()->json($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully.']);
    }
    
    /**
     * Destroy multiple orders at once.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Order::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Selected orders have been deleted.']);
    }

    /**
 * Show the form for editing the specified order.
 */
public function edit(Order $order)
{
    // Eager load the relationships to prevent too many database queries in the view
    $order->load('customer', 'orderDetails.product');

    return view('admin.order.edit', compact('order'));
}

/**
 * Update the specified order in storage.
 */
public function update(Request $request, Order $order)
{
    $request->validate([
        'customer_id' => 'required|exists:customers,id',
        // Make sure the invoice number is unique, but ignore the current order's ID
        'invoice_no' => 'required|string|unique:orders,invoice_no,' . $order->id,
        'order_date' => 'required|date_format:d-m-Y',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($request, $order) {
        // 1. Update the main order fields
        $order->update([
            'customer_id' => $request->customer_id,
            'invoice_no' => $request->invoice_no,
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'shipping_cost' => $request->shipping_cost,
            'total_amount' => $request->total_amount,
            'total_pay' => $request->total_pay,
            'cod' => $request->cod,
            'due' => $request->total_amount - $request->total_pay,
            'shipping_address' => $request->shipping_address,
            'payment_term' => $request->payment_term,
            'order_from' => $request->order_from,
            'notes' => $request->notes,
            'status' => $request->status ?? 'pending', // You can add a status dropdown if needed
            'order_date' => Carbon::createFromFormat('d-m-Y', $request->order_date)->format('Y-m-d'),
        ]);

        // 2. Sync the order details. This is the cleanest way to handle changes.
        // It deletes the old items and creates new ones from the submitted form data.
        $order->orderDetails()->delete();

        foreach ($request->items as $item) {
            $amount = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
            $after_discount = $amount - ($item['discount'] ?? 0);

            $order->orderDetails()->create([
                'product_id' => $item['product_id'],
                'product_variant_id' => null,
                'size' => $item['size'],
                'color' => $item['color'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $amount,
                'discount' => $item['discount'] ?? 0,
                'after_discount_price' => $after_discount,
            ]);
        }
    });

    return redirect()->route('order.index')->with('success', 'Order updated successfully.');
}


public function show(Order $order)
{
    $order->load('customer', 'orderDetails.product', 'payments');
    $companyInfo = DB::table('system_information')->first(); // Fetch company info
    return view('admin.order.show', compact('order', 'companyInfo'));
}

// ...

/**
 * Generate and stream an A4 PDF invoice.
 */
public function printA4(Order $order)
{
    $order->load('customer', 'orderDetails.product', 'payments');
    $companyInfo = DB::table('system_information')->first(); // Fetch company info
    $pdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
    $html = view('admin.order.print_a4', compact('order', 'companyInfo'))->render();
    $pdf->WriteHTML($html);
    return $pdf->Output('invoice-'.$order->invoice_no.'.pdf', 'I');
}

/**
 * Generate and stream a POS receipt PDF.
 */
public function printPOS(Order $order)
{
    $order->load('customer', 'orderDetails.product', 'payments');
    $companyInfo = DB::table('system_information')->first(); // Fetch company info
    $pdf = new Mpdf(['mode' => 'utf-8', 'format' => [80, 250]]); // Adjusted height for more content
    $html = view('admin.order.print_pos', compact('order', 'companyInfo'))->render();
    $pdf->WriteHTML($html);
    return $pdf->Output('receipt-'.$order->invoice_no.'.pdf', 'I');
}

/**
     * Store a new payment for an order.
     */
    public function storePayment(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $order->due,
            'payment_date' => 'required|date_format:d-m-Y',
            'payment_method' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $order) {
            $order->payments()->create([
                'amount' => $request->amount,
                'payment_date' => Carbon::createFromFormat('d-m-Y', $request->payment_date)->format('Y-m-d'),
                'payment_method' => $request->payment_method,
                'note' => $request->note,
            ]);

            // Update the order's payment status
            $order->total_pay += $request->amount;
            $order->due -= $request->amount;
            $order->save();
        });

        return redirect()->route('order.show', $order->id)->with('success', 'Payment added successfully.');
    }
}
