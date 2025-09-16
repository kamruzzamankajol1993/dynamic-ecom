<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'amount',
        'type',
        'as_of_date'
    ];
    
    protected $casts = [
        'as_of_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}