<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public const CATEGORIES = [
        'electronics' => 'Electronics',
        'clothing' => 'Clothing',
        'books' => 'Books',
        'home' => 'Home & Kitchen',
        'toys' => 'Toys',
    ];

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'price_cents',
        'stock',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Editing surfaces work in pounds while the column stays in minor units.
     * round() before casting to int matters: (int) (19.99 * 100) is 1998, not
     * 1999, because 19.99 has no exact binary representation.
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): float => ($attributes['price_cents'] ?? 0) / 100,
            set: fn (float|string $value): array => ['price_cents' => (int) round((float) $value * 100)],
        );
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }
}
