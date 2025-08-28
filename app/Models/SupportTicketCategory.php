<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'icon', 'description', 'status'];

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class, 'category_id');
    }
}