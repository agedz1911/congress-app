<?php

namespace App\Filament\Resources\Accommodation\BookingResource\Schemas;

use App\Models\Accommodation\HotelRoom;
use App\Models\Registration\Participant;
use Illuminate\Support\Str;

class BookPrice
{
    public static function getRoomUnitBasePrice(HotelRoom $room, Participant $participant)
    {
        $country = (string) ($participant->country ?? '');
        $isIndonesia = Str::of($country)->lower()->contains('indonesia');

        if ($isIndonesia) {
            return $room->price_idr;
        }

        return ($room->price_usd ?? $room->price_idr);
    }
}
