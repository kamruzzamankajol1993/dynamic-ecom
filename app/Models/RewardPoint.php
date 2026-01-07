<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_id',
        'points',
        'type', // 'earned', 'redeemed', 'refunded', 'expired'
        'meta',
    ];

    /**
     * Get the customer that owns the reward point log.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order associated with the reward point (if any).
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}