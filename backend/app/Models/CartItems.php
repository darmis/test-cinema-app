<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItems extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemsFactory> */
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'seat_id',
        'schedule_id',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}
