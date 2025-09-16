<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ShareholderDeposit extends Model
{
    use HasFactory;

    protected $fillable = ['shareholder_id','transaction_id', 'amount', 'date', 'note','cash_account_id','equity_account_id'];

    public function shareholder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shareholder_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
