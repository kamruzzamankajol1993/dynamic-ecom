<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_variant_id',
        'size_id',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}