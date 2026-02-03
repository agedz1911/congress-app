<?php

namespace App\Models\Accommodation;

use App\Models\Registration\Participant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code',
        'hotel_id',
        'room_id',
        'participant_id',
        'check_in_date',
        'check_out_date',
        'total_night',
        'coupon',
        'discount',
        'subtotal',
        'total',
        'status'
    ];

    public function scopeSearch($query, $value)
    {
        if (empty($value)) {
            return $query;
        }
        return $query->where(function ($query) use ($value) {
            $query->where('booking_code', 'like', '%' . $value . '%')
                ->orWhere('status', 'like', '%' . $value . '%')
                ->orWhereHas('participant', function ($q) use ($value) {
                    $q->where('first_name', 'like', '%' . $value . '%')
                        ->orWhere('last_name', 'like', '%' . $value . '%')
                        ->orWhere('country', 'like', '%' . $value . '%');
                });
        });
        // $query->where(['hotel_id'], 'like', "%{$value}%");
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participant', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function room()
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function bookingTransaction()
    {
        return $this->hasOne(BookingTransaction::class, 'booking_id');
    }
}
