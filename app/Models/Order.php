<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'number',
        'status',
        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'grand_total',
        'billing_address_id',
        'shipping_address_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'integer',
        'discount_total' => 'integer',
        'tax_total' => 'integer',
        'shipping_total' => 'integer',
        'grand_total' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->number)) {
                $order->number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate a unique order number.
     */
    protected static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(Str::random(8));
        } while (static::where('number', $number)->exists());

        return $number;
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the billing address.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /**
     * Get the shipping address.
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Get the payments for the order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal / 100, 2);
    }

    /**
     * Get the formatted grand total.
     */
    public function getFormattedGrandTotalAttribute(): string
    {
        return number_format($this->grand_total / 100, 2);
    }

    /**
     * Get the formatted discount total.
     */
    public function getFormattedDiscountTotalAttribute(): string
    {
        return number_format($this->discount_total / 100, 2);
    }

    /**
     * Get the formatted tax total.
     */
    public function getFormattedTaxTotalAttribute(): string
    {
        return number_format($this->tax_total / 100, 2);
    }

    /**
     * Get the formatted shipping total.
     */
    public function getFormattedShippingTotalAttribute(): string
    {
        return number_format($this->shipping_total / 100, 2);
    }

    /**
     * Check if the order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the order is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if the order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the order is shipped.
     */
    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    /**
     * Mark the order as paid.
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Mark the order as cancelled.
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Mark the order as shipped.
     */
    public function markAsShipped(): void
    {
        $this->update(['status' => 'shipped']);
    }

    /**
     * Get the total number of items in the order.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('qty');
    }
}

