<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'linked_menu_id',
        'source_menu_id',
        'propagation_mode',
        'override_fields',
        'last_synced_at',
    ];

    protected $casts = [
        'override_fields' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function linkedMenu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'linked_menu_id');
    }

    public function sourceMenu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'source_menu_id');
    }
}
