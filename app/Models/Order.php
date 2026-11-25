<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
 protected $fillable=['user_id','order_code','total_price','status','notes'];
 protected $casts=['total_price'=>'decimal:2'];
 public function user(){return $this->belongsTo(User::class);}
 public function items(){return $this->hasMany(OrderItem::class);}
 public function shipment(){return $this->hasOne(Shipment::class);}
 public function payment(){return $this->hasOne(Payment::class);}
 // Pesanan yang memuat minimal satu produk milik seller tertentu.
 public function scopeForSeller($query,int $sellerId){return $query->whereHas('items.product',fn($q)=>$q->where('seller_id',$sellerId));}
}
