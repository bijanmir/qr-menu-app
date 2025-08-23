<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'restaurant_id',
        'table_id',
        'customer_id',
        'status',
        'channel',
        'subtotal',
        'taxes',
        'service_fees',
        'tips',
        'total',
        'currency',
        'payment_status',
        'notes',
        'customer_info',
        'accepted_at',
        'ready_at',
        'served_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'taxes' => 'decimal:2',
        'service_fees' => 'decimal:2',
        'tips' => 'decimal:2',
        'total' => 'decimal:2',
        'customer_info' => 'array',
        'accepted_at' => 'datetime',
        'ready_at' => 'datetime',
        'served_at' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeForRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    protected static function booted()
    {
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }
}
