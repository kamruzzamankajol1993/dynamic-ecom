<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
     public function index()
    {
        // The view will now be populated by AJAX
        return view('admin.purchase.index');
    }

    public function data(Request $request)
    {
        $query = Purchase::with('supplier');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('purchase_no', 'like', $searchTerm . '%')
                  ->orWhereHas('supplier', function ($q) use ($searchTerm) {
                      $q->where('company_name', 'like', $searchTerm . '%');
                  });
        }

        $query->orderBy($request->get('sort', 'id'), $request->get('direction', 'desc'));
        $purchases = $query->paginate(10);

        return response()->json([
            'data' => $purchases->items(),
            'total' => $purchases->total(),
            'current_page' => $purchases->currentPage(),
            'last_page' => $purchases->lastPage(),
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::where('status', true)->get();
        $products = Product::where('status', true)->select('id', 'name')->get();
        return view('admin.purchase.create', compact('suppliers', 'products'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.variant_id' => 'required|exists:product_variants,id',
            'products.*.size_id' => 'required|exists:sizes,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_no' => 'PR-' . time(),
                'purchase_date' => $request->purchase_date,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'shipping_cost' => $request->shipping_cost,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->total_amount - $request->paid_amount,
                'payment_status' => ($request->total_amount - $request->paid_amount) == 0 ? 'paid' : 'due',
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->products as $item) {
                $purchase->purchaseDetails()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                ]);

                // Update Stock and create history
                $variant = ProductVariant::find($item['variant_id']);
                $sizes = $variant->sizes;
                $previousQuantity = 0;
                $sizeFound = false;

                foreach ($sizes as $key => $size) {
                    if ($size['size_id'] == $item['size_id']) {
                        $previousQuantity = $size['quantity'];
                        $sizes[$key]['quantity'] += $item['quantity'];
                        $sizeFound = true;
                        break;
                    }
                }
                
                if (!$sizeFound) {
                    $sizes[] = ['size_id' => $item['size_id'], 'quantity' => $item['quantity']];
                }

                $variant->sizes = $sizes;
                $variant->save();

                StockHistory::create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'size_id' => $item['size_id'],
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $previousQuantity + $item['quantity'],
                    'quantity_change' => $item['quantity'],
                    'type' => 'purchase',
                    'notes' => 'Purchase #' . $purchase->purchase_no,
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('admin.purchase.index')->with('success', 'Purchase created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create purchase: ' . $e->getMessage())->withInput();
        }
    }

     public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'purchaseDetails.product', 'purchaseDetails.productVariant.color', 'purchaseDetails.size');
        return view('admin.purchase.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load('purchaseDetails.product', 'purchaseDetails.productVariant.color', 'purchaseDetails.size');
        $suppliers = Supplier::where('status', true)->get();
        $products = Product::where('status', true)->select('id', 'name')->get();
        return view('admin.purchase.edit', compact('purchase', 'suppliers', 'products'));
    }

     public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.variant_id' => 'required|exists:product_variants,id',
            'products.*.size_id' => 'required|exists:sizes,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_cost' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            // Store original details before any changes
            $originalDetails = $purchase->purchaseDetails()->get()->keyBy(function ($item) {
                return $item->product_variant_id . '-' . $item->size_id;
            });

            // Step 1: Revert stock for all original items
            foreach ($originalDetails as $detail) {
                $variant = ProductVariant::find($detail->product_variant_id);
                if ($variant) {
                    $sizes = $variant->sizes;
                    $previousQuantity = 0;
                    foreach ($sizes as $key => $size) {
                        if ($size['size_id'] == $detail->size_id) {
                            $previousQuantity = $size['quantity'];
                            $sizes[$key]['quantity'] -= $detail->quantity;
                            break;
                        }
                    }
                    $variant->sizes = $sizes;
                    $variant->save();

                    StockHistory::create([
                        'product_id' => $detail->product_id,
                        'product_variant_id' => $detail->product_variant_id,
                        'size_id' => $detail->size_id,
                        'previous_quantity' => $previousQuantity,
                        'new_quantity' => $previousQuantity - $detail->quantity,
                        'quantity_change' => -$detail->quantity,
                        'type' => 'purchase_update_revert',
                        'notes' => 'Reverting Purchase #' . $purchase->purchase_no,
                        'user_id' => Auth::id(),
                    ]);
                }
            }
            
            // Step 2: Delete all old purchase details
            $purchase->purchaseDetails()->delete();

            // Step 3: Add new items and update stock again
            foreach ($request->products as $item) {
                $purchase->purchaseDetails()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                ]);

                $variant = ProductVariant::find($item['variant_id']);
                $sizes = $variant->sizes;
                $previousQuantity = 0;
                $sizeFound = false;

                foreach ($sizes as $key => $size) {
                    if ($size['size_id'] == $item['size_id']) {
                        $previousQuantity = $size['quantity'];
                        $sizes[$key]['quantity'] += $item['quantity'];
                        $sizeFound = true;
                        break;
                    }
                }
                if (!$sizeFound) { $sizes[] = ['size_id' => $item['size_id'], 'quantity' => $item['quantity']]; }
                
                $variant->sizes = $sizes;
                $variant->save();

                StockHistory::create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'size_id' => $item['size_id'],
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $previousQuantity + $item['quantity'],
                    'quantity_change' => $item['quantity'],
                    'type' => 'purchase_update',
                    'notes' => 'Updating Purchase #' . $purchase->purchase_no,
                    'user_id' => Auth::id(),
                ]);
            }

            // Step 4: Update the main purchase record
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'shipping_cost' => $request->shipping_cost,
                'total_amount' => $request->total_amount,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->total_amount - $request->paid_amount,
                'payment_status' => ($request->total_amount - $request->paid_amount) <= 0 ? 'paid' : 'due',
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('admin.purchase.index')->with('success', 'Purchase updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Purchase $purchase)
    {
        
        try {
            DB::beginTransaction();
            
            // Re-add stock (important for inventory accuracy)
            foreach($purchase->purchaseDetails as $detail) {
                $variant = ProductVariant::find($detail->product_variant_id);
                if ($variant) {
                    $sizes = $variant->sizes;
                    $sizeFound = false;
                    foreach ($sizes as $key => $size) {
                        if ($size['size_id'] == $detail->size_id) {
                            $sizes[$key]['quantity'] -= $detail->quantity;
                            $sizeFound = true;
                            break;
                        }
                    }
                    if($sizeFound){
                        $variant->sizes = $sizes;
                        $variant->save();
                    }
                }
            }

            $purchase->delete();
            DB::commit();
            return response()->json(['message' => 'Purchase deleted and stock reverted successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete purchase: ' . $e->getMessage()], 500);
        }
    }
}