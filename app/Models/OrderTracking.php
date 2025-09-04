<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class OrderTracking extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'tracking_number','status','bd_time','bd_date'];

   
}
