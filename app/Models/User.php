<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'address', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi Customer
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    // Relasi Seller
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    // Relasi Negosiasi
    public function customerNegotiations(): HasMany
    {
        return $this->hasMany(Negotiation::class, 'customer_id');
    }

    public function sellerNegotiations(): HasMany
    {
        return $this->hasMany(Negotiation::class, 'seller_id');
    }

    // Relasi Activity Log
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}