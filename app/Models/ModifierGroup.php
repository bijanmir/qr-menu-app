<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModifierGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'name',
        'required',
        'min_selection',
        'max_selection',
        'sort_index',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(Modifier::class)->orderBy('sort_index');
    }
}
