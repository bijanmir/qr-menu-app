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
        'sharing_settings',
    ];

    protected $casts = [
        'open_hours' => 'array',
        'service_modes' => 'array',
        'table_map' => 'array',
        'printer_config' => 'array',
        'active' => 'boolean',
        'sharing_settings' => 'array',
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

    // Sharing relationships
    public function incomingSharingRequests(): HasMany
    {
        return $this->hasMany(ItemSharingRequest::class, 'target_restaurant_id');
    }

    public function outgoingSharingRequests(): HasMany
    {
        return $this->hasMany(ItemSharingRequest::class, 'source_restaurant_id');
    }

    // Sharing methods
    public function getDefaultSharingSettings(): array
    {
        return [
            'allow_incoming_duplications' => true,
            'allow_incoming_links' => true,
            'allow_outgoing_sharing' => true,
            'auto_approve_sharing' => false,
            'sharing_permissions' => ['same_tenant'],
        ];
    }

    public function getSharingSettings(): array
    {
        return array_merge($this->getDefaultSharingSettings(), $this->sharing_settings ?? []);
    }

    public function allowsIncomingDuplications(): bool
    {
        return $this->getSharingSettings()['allow_incoming_duplications'] ?? true;
    }

    public function allowsIncomingLinks(): bool
    {
        return $this->getSharingSettings()['allow_incoming_links'] ?? true;
    }

    public function allowsOutgoingSharing(): bool
    {
        return $this->getSharingSettings()['allow_outgoing_sharing'] ?? true;
    }

    public function autoApprovesSharing(): bool
    {
        return $this->getSharingSettings()['auto_approve_sharing'] ?? false;
    }

    public function canShareWith(Restaurant $targetRestaurant): bool
    {
        if (!$this->allowsOutgoingSharing() || !$targetRestaurant->allowsIncomingDuplications()) {
            return false;
        }

        $permissions = $this->getSharingSettings()['sharing_permissions'] ?? ['same_tenant'];
        
        // Check same tenant permission
        if (in_array('same_tenant', $permissions) && $this->tenant_id === $targetRestaurant->tenant_id) {
            return true;
        }
        
        // Check public permission
        if (in_array('public', $permissions)) {
            return true;
        }

        return false;
    }
}
