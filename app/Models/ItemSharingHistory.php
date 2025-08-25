<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSharingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_item_id',
        'target_item_id', 
        'source_restaurant_id',
        'target_restaurant_id',
        'action_type',
        'changed_fields',
        'user_id',
    ];

    protected $casts = [
        'changed_fields' => 'array',
    ];

    public function sourceItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'source_item_id');
    }

    public function targetItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'target_item_id');
    }

    public function sourceRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'source_restaurant_id');
    }

    public function targetRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'target_restaurant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
