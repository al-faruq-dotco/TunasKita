<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    protected $fillable = ['order_id', 'recipient_name', 'phone', 'address', 'courier', 'tracking_number', 'shipping_cost', 'status', 'shipped_at', 'delivered_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(ShipmentTracking::class);
    }
}
