<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'restaurant_id',
        'table_id',
        'items',
        'subtotal',
        'taxes',
        'service_fees',
        'total',
        'expires_at',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'taxes' => 'decimal:2',
        'service_fees' => 'decimal:2',
        'total' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function addItem(array $item): void
    {
        $items = $this->items ?? [];
        $items[] = $item;
        $this->items = $items;
        $this->calculateTotals();
    }

    public function removeItem(int $index): void
    {
        $items = $this->items ?? [];
        unset($items[$index]);
        $this->items = array_values($items);
        $this->calculateTotals();
    }

    public function updateQuantity(int $index, int $quantity): void
    {
        $items = $this->items ?? [];
        if (isset($items[$index])) {
            $items[$index]['quantity'] = $quantity;
            $this->items = $items;
            $this->calculateTotals();
        }
    }

    private function calculateTotals(): void
    {
        $subtotal = 0;
        foreach ($this->items ?? [] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $this->subtotal = $subtotal;
        $this->taxes = $subtotal * 0.08; // 8% tax rate - should be configurable
        $this->service_fees = $subtotal * 0.02; // 2% service fee - should be configurable
        $this->total = $this->subtotal + $this->taxes + $this->service_fees;
    }
}
