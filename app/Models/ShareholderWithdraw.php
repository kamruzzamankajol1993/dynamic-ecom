<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareholderWithdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'shareholder_id',
        'transaction_id',
        'amount',
        'date',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function shareholder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shareholder_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}