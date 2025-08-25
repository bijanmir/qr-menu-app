<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ItemSharingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_item_id',
        'source_restaurant_id',
        'target_restaurant_id',
        'requester_id',
        'sharing_type',
        'status',
        'message',
        'rejection_reason',
        'expires_at',
        'responded_at',
        'responder_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    protected static function booted()
    {
        // Set default expiration when creating a request
        static::creating(function ($request) {
            if (!$request->expires_at) {
                $request->expires_at = now()->addDays(7); // Expire in 7 days
            }
        });
    }

    // Relationships
    public function sourceItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'source_item_id');
    }

    public function sourceRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'source_restaurant_id');
    }

    public function targetRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'target_restaurant_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '<=', now());
    }

    public function scopeForRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where('target_restaurant_id', $restaurantId);
    }

    // Methods
    public function approve(User $responder): bool
    {
        if ($this->status !== 'pending' || $this->isExpired()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'responder_id' => $responder->id,
            'responded_at' => now(),
        ]);

        return true;
    }

    public function reject(User $responder, string $reason = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'responder_id' => $responder->id,
            'responded_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function markExpired(): void
    {
        if ($this->status === 'pending' && $this->isExpired()) {
            $this->update(['status' => 'expired']);
        }
    }
}
