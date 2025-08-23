<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'restaurant_id',
        'menu_scope',
        'name',
        'description',
        'status',
        'schedule',
        'theme',
        'published_at',
        'scheduled_at',
    ];

    protected $casts = [
        'schedule' => 'array',
        'theme' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('sort_index');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function sourceLinks(): HasMany
    {
        return $this->hasMany(MenuLink::class, 'source_menu_id');
    }

    public function linkedFrom(): BelongsTo
    {
        return $this->belongsTo(MenuLink::class, 'id', 'linked_menu_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeForRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function isActive(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        // Check schedule if exists
        if ($this->schedule) {
            // Add schedule logic here
            return true; // Simplified for now
        }

        return true;
    }
}
