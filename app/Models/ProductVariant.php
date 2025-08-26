<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'color_id',
        'variant_image',
        'main_image',
        'sizes',
        'variant_sku',
        'additional_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sizes' => 'array', // Automatically cast the JSON to an array
    ];

    /**
     * Get the product that this variant belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the color for this variant.
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function bundleOfferProducts()
{
    return $this->hasMany(BundleOfferProduct::class);
}
}
