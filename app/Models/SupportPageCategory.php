<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SupportPageCategory extends Model
{
     use HasFactory;
    protected $fillable = ['name', 'icon', 'description', 'status'];

    public function tickets()
    {
        return $this->hasMany(SupportQa::class, 'category_id');
    }
}
