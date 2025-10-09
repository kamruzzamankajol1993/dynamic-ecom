<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait StockManagementTrait
{
    /**
     * Adjusts the stock for all items in an order.
     *
     * @param Order $order The order being processed.
     * @param string $operation The operation to perform: 'deduct' or 'add'.
     * @throws \Exception
     */
    protected function adjustStockForOrder(Order $order, string $operation)
    {
        foreach ($order->orderDetails as $detail) {
            try {
                // Find Size ID from the name stored in order detail
                $size = Size::where('name', $detail->size)->first();
                if (!$size) {
                    Log::warning("Stock adjustment skipped: Size '{$detail->size}' not found for OrderDetail ID {$detail->id}.");
                    continue;
                }

                // Find Color ID from the name stored in order detail
                $color = Color::where('name', $detail->color)->first();
                if (!$color) {
                    Log::warning("Stock adjustment skipped: Color '{$detail->color}' not found for OrderDetail ID {$detail->id}.");
                    continue;
                }

                // Find the specific Product Variant
                $variant = ProductVariant::where('product_id', $detail->product_id)
                                         ->where('color_id', $color->id)
                                         ->first();

                if (!$variant) {
                    Log::warning("Stock adjustment skipped: Variant not found for Product ID {$detail->product_id} and Color '{$detail->color}'.");
                    continue;
                }

                $sizesArray = $variant->sizes;
                $sizeFoundInVariant = false;
                $previousQuantity = 0;
                $newQuantity = 0;

                // Loop through the sizes array to find and update the correct size
                foreach ($sizesArray as $key => &$sizeEntry) {
                    // ▼▼▼ THIS IS THE CORRECTED LINE ▼▼▼
                    if (isset($sizeEntry['size_id']) && (int)$sizeEntry['size_id'] == (int)$size->id) {
                    // ▲▲▲ THIS IS THE CORRECTED LINE ▲▲▲
                        
                        $previousQuantity = $sizeEntry['quantity'];

                        // Ensure quantity is treated as a number before calculation
                        if ($operation === 'deduct') {
                            $sizeEntry['quantity'] = (int)$sizeEntry['quantity'] - $detail->quantity;
                        } else { // 'add'
                            $sizeEntry['quantity'] = (int)$sizeEntry['quantity'] + $detail->quantity;
                        }

                        $newQuantity = $sizeEntry['quantity'];
                        $sizeFoundInVariant = true;
                        break;
                    }
                }

                if ($sizeFoundInVariant) {
                    $variant->sizes = $sizesArray;
                    $variant->save();

                    // Log the stock history for tracking
                    StockHistory::create([
                        'product_id' => $detail->product_id,
                        'product_variant_id' => $variant->id,
                        'size_id' => $size->id,
                        'previous_quantity' => $previousQuantity,
                        'new_quantity' => $newQuantity,
                        'quantity_change' => ($operation === 'deduct' ? -1 : 1) * $detail->quantity,
                        'type' => 'order_status_update',
                        'notes' => "Order #{$order->invoice_no} status changed. Stock {$operation}ed.",
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    Log::warning("Stock adjustment skipped: Size ID '{$size->id}' not found in variant's sizes array for Variant ID {$variant->id}.");
                }

            } catch (\Exception $e) {
                Log::error("Error adjusting stock for OrderDetail ID {$detail->id}: " . $e->getMessage());
                // Re-throw the exception to ensure the database transaction is rolled back.
                throw $e;
            }
        }
    }
}