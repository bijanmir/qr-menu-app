<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'sort_index',
        'icon',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class)->orderBy('sort_index');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visible', true);
    }
}
