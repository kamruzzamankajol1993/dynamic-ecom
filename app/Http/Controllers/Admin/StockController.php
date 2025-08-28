<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::select('id', 'name')->where('status', true)->get();
        return view('admin.stock.index', compact('products'));
    }

    public function getVariants(Product $product)
    {
        $product->load('variants.color');
        return response()->json($product);
    }

    public function update(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'size_id' => 'required|exists:sizes,id',
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $variant = ProductVariant::findOrFail($request->variant_id);
            $sizes = $variant->sizes;
            $previousQuantity = 0;
            $sizeIndex = null;

            foreach ($sizes as $index => $size) {
                if ($size['size_id'] == $request->size_id) {
                    $previousQuantity = $size['quantity'];
                    $sizeIndex = $index;
                    break;
                }
            }

            if ($sizeIndex === null) {
                return response()->json(['message' => 'Size not found for this product variant.'], 404);
            }

            // Update the quantity in the array
            $sizes[$sizeIndex]['quantity'] = $request->quantity;
            $variant->sizes = $sizes;
            $variant->save();

            // Create a history record
            StockHistory::create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'size_id' => $request->size_id,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $request->quantity,
                'quantity_change' => $request->quantity - $previousQuantity,
                'type' => 'manual_update',
                'notes' => $request->notes,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Stock updated successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update stock: ' . $e->getMessage()], 500);
        }
    }
    
    public function getHistory(ProductVariant $variant, $sizeId)
    {
        $history = StockHistory::where('product_variant_id', $variant->id)
            ->where('size_id', $sizeId)
            ->with('user:id,name') // Only get user's id and name
            ->latest()
            ->get();
            
        return response()->json($history);
    }
}