<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'discount_price',
        'offer_start_date',
        'offer_end_date',
        'status',
    ];

    /**
     * Get the product associated with the offer.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
