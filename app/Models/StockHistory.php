<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'size_id',
        'previous_quantity',
        'new_quantity',
        'quantity_change',
        'type',
        'notes',
        'user_id',
    ];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}