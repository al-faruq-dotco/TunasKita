<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const PLACEHOLDER_IMAGE = 'images/placeholder-bibit.svg';
    protected $fillable            = ['category_id', 'seller_id', 'name', 'slug', 'description', 'price', 'stock', 'plant_age', 'plant_size', 'availability', 'status'];
    protected $casts               = ['price' => 'decimal:2', 'stock' => 'integer'];
    public function category()
    {return $this->belongsTo(Category::class);}
    public function seller()
    {return $this->belongsTo(User::class, 'seller_id');}
    public function images()
    {return $this->hasMany(ProductImage::class);}
    // Gambar utama; jika belum ada yang ditandai utama, pakai gambar pertama agar tidak kosong.
    public function primaryImage()
    {return $this->hasOne(ProductImage::class)->orderByDesc('is_primary')->orderBy('id');}
    // URL siap pakai untuk <img>. Selalu string (ada fallback placeholder).
    public function getThumbnailUrlAttribute()
{
    $image = $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
    if (!$image) {
        return 'https://images.unsplash.com/photo-1537640538966-79f369143f8f?auto=format&fit=crop&w=800&q=80';
    }
    return str_starts_with($image->image_path, 'http')
        ? $image->image_path
        : asset($image->image_path);
}
}
