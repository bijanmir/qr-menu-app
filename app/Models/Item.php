<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'category_id',
        'name',
        'description',
        'price',
        'currency',
        'sku',
        'image',
        'allergens',
        'tags',
        'calories',
        'visible',
        'available',
        'tax_code',
        'prep_station',
        'sort_index',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'allergens' => 'array',
        'tags' => 'array',
        'visible' => 'boolean',
        'available' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modifierGroups(): HasMany
    {
        return $this->hasMany(ModifierGroup::class)->orderBy('sort_index');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visible', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('available', true);
    }

    public function averageRating(): float
    {
        return $this->ratings()->where('verified', true)->avg('stars') ?? 0;
    }

    public function totalRatings(): int
    {
        return $this->ratings()->where('verified', true)->count();
    }

    public function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => '$' . number_format($this->price, 2)
        );
    }

    // Alias for formatted_price (snake_case access)
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    // Check if item is 86'd (unavailable)
    public function getIs86edAttribute()
    {
        return !$this->available;
    }
}
