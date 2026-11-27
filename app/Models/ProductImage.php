<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_path', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // URL publik gambar:
    // - URL penuh (mis. dari seeder) dipakai apa adanya,
    // - file di disk "public" dilayani lewat /storage,
    // - jika file fisik hilang, tampilkan placeholder (bukan ikon rusak).
    public function getUrlAttribute(): string
    {
        $path = (string) $this->image_path;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->exists($path)
            ? asset('storage/'.ltrim($path, '/'))
            : asset(Product::PLACEHOLDER_IMAGE);
    }
}
