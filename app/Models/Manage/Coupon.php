<?php

namespace App\Models\Manage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nominal',
        'type',
        'starts_at',
        'ends_at',
        'quota',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'quota' => 'integer',
        'used_count' => 'integer',
    ];

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        return true;
    }

    public function isQuotaAvailable(): bool
    {
        // null atau 0 = unlimited
        if (empty($this->quota)) return true;
        return $this->used_count < $this->quota;
    }

    public function remainingQuota(): ?int
    {
        if (empty($this->quota)) return null; // unlimited
        return max(0, $this->quota - $this->used_count);
    }

    public function computeDiscountForSubtotal(float $subtotal): float
    {
        if ($subtotal <= 0) return 0.0;

        if ($this->type === 'percent') {
            $discount = round($subtotal * ((float) $this->nominal / 100), 2);
        } else {
            $discount = (float) $this->nominal;
        }

        return (float) min($discount, $subtotal);
    }
}
