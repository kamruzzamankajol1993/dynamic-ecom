<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'delivery_type',
        'invoice_no',
        'subtotal',
        'shipping_cost',
        'discount',
        'total_amount',
        'total_pay',
        'due',
        'cod',
        'old_id',
        'status',
        'shipping_address',
        'billing_address',
        'payment_method',
        'payment_status',
        'payment_term',
        'order_from',
        'trxID',
        'reward_point_discount',	
        'statusMessage',
        'notes',
        'discount_type',  // নতুন
    'discount_value', // নতুন
    'custom_name',   // নতুন যুক্ত করা হয়েছে
    'custom_number', // নতুন যুক্ত করা হয়েছে
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all of the details for the order.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments()
{
    return $this->hasMany(Payment::class);
}

/**
     * Get the tracking history for the order.
     */
    public function trackingHistory()
    {
        return $this->hasMany(OrderTracking::class)->orderBy('created_at', 'asc');
    }
}
