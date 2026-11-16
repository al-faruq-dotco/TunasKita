<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
 protected $fillable=['category_id','seller_id','name','slug','description','price','stock','plant_age','plant_size','availability','status'];
 protected $casts=['price'=>'decimal:2','stock'=>'integer'];
 public function category(){return $this->belongsTo(Category::class);}
 public function images(){return $this->hasMany(ProductImage::class);}
 public function primaryImage(){return $this->hasOne(ProductImage::class)->where('is_primary',true);}
}
