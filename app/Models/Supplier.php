<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'vat_number',
        'status',
    ];

    // Assuming you will have a Purchase model later
    public function purchases()
    {
        // This relationship can be defined when you create the Purchase model
        // return $this->hasMany(Purchase::class);
    }
}