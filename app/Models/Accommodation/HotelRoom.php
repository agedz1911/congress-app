<?php

namespace App\Models\Accommodation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'room_type',
        'price_idr',
        'price_usd',
        'image',
        'description',
        'quota',
        'used_count',
        'is_active'
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
