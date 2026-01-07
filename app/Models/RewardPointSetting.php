<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Optional but good practice
use Illuminate\Database\Eloquent\Model;

class RewardPointSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'earn_points_per_unit',
        'earn_per_unit_amount',
        'redeem_points_per_unit',
        'redeem_per_unit_amount',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'earn_per_unit_amount' => 'decimal:2',
        'redeem_per_unit_amount' => 'decimal:2',
    ];
}