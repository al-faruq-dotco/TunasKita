<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = ['province', 'city', 'shipping_cost', 'is_active'];

    protected $casts = ['shipping_cost' => 'decimal:2', 'is_active' => 'boolean'];
}