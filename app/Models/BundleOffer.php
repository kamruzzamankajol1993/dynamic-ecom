<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class BundleOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'title',
        'status',
        'is_free_delivery',
        'image',
        'startdate',
        'enddate',
    ];
protected $casts = [
        'startdate' => 'datetime',
        'enddate' => 'datetime',
        'is_free_delivery' => 'boolean',
    ];
    /**
     * Get the product sets for the bundle offer.
     */
    public function bundleOfferProducts()
    {
        return $this->hasMany(BundleOfferProduct::class);
    }

     // Automatically create a slug from the name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                 $category->slug = Str::slug($category->name);
            }
        });
    }
}
