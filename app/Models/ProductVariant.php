<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color_id',
        'variant_image',
        'main_image',
        'sizes',
        'variant_sku',
        'additional_price',
    ];

    protected $casts = [
        'sizes' => 'array',
    ];

    protected $appends = ['detailed_sizes'];

    /**
     * Accessor for detailed sizes.
     * This method fetches the names for the size IDs stored in the `sizes` JSON column.
     *
     * @return array
     */
    public function getDetailedSizesAttribute(): array
    {
        $sizesData = $this->sizes;
        if (empty($sizesData) || !is_array($sizesData)) {
            return [];
        }

        // === CHANGE #1: Use 'size_id' to get the IDs ===
        $sizeIds = array_column($sizesData, 'size_id');
        if (empty($sizeIds)) {
            return [];
        }

        $sizesMasterList = Size::whereIn('id', $sizeIds)->get()->keyBy('id');
        
        $detailedSizes = [];
        foreach ($sizesData as $sizeEntry) {
            // === CHANGE #2: Check for 'size_id' in the entry ===
            if (isset($sizeEntry['size_id']) && $sizesMasterList->has($sizeEntry['size_id'])) {
                // === CHANGE #3: Use 'size_id' to look up the name ===
                $detailedSizes[] = [
                    'id'       => $sizeEntry['size_id'], // Output 'id' for JS consistency
                    'name'     => $sizesMasterList[$sizeEntry['size_id']]->name,
                    'quantity' => $sizeEntry['quantity'],
                ];
            }
        }

        return $detailedSizes;
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function bundleOfferProducts()
    {
        return $this->hasMany(BundleOfferProduct::class);
    }
}