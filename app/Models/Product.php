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

    // URL thumbnail produk. Memakai relasi primaryImage (tidak ada query tambahan
    // jika sudah di-eager-load) dan jatuh ke placeholder bila produk belum punya gambar.
    public function getThumbnailUrlAttribute(): string
    {
        return $this->primaryImage?->url ?? asset(self::PLACEHOLDER_IMAGE);
    }

    // Mengecek apakah produk bisa dibeli.
    public function isAvailableForPurchase(): bool
    {
        return $this->status === 'active'
            && $this->availability !== 'out_of_stock'
            && $this->stock > 0;
    }
}