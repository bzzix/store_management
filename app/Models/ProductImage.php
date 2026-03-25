<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'image_path',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            // If this is set as primary, unset other primary images
            if ($image->is_primary) {
                ProductImage::where('product_id', $image->product_id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            // Set sort order if not provided
            if (is_null($image->sort_order)) {
                $maxOrder = ProductImage::where('product_id', $image->product_id)
                    ->max('sort_order') ?? 0;
                $image->sort_order = $maxOrder + 1;
            }
        });
    }

    /**
     * Get the product that owns the image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include primary images.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get full URL of the image.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
