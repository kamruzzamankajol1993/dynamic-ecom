<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_no',
        'purchase_date',
        'subtotal',
        'discount',
        'shipping_cost',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'notes',
        'created_by',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}