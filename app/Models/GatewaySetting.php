<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewaySetting extends Model
{
    use HasFactory;

    // The table name is automatically inferred as 'gateway_settings' from the model name.

    protected $fillable = [
        'key',
        'value',
    ];
}