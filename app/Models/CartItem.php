<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'qty',
        'unit_amount',
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'qty' => 'integer',
        'unit_amount' => 'integer',
    ];

    /**
     * Get the cart that owns the item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the total for this item in minor units.
     */
    public function getTotalAttribute(): int
    {
        return $this->unit_amount * $this->qty;
    }

    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_amount / 100, 2);
    }

    /**
     * Get the formatted total.
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total / 100, 2);
    }

    /**
     * Update the quantity.
     */
    public function updateQuantity(int $quantity): void
    {
        $this->update(['qty' => $quantity]);
    }

    /**
     * Increase the quantity.
     */
    public function increaseQuantity(int $amount = 1): void
    {
        $this->increment('qty', $amount);
    }

    /**
     * Decrease the quantity.
     */
    public function decreaseQuantity(int $amount = 1): void
    {
        $newQty = max(0, $this->qty - $amount);
        
        if ($newQty === 0) {
            $this->delete();
        } else {
            $this->update(['qty' => $newQty]);
        }
    }
}

