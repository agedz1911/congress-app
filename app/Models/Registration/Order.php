<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reg_code',
        'participant_id',
        'total',
        'discount',
        'coupon',
        'status'
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'order_id');
    }

    public function scopeSearch($query, $value)
    {
        if (empty($value)) {
            return $query;
        }
        return $query->where(function ($query) use ($value) {
            $query->where('reg_code', 'like', '%' . $value . '%')
                ->orWhere('status', 'like', '%' . $value . '%')
                ->orWhereHas('participant', function ($q) use ($value) {
                    $q->where('first_name', 'like', '%' . $value . '%')
                        ->orWhere('last_name', 'like', '%' . $value . '%')
                        ->orWhere('country', 'like', '%' . $value . '%');
                });
        });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participant', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
