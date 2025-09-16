<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionEntry extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id', 'account_id', 'type', 'amount'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
