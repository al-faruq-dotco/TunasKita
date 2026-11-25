<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class ProductImage extends Model {
 protected $fillable=['product_id','image_path','is_primary'];
 protected $casts=['is_primary'=>'boolean'];
 public function product(){return $this->belongsTo(Product::class);}
 // URL publik gambar. Jika file fisik hilang, tampilkan placeholder (bukan ikon rusak).
 public function getUrlAttribute(): string {
  return Storage::disk('public')->exists($this->image_path)
   ? asset('storage/'.ltrim($this->image_path,'/'))
   : asset(Product::PLACEHOLDER_IMAGE);
 }
}
