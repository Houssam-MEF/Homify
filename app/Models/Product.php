<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_amount',
        'price_currency',
        'stock',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_amount' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the product images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort');
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the formatted price.
     */
    public function price(): string
    {
        return number_format($this->price_amount / 100, 2);
    }

    /**
     * Get the price in dollars (for display).
     */
    public function getPriceAttribute(): string
    {
        return $this->price();
    }

    /**
     * Check if the product is in stock.
     */
    public function inStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Check if the product is available for purchase.
     */
    public function isAvailable(int $quantity = 1): bool
    {
        return $this->is_active && $this->inStock($quantity);
    }

    /**
     * Get the main product image.
     */
    public function getMainImageAttribute(): ?ProductImage
    {
        return $this->images->first();
    }

    /**
     * Decrease stock quantity.
     */
    public function decreaseStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }

    /**
     * Increase stock quantity.
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }
}

