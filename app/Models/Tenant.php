<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'billing_account_id',
        'plan',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
