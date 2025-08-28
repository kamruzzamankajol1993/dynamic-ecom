<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SupportQa extends Model
{
     use HasFactory;
    protected $fillable = ['category_id', 'question', 'answer', 'is_faq', 'status'];

    public function category()
    {
        return $this->belongsTo(SupportPageCategory::class, 'category_id');
    }
}
