<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
         'start_date',
        'min_amount',
        'user_type',
        'product_ids',
        'category_ids',
        'usage_limit',
        'times_used',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'category_ids' => 'array',
        'start_date' => 'date',
        'expires_at' => 'date',
        'status' => 'boolean',
    ];
}
