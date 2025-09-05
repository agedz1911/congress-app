<?php

namespace App\Models\Accommodation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'payment_date',
        'payment_status',
        'amount',
        'attachment',
        'kurs',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
