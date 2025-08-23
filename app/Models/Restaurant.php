<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'timezone',
        'address',
        'open_hours',
        'service_modes',
        'table_map',
        'printer_config',
        'active',
    ];

    protected $casts = [
        'open_hours' => 'array',
        'service_modes' => 'array',
        'table_map' => 'array',
        'printer_config' => 'array',
        'active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function getActiveMenus()
    {
        return $this->menus()
            ->where('status', 'published')
            ->get()
            ->filter(function ($menu) {
                // If no schedule, consider it active
                if (!$menu->schedule) {
                    return true;
                }
                
                // If schedule doesn't have 'active' key, consider it active
                if (!array_key_exists('active', $menu->schedule)) {
                    return true;
                }
                
                // Return the actual 'active' value
                return $menu->schedule['active'] === true;
            });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
