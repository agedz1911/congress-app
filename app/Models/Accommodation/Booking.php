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
        'total',
        'status'
    ];

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
