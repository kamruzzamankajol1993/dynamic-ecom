<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['voucher_type', 'voucher_no', 'date', 'reference', 'description', 'created_by'];

    public function entries()
    {
        return $this->hasMany(TransactionEntry::class);
    }
}
