<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'original_price',
        'image',
        'visible',
        'available',
        'is_86ed',
        'is_popular',
        'is_featured',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'is_spicy',
        'tags',
        'allergens',
        'sort_index',
        // Sharing functionality
        'sharing_type',
        'source_item_id',
        'source_restaurant_id',
        'allow_price_override',
        'allow_description_override',
        'allow_image_override',
        'last_synced_at',
        'sync_settings',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'visible' => 'boolean',
        'available' => 'boolean',
        'is_86ed' => 'boolean',
        'is_popular' => 'boolean',
        'is_featured' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'is_spicy' => 'boolean',
        'tags' => 'array',
        'allergens' => 'array',
        // Sharing functionality casts
        'allow_price_override' => 'boolean',
        'allow_description_override' => 'boolean',
        'allow_image_override' => 'boolean',
        'last_synced_at' => 'datetime',
        'sync_settings' => 'array',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modifierGroups(): HasMany
    {
        return $this->hasMany(ModifierGroup::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Sharing relationships
    public function sourceItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'source_item_id');
    }

    public function sourceRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'source_restaurant_id');
    }

    public function copiedItems(): HasMany
    {
        return $this->hasMany(Item::class, 'source_item_id');
    }

    public function sharingHistory(): HasMany
    {
        return $this->hasMany(ItemSharingHistory::class, 'source_item_id');
    }

    public function targetSharingHistory(): HasMany
    {
        return $this->hasMany(ItemSharingHistory::class, 'target_item_id');
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->is_on_sale) {
            return null;
        }
        
        return round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    // Methods for the premium item card
    public function hasModifiers(): bool
    {
        return $this->modifierGroups()->count() > 0;
    }

    public function totalRatings(): int
    {
        return $this->ratings()->where('verified', true)->count();
    }

    public function averageRating(): float
    {
        $ratings = $this->ratings()->where('verified', true);
        
        if ($ratings->count() === 0) {
            return 0;
        }
        
        return round($ratings->avg('rating'), 1);
    }

    public function isAvailable(): bool
    {
        return $this->available && !$this->is_86ed && $this->visible;
    }

    public function hasAllergens(): bool
    {
        return !empty($this->allergens);
    }

    public function hasDietaryRestrictions(): bool
    {
        return $this->is_vegetarian || $this->is_vegan || $this->is_gluten_free;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('available', true)
                    ->where('visible', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeWithModifiers($query)
    {
        return $query->whereHas('modifierGroups');
    }

    public function scopeWithoutModifiers($query)
    {
        return $query->whereDoesntHave('modifierGroups');
    }

    // Sharing scopes
    public function scopeOriginal($query)
    {
        return $query->where('sharing_type', 'original');
    }

    public function scopeDuplicated($query)
    {
        return $query->where('sharing_type', 'duplicated');
    }

    public function scopeLinked($query)
    {
        return $query->where('sharing_type', 'linked');
    }

    public function scopeSharedFrom($query, $restaurantId)
    {
        return $query->where('source_restaurant_id', $restaurantId);
    }

    // Sharing methods
    public function isOriginal(): bool
    {
        return $this->sharing_type === 'original';
    }

    public function isDuplicated(): bool
    {
        return $this->sharing_type === 'duplicated';
    }

    public function isLinked(): bool
    {
        return $this->sharing_type === 'linked';
    }

    public function isShared(): bool
    {
        return in_array($this->sharing_type, ['duplicated', 'linked']);
    }

    public function canOverrideField(string $field): bool
    {
        if ($this->isOriginal()) {
            return true;
        }

        return match ($field) {
            'price' => $this->allow_price_override,
            'description' => $this->allow_description_override,
            'image' => $this->allow_image_override,
            default => true, // Allow other fields by default
        };
    }

    public function getSyncableFields(): array
    {
        $defaultFields = ['name', 'allergens', 'tags', 'calories', 'visible', 'available'];
        
        if (!$this->canOverrideField('price')) {
            $defaultFields[] = 'price';
        }
        
        if (!$this->canOverrideField('description')) {
            $defaultFields[] = 'description';
        }
        
        if (!$this->canOverrideField('image')) {
            $defaultFields[] = 'image';
        }

        return array_merge($defaultFields, $this->sync_settings['additional_fields'] ?? []);
    }

    public function needsSync(): bool
    {
        if (!$this->isLinked() || !$this->sourceItem) {
            return false;
        }

        return $this->sourceItem->updated_at > ($this->last_synced_at ?? $this->created_at);
    }
}
