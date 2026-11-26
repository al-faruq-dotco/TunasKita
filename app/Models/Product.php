<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    public const PLACEHOLDER_IMAGE = 'images/placeholder-bibit.svg';

    protected $fillable = [
        'category_id',
        'seller_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'plant_age',
        'plant_size',
        'availability',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    // Gambar utama, fallback ke gambar pertama.
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->orderByDesc('is_primary')
            ->orderBy('id');
    }

    // URL thumbnail produk.
    public function getThumbnailUrlAttribute(): string
    {
        $image = $this->images()
            ->where('is_primary', true)
            ->first() ?? $this->images()->first();

        if (!$image) {
            return 'https://images.unsplash.com/photo-1537640538966-79f369143f8f?auto=format&fit=crop&w=800&q=80';
        }

        return str_starts_with($image->image_path, 'http')
            ? $image->image_path
            : asset($image->image_path);
    }

    // Mengecek apakah produk bisa dibeli.
    public function isAvailableForPurchase(): bool
    {
        return $this->status === 'active'
            && $this->availability !== 'out_of_stock'
            && $this->stock > 0;
    }
}