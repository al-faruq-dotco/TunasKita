<?php
namespace App\Models;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
#[Fillable(['name','email','phone','address','password','role'])]
#[Hidden(['password','remember_token'])]
class User extends Authenticatable {
 use HasFactory, Notifiable;
 protected function casts(): array {return ['email_verified_at'=>'datetime','password'=>'hashed'];}
 public function cart(){return $this->hasOne(Cart::class);}
 public function orders(){return $this->hasMany(Order::class);}
 public function wishlistItems(){return $this->hasMany(WishlistItem::class);}
}
