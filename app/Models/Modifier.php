<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'modifier_group_id',
        'name',
        'price_adjustment',
        'available',
        'sort_index',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'available' => 'boolean',
    ];

    public function modifierGroup(): BelongsTo
    {
        return $this->belongsTo(ModifierGroup::class);
    }
}
