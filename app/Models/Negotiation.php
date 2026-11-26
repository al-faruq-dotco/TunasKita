<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Negotiation extends Model
{
    protected $fillable = ['order_id', 'customer_id', 'seller_id', 'offered_price', 'proposed_price', 'message', 'status'];

    protected $casts = ['offered_price' => 'decimal:2', 'proposed_price' => 'decimal:2'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
